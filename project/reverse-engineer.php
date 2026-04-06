<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Serinity-friendly reverse engineering script.
 *
 * Improvements over the workshop script:
 * - Reads DATABASE_URL from .env.local / .env
 * - Safer naming conversion (snake_case -> PascalCase / camelCase)
 * - Better type handling (bigint -> string, enum -> string, datetime -> \DateTimeInterface)
 * - Correct owning/inverse side generation for pure many-to-many join tables
 * - Initializes collections in constructor
 * - Generates add/remove methods that sync inverse relations
 * - Supports --dry-run and --only=table1,table2
 *
 * Usage:
 *   php reverse-engineer.php --dry-run
 *   php reverse-engineer.php --only=emotion,influence
 *   php reverse-engineer.php
 */

$options = getopt('', ['dry-run', 'force', 'only::', 'namespace::', 'output-dir::']);

$dryRun = array_key_exists('dry-run', $options);
$force = array_key_exists('force', $options);
$onlyTables = [];

if (!empty($options['only'])) {
    $onlyTables = array_values(array_filter(array_map('trim', explode(',', (string) $options['only']))));
}

$namespace = $options['namespace'] ?? 'App\\Entity';
$outputDir = $options['output-dir'] ?? (__DIR__ . '/src/Entity');

if (!is_dir($outputDir) && !$dryRun) {
    mkdir($outputDir, 0775, true);
}

$config = loadDatabaseConfig(__DIR__);
$pdo = createPdo($config);

echo "Connected to {$config['dbName']} on {$config['host']}:{$config['port']}\n";

$tables = fetchTables($pdo);
if ($onlyTables !== []) {
    $tables = array_values(array_intersect($tables, $onlyTables));
}

if ($tables === []) {
    fwrite(STDERR, "No tables found for reverse engineering.\n");
    exit(1);
}

$tableMeta = [];
foreach ($tables as $table) {
    if (shouldSkipTable($table)) {
        continue;
    }

    $columns = fetchColumns($pdo, $table, $config['dbName']);
    $foreignKeys = fetchForeignKeys($pdo, $table, $config['dbName']);
    $indexes = fetchIndexes($pdo, $table, $config['dbName']);
    $primaryKeyColumns = array_values(array_map(
        fn(array $row) => $row['COLUMN_NAME'],
        array_filter($columns, fn(array $col) => $col['COLUMN_KEY'] === 'PRI')
    ));

    $tableMeta[$table] = [
        'table' => $table,
        'className' => pascalCase($table),
        'columns' => $columns,
        'foreignKeys' => $foreignKeys,
        'indexes' => $indexes,
        'primaryKeyColumns' => $primaryKeyColumns,
        'uniqueColumns' => fetchSingleColumnUniques($indexes),
    ];
}

$manyToMany = detectPureJoinTables($tableMeta);

foreach ($tableMeta as $table => $meta) {
    if (isset($manyToMany[$table])) {
        echo "Join table detected: {$table}\n";
    }
}

foreach ($tableMeta as $table => $meta) {
    if (isset($manyToMany[$table])) {
        continue;
    }

    $code = generateEntityCode($meta, $tableMeta, $manyToMany, $namespace);

    $target = rtrim($outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $meta['className'] . '.php';
    if ($dryRun) {
        echo "\n===== {$meta['className']} ({$table}) =====\n";
        echo $code . "\n";
        continue;
    }

    if (file_exists($target) && !$force) {
        echo "Skipping existing file: {$target} (use --force to overwrite)\n";
        continue;
    }

    file_put_contents($target, $code);
    echo "Generated {$target}\n";
}

echo $dryRun ? "\nDry run complete.\n" : "\nGeneration complete.\n";

function loadDatabaseConfig(string $projectRoot): array
{
    $envFiles = [$projectRoot . '/.env.local', $projectRoot . '/.env'];
    $databaseUrl = null;

    foreach ($envFiles as $file) {
        if (!is_file($file)) {
            continue;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (str_starts_with($line, 'DATABASE_URL=')) {
                $databaseUrl = trim(substr($line, strlen('DATABASE_URL=')));
                $databaseUrl = trim($databaseUrl, "\"'");
            }
        }

        if ($databaseUrl !== null) {
            break;
        }
    }

    if ($databaseUrl === null) {
        throw new RuntimeException('DATABASE_URL not found in .env.local or .env');
    }

    $parts = parse_url($databaseUrl);
    if ($parts === false) {
        throw new RuntimeException('Invalid DATABASE_URL');
    }

    parse_str($parts['query'] ?? '', $query);

    return [
        'scheme' => $parts['scheme'] ?? 'mysql',
        'host' => $parts['host'] ?? '127.0.0.1',
        'port' => (int) ($parts['port'] ?? 3306),
        'dbName' => isset($parts['path']) ? ltrim($parts['path'], '/') : '',
        'user' => urldecode($parts['user'] ?? ''),
        'pass' => urldecode($parts['pass'] ?? ''),
        'charset' => $query['charset'] ?? 'utf8mb4',
    ];
}

function createPdo(array $config): PDO
{
    if (($config['scheme'] ?? 'mysql') !== 'mysql') {
        throw new RuntimeException('Only mysql/mariadb DATABASE_URL is supported by this script.');
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $config['host'],
        $config['port'],
        $config['dbName'],
        $config['charset']
    );

    $pdo = new PDO($dsn, $config['user'], $config['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

function fetchTables(PDO $pdo): array
{
    return $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) ?: [];
}

function shouldSkipTable(string $table): bool
{
    $lower = strtolower($table);

    return str_contains($lower, 'migration')
        || str_contains($lower, 'doctrine')
        || str_starts_with($lower, '_');
}

function fetchColumns(PDO $pdo, string $table, string $dbName): array
{
    $sql = <<<SQL
        SELECT
            c.COLUMN_NAME,
            c.COLUMN_DEFAULT,
            c.IS_NULLABLE,
            c.DATA_TYPE,
            c.COLUMN_TYPE,
            c.CHARACTER_MAXIMUM_LENGTH,
            c.NUMERIC_PRECISION,
            c.NUMERIC_SCALE,
            c.COLUMN_KEY,
            c.EXTRA
        FROM information_schema.COLUMNS c
        WHERE c.TABLE_SCHEMA = :db AND c.TABLE_NAME = :table
        ORDER BY c.ORDINAL_POSITION
    SQL;

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['db' => $dbName, 'table' => $table]);

    return $stmt->fetchAll() ?: [];
}

function fetchForeignKeys(PDO $pdo, string $table, string $dbName): array
{
    $sql = <<<SQL
        SELECT
            kcu.CONSTRAINT_NAME,
            kcu.COLUMN_NAME,
            kcu.REFERENCED_TABLE_NAME,
            kcu.REFERENCED_COLUMN_NAME,
            kcu.ORDINAL_POSITION
        FROM information_schema.KEY_COLUMN_USAGE kcu
        WHERE kcu.TABLE_SCHEMA = :db
          AND kcu.TABLE_NAME = :table
          AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
        ORDER BY kcu.ORDINAL_POSITION
    SQL;

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['db' => $dbName, 'table' => $table]);

    return $stmt->fetchAll() ?: [];
}

function fetchIndexes(PDO $pdo, string $table, string $dbName): array
{
    $sql = <<<SQL
        SELECT
            s.INDEX_NAME,
            s.NON_UNIQUE,
            s.COLUMN_NAME,
            s.SEQ_IN_INDEX
        FROM information_schema.STATISTICS s
        WHERE s.TABLE_SCHEMA = :db
          AND s.TABLE_NAME = :table
        ORDER BY s.INDEX_NAME, s.SEQ_IN_INDEX
    SQL;

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['db' => $dbName, 'table' => $table]);

    return $stmt->fetchAll() ?: [];
}

function fetchSingleColumnUniques(array $indexes): array
{
    $grouped = [];
    foreach ($indexes as $index) {
        if ((int) $index['NON_UNIQUE'] !== 0 || $index['INDEX_NAME'] === 'PRIMARY') {
            continue;
        }
        $grouped[$index['INDEX_NAME']][] = $index['COLUMN_NAME'];
    }

    $singleColumn = [];
    foreach ($grouped as $indexName => $columns) {
        if (count($columns) === 1) {
            $singleColumn[] = $columns[0];
        }
    }

    return array_values(array_unique($singleColumn));
}

function detectPureJoinTables(array $tableMeta): array
{
    $joinTables = [];

    foreach ($tableMeta as $table => $meta) {
        $fkColumns = array_values(array_map(fn(array $fk) => $fk['COLUMN_NAME'], $meta['foreignKeys']));
        $allColumnNames = array_values(array_map(fn(array $col) => $col['COLUMN_NAME'], $meta['columns']));
        $nonFkColumns = array_values(array_diff($allColumnNames, $fkColumns));

        $hasExactlyTwoFks = count($meta['foreignKeys']) === 2;
        $onlyFkColumns = count($nonFkColumns) === 0;
        $pkMatchesFks = count(array_diff($meta['primaryKeyColumns'], $fkColumns)) === 0
            && count(array_diff($fkColumns, $meta['primaryKeyColumns'])) === 0;

        if ($hasExactlyTwoFks && $onlyFkColumns && $pkMatchesFks) {
            $joinTables[$table] = [
                'table' => $table,
                'left' => $meta['foreignKeys'][0],
                'right' => $meta['foreignKeys'][1],
            ];
        }
    }

    return $joinTables;
}


function determineOwningSideTable(string $joinTable, string $leftTable, string $rightTable): string
{
    $joinTableLower = strtolower($joinTable);
    $leftPos = strpos($joinTableLower, strtolower($leftTable));
    $rightPos = strpos($joinTableLower, strtolower($rightTable));

    if ($leftPos !== false && $rightPos !== false) {
        return $leftPos <= $rightPos ? $leftTable : $rightTable;
    }

    if ($leftPos !== false) {
        return $leftTable;
    }

    if ($rightPos !== false) {
        return $rightTable;
    }

    return $leftTable;
}

function generateEntityCode(array $meta, array $tableMeta, array $manyToMany, string $namespace): string
{
    $className = $meta['className'];
    $uses = [
        'Doctrine\\ORM\\Mapping as ORM',
    ];

    $properties = [];
    $methods = [];
    $constructorLines = [];
    $needsCollections = false;

    $fkByColumn = [];
    foreach ($meta['foreignKeys'] as $fk) {
        $fkByColumn[$fk['COLUMN_NAME']] = $fk;
    }

    foreach ($meta['columns'] as $column) {
        $columnName = $column['COLUMN_NAME'];

        if (isset($fkByColumn[$columnName])) {
            $fk = $fkByColumn[$columnName];
            $targetClass = $tableMeta[$fk['REFERENCED_TABLE_NAME']]['className'] ?? pascalCase($fk['REFERENCED_TABLE_NAME']);
            $propertyName = relationPropertyName($columnName, $targetClass);
            $nullable = $column['IS_NULLABLE'] === 'YES' ? 'true' : 'false';
            $inversedBy = camelCase(pluralize($className));

            $properties[] =
                "    #[ORM\\ManyToOne(targetEntity: {$targetClass}::class, inversedBy: '{$inversedBy}')]\n" .
                "    #[ORM\\JoinColumn(name: '{$columnName}', referencedColumnName: '{$fk['REFERENCED_COLUMN_NAME']}', nullable: {$nullable})]\n" .
                "    private ?{$targetClass} \${$propertyName} = null;\n";

            $getter = ucfirst($propertyName);
            $methods[] =
                "    public function get{$getter}(): ?{$targetClass}\n" .
                "    {\n" .
                "        return \$this->{$propertyName};\n" .
                "    }\n";

            $methods[] =
                "    public function set{$getter}(?{$targetClass} \${$propertyName}): static\n" .
                "    {\n" .
                "        \$this->{$propertyName} = \${$propertyName};\n\n" .
                "        return \$this;\n" .
                "    }\n";

            continue;
        }

        [$phpType, $doctrineType, $columnArgs] = mapColumnTypes($column);
        $propertyName = camelCase($columnName);

        $attrLines = [];
        if (in_array($columnName, $meta['primaryKeyColumns'], true)) {
            $attrLines[] = '    #[ORM\Id]';
            if (str_contains($column['EXTRA'] ?? '', 'auto_increment')) {
                $attrLines[] = '    #[ORM\GeneratedValue]';
            }
        }

        $columnOptions = [];
        $columnOptions[] = "type: '{$doctrineType}'";

        if ($column['IS_NULLABLE'] === 'YES') {
            $columnOptions[] = 'nullable: true';
        }

        if (in_array($columnName, $meta['uniqueColumns'], true) && !in_array($columnName, $meta['primaryKeyColumns'], true)) {
            $columnOptions[] = 'unique: true';
        }

        if ($columnArgs !== []) {
            $columnOptions = array_merge($columnOptions, $columnArgs);
        }

        if ($columnName !== $propertyName) {
            $columnOptions[] = "name: '{$columnName}'";
        }

        $attrLines[] = '    #[ORM\Column(' . implode(', ', $columnOptions) . ')]';

        $isAutoGeneratedPrimaryKey = in_array($columnName, $meta['primaryKeyColumns'], true)
            && str_contains($column['EXTRA'] ?? '', 'auto_increment');

        $nullablePhp = $column['IS_NULLABLE'] === 'YES' || in_array($columnName, $meta['primaryKeyColumns'], true);
        $typedProperty = ($nullablePhp ? '?' : '') . $phpType;

        $defaultSuffix = '';
        if ($nullablePhp) {
            $defaultSuffix = ' = null';
        }

        $properties[] = implode("\n", $attrLines) . "\n" .
            "    private {$typedProperty} \${$propertyName}{$defaultSuffix};\n";

        $getterName = getterName($propertyName, $phpType);
        $getterReturn = ($nullablePhp ? '?' : '') . $phpType;
        $methods[] =
            "    public function {$getterName}(): {$getterReturn}\n" .
            "    {\n" .
            "        return \$this->{$propertyName};\n" .
            "    }\n";

        if (!$isAutoGeneratedPrimaryKey) {
            $setterType = ($nullablePhp ? '?' : '') . $phpType;
            $setterName = 'set' . ucfirst($propertyName);
            $methods[] =
                "    public function {$setterName}({$setterType} \${$propertyName}): static\n" .
                "    {\n" .
                "        \$this->{$propertyName} = \${$propertyName};\n\n" .
                "        return \$this;\n" .
                "    }\n";
        }
    }

    // Inverse OneToMany sides
    foreach ($tableMeta as $otherTable => $otherMeta) {
        if ($otherTable === $meta['table'] || isset($manyToMany[$otherTable])) {
            continue;
        }

        foreach ($otherMeta['foreignKeys'] as $otherFk) {
            if ($otherFk['REFERENCED_TABLE_NAME'] !== $meta['table']) {
                continue;
            }

            $otherClass = $otherMeta['className'];
            $mappedBy = relationPropertyName($otherFk['COLUMN_NAME'], $className);
            $propertyName = camelCase(pluralize($otherClass));
            $singularProperty = camelCase($otherClass);

            $uses[] = 'Doctrine\\Common\\Collections\\ArrayCollection';
            $uses[] = 'Doctrine\\Common\\Collections\\Collection';
            $needsCollections = true;
            $constructorLines[] = "        \$this->{$propertyName} = new ArrayCollection();";

            $properties[] =
                "    /**\n" .
                "     * @var Collection<int, {$otherClass}>\n" .
                "     */\n" .
                "    #[ORM\\OneToMany(targetEntity: {$otherClass}::class, mappedBy: '{$mappedBy}')]\n" .
                "    private Collection \${$propertyName};\n";

            $methods[] =
                "    /**\n" .
                "     * @return Collection<int, {$otherClass}>\n" .
                "     */\n" .
                "    public function get" . ucfirst($propertyName) . "(): Collection\n" .
                "    {\n" .
                "        return \$this->{$propertyName};\n" .
                "    }\n";

            $methods[] =
                "    public function add" . ucfirst($singularProperty) . "({$otherClass} \${$singularProperty}): static\n" .
                "    {\n" .
                "        if (!\$this->{$propertyName}->contains(\${$singularProperty})) {\n" .
                "            \$this->{$propertyName}->add(\${$singularProperty});\n" .
                "            \$this->{$singularProperty}->set" . ucfirst($mappedBy) . "(\$this);\n" .
                "        }\n\n" .
                "        return \$this;\n" .
                "    }\n";

            $methods[] =
                "    public function remove" . ucfirst($singularProperty) . "({$otherClass} \${$singularProperty}): static\n" .
                "    {\n" .
                "        if (\$this->{$propertyName}->removeElement(\${$singularProperty})) {\n" .
                "            if (\${$singularProperty}->get" . ucfirst($mappedBy) . "() === \$this) {\n" .
                "                \${$singularProperty}->set" . ucfirst($mappedBy) . "(null);\n" .
                "            }\n" .
                "        }\n\n" .
                "        return \$this;\n" .
                "    }\n";
        }
    }

    // ManyToMany (pure join tables only)
    foreach ($manyToMany as $joinTable => $joinMeta) {
        $leftTable = $joinMeta['left']['REFERENCED_TABLE_NAME'];
        $rightTable = $joinMeta['right']['REFERENCED_TABLE_NAME'];

        if ($meta['table'] !== $leftTable && $meta['table'] !== $rightTable) {
            continue;
        }

        $owningTable = determineOwningSideTable($joinTable, $leftTable, $rightTable);
        $targetTable = $meta['table'] === $leftTable ? $rightTable : $leftTable;
        $targetClass = $tableMeta[$targetTable]['className'];
        $propertyName = camelCase(pluralize($targetClass));
        $singularProperty = camelCase($targetClass);
        $inverseProperty = camelCase(pluralize($className));

        $uses[] = 'Doctrine\Common\Collections\ArrayCollection';
        $uses[] = 'Doctrine\Common\Collections\Collection';
        $needsCollections = true;
        $constructorLines[] = "        \$this->{$propertyName} = new ArrayCollection();";

        $isOwningSide = $meta['table'] === $owningTable;

        if ($isOwningSide) {
            $joinColumn = $meta['table'] === $leftTable ? $joinMeta['left'] : $joinMeta['right'];
            $inverseJoinColumn = $meta['table'] === $leftTable ? $joinMeta['right'] : $joinMeta['left'];

            $properties[] =
                "    /**\n" .
                "     * @var Collection<int, {$targetClass}>\n" .
                "     */\n" .
                "    #[ORM\ManyToMany(targetEntity: {$targetClass}::class, inversedBy: '{$inverseProperty}')]\n" .
                "    #[ORM\JoinTable(name: '{$joinTable}')]\n" .
                "    #[ORM\JoinColumn(name: '{$joinColumn['COLUMN_NAME']}', referencedColumnName: '{$joinColumn['REFERENCED_COLUMN_NAME']}')]\n" .
                "    #[ORM\InverseJoinColumn(name: '{$inverseJoinColumn['COLUMN_NAME']}', referencedColumnName: '{$inverseJoinColumn['REFERENCED_COLUMN_NAME']}')]\n" .
                "    private Collection \${$propertyName};\n";
        } else {
            $properties[] =
                "    /**\n" .
                "     * @var Collection<int, {$targetClass}>\n" .
                "     */\n" .
                "    #[ORM\ManyToMany(targetEntity: {$targetClass}::class, mappedBy: '{$inverseProperty}')]\n" .
                "    private Collection \${$propertyName};\n";
        }

        $methods[] =
            "    /**\n" .
            "     * @return Collection<int, {$targetClass}>\n" .
            "     */\n" .
            "    public function get" . ucfirst($propertyName) . "(): Collection\n" .
            "    {\n" .
            "        return \$this->{$propertyName};\n" .
            "    }\n";

        $methods[] =
            "    public function add" . ucfirst($singularProperty) . "({$targetClass} \${$singularProperty}): static\n" .
            "    {\n" .
            "        if (!\$this->{$propertyName}->contains(\${$singularProperty})) {\n" .
            "            \$this->{$propertyName}->add(\${$singularProperty});\n" .
            ($isOwningSide
                ? "            \${$singularProperty}->add" . ucfirst(camelCase($className)) . "(\$this);\n"
                : '') .
            "        }\n\n" .
            "        return \$this;\n" .
            "    }\n";

        $methods[] =
            "    public function remove" . ucfirst($singularProperty) . "({$targetClass} \${$singularProperty}): static\n" .
            "    {\n" .
            "        \$this->{$propertyName}->removeElement(\${$singularProperty});\n" .
            ($isOwningSide
                ? "        \${$singularProperty}->remove" . ucfirst(camelCase($className)) . "(\$this);\n"
                : '') .
            "\n        return \$this;\n" .
            "    }\n";
    }

    $uses = array_values(array_unique($uses));
    sort($uses);

    $header = "<?php\n\ndeclare(strict_types=1);\n\nnamespace {$namespace};\n\n";
    foreach ($uses as $use) {
        $header .= "use {$use};\n";
    }
    $header .= "\n#[ORM\\Entity]\n#[ORM\\Table(name: '{$meta['table']}')]\nclass {$className}\n{\n";

    $constructor = '';
    $constructorLines = array_values(array_unique($constructorLines));
    if ($needsCollections) {
        $constructor = "    public function __construct()\n    {\n" .
            implode("\n", $constructorLines) .
            "\n    }\n\n";
    }

    $body = implode("\n", $properties) . "\n" . $constructor . implode("\n", $methods);

    return $header . $body . "}\n";
}

function mapColumnTypes(array $column): array
{
    $dataType = strtolower((string) $column['DATA_TYPE']);
    $columnType = strtolower((string) $column['COLUMN_TYPE']);
    $args = [];

    switch ($dataType) {
        case 'bigint':
            return ['string', 'bigint', []];

        case 'tinyint':
            if ($columnType === 'tinyint(1)') {
                return ['bool', 'boolean', []];
            }

            return ['int', 'smallint', []];

        case 'smallint':
            return ['int', 'smallint', []];

        case 'int':
        case 'integer':
        case 'mediumint':
            return ['int', 'integer', []];

        case 'decimal':
        case 'numeric':
            if ($column['NUMERIC_PRECISION']) {
                $args[] = 'precision: ' . (int) $column['NUMERIC_PRECISION'];
            }
            if ($column['NUMERIC_SCALE'] !== null) {
                $args[] = 'scale: ' . (int) $column['NUMERIC_SCALE'];
            }

            return ['string', 'decimal', $args];

        case 'float':
            return ['float', 'float', []];

        case 'double':
            return ['float', 'float', []];

        case 'datetime':
        case 'timestamp':
            return ['\DateTimeInterface', 'datetime', []];

        case 'date':
            return ['\DateTimeInterface', 'date', []];

        case 'time':
            return ['\DateTimeInterface', 'time', []];

        case 'text':
        case 'mediumtext':
        case 'longtext':
            return ['string', 'text', []];

        case 'enum':
            $args[] = "columnDefinition: \"" . addcslashes(strtoupper($column['COLUMN_TYPE']), "\\\"") . "\"";
            return ['string', 'string', $args];

        case 'char':
        case 'varchar':
        default:
            if ($column['CHARACTER_MAXIMUM_LENGTH']) {
                $args[] = 'length: ' . (int) $column['CHARACTER_MAXIMUM_LENGTH'];
            }
            return ['string', 'string', $args];
    }
}

function pascalCase(string $value): string
{
    $value = trim($value);
    if ($value === '') {
        return '';
    }

    if (str_contains($value, '_') === false && preg_match('/[A-Z]/', $value)) {
        return preg_replace('/[^a-zA-Z0-9]/', '', $value) ?? $value;
    }

    $value = preg_replace('/[^a-zA-Z0-9_]+/', '_', $value) ?? $value;
    $parts = array_filter(explode('_', strtolower($value)));

    return implode('', array_map(static fn(string $part): string => ucfirst($part), $parts));
}

function camelCase(string $value): string
{
    $value = trim($value);
    if ($value === '') {
        return '';
    }

    if (str_contains($value, '_') === false && preg_match('/[A-Z]/', $value)) {
        return lcfirst($value);
    }

    $pascal = pascalCase($value);

    return lcfirst($pascal);
}

function pluralize(string $word): string
{
    if (preg_match('/(s|x|z|ch|sh)$/i', $word)) {
        return $word . 'es';
    }
    if (preg_match('/y$/i', $word)) {
        return substr($word, 0, -1) . 'ies';
    }
    return $word . 's';
}

function relationPropertyName(string $columnName, string $targetClass): string
{
    $base = preg_replace('/_id$/', '', $columnName) ?? $columnName;
    $baseCamel = camelCase($base);

    if ($baseCamel === 'id' || $baseCamel === '') {
        return lcfirst($targetClass);
    }

    return $baseCamel;
}

function getterName(string $propertyName, string $phpType): string
{
    if ($phpType === 'bool') {
        return 'is' . ucfirst($propertyName);
    }

    return 'get' . ucfirst($propertyName);
}