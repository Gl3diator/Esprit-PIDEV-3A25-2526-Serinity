<?php

declare(strict_types=1);

$dumpPath = $argv[1] ?? 'C:\\Users\\saifd\\Downloads\\serinity.sql';

if (!is_file($dumpPath)) {
    fwrite(STDERR, "Dump file not found: {$dumpPath}\n");
    exit(1);
}

$mysqli = @new mysqli('127.0.0.1', 'root', '', '', 3306);
if ($mysqli->connect_errno) {
    fwrite(STDERR, 'MySQL connection failed: '.$mysqli->connect_error."\n");
    exit(1);
}

$mysqli->set_charset('utf8mb4');

if (!$mysqli->query('DROP DATABASE IF EXISTS serinity')) {
    fwrite(STDERR, 'DROP DATABASE failed: '.$mysqli->error."\n");
    exit(1);
}

if (!$mysqli->query('CREATE DATABASE serinity CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')) {
    fwrite(STDERR, 'CREATE DATABASE failed: '.$mysqli->error."\n");
    exit(1);
}

if (!$mysqli->select_db('serinity')) {
    fwrite(STDERR, 'USE serinity failed: '.$mysqli->error."\n");
    exit(1);
}

$sql = file_get_contents($dumpPath);
if ($sql === false) {
    fwrite(STDERR, "Failed to read dump file.\n");
    exit(1);
}

// Remove a known malformed line in this dump.
$sql = str_replace("\n**\n", "\n", $sql);

$statements = [];
$buffer = '';
$inSingleQuote = false;
$inDoubleQuote = false;
$escape = false;
$len = strlen($sql);

for ($i = 0; $i < $len; $i++) {
    $ch = $sql[$i];

    if ($escape) {
        $buffer .= $ch;
        $escape = false;
        continue;
    }

    if ($ch === '\\') {
        $buffer .= $ch;
        if ($inSingleQuote || $inDoubleQuote) {
            $escape = true;
        }
        continue;
    }

    if ($ch === "'" && !$inDoubleQuote) {
        $inSingleQuote = !$inSingleQuote;
        $buffer .= $ch;
        continue;
    }

    if ($ch === '"' && !$inSingleQuote) {
        $inDoubleQuote = !$inDoubleQuote;
        $buffer .= $ch;
        continue;
    }

    if ($ch === ';' && !$inSingleQuote && !$inDoubleQuote) {
        $stmt = trim($buffer);
        if ($stmt !== '') {
            $statements[] = $stmt;
        }
        $buffer = '';
        continue;
    }

    $buffer .= $ch;
}

$tail = trim($buffer);
if ($tail !== '') {
    $statements[] = $tail;
}

$ok = 0;
$failed = [];

foreach ($statements as $stmt) {
    $trim = ltrim($stmt);

    if (str_starts_with($trim, '--')) {
        continue;
    }

    // Skip bad/incomplete section that breaks import in this dump.
    if (str_contains($stmt, "ADD KEY `notifications_ibfk_1` (`thread_id`),")
        || str_contains($stmt, "ADD KEY `thread_id` (`thread_id`),")
        || preg_match('/^-- Indexes for table `profiles`\s*-$/m', $stmt)) {
        continue;
    }

    if (!$mysqli->query($stmt)) {
        $failed[] = [
            'error' => $mysqli->error,
            'sql' => substr(preg_replace('/\s+/', ' ', $stmt) ?? $stmt, 0, 220),
        ];
        continue;
    }

    $ok++;
}

// Ensure these indexes exist even if broken statements were skipped.
$repairStatements = [
    "ALTER TABLE notifications ADD INDEX IDX_NOTIFICATIONS_THREAD (thread_id)",
    "ALTER TABLE postinteraction ADD INDEX IDX_POSTINTERACTION_THREAD (thread_id)",
    "ALTER TABLE replies ADD INDEX IDX_REPLIES_THREAD (thread_id)",
    "ALTER TABLE replies ADD INDEX IDX_REPLIES_PARENT (parent_id)",
    "ALTER TABLE threads ADD INDEX IDX_THREADS_CATEGORY (category_id)",
    "ALTER TABLE profiles ADD UNIQUE INDEX UNIQ_PROFILES_EMAIL (email)",
    "ALTER TABLE profiles ADD UNIQUE INDEX UNIQ_PROFILES_USERNAME (username)",
];

foreach ($repairStatements as $stmt) {
    if (!$mysqli->query($stmt)) {
        // Ignore duplicate index errors, collect other repair failures.
        if ((int) $mysqli->errno !== 1061) {
            $failed[] = [
                'error' => $mysqli->error,
                'sql' => $stmt,
            ];
        }
    }
}

fwrite(STDOUT, "Import complete. Successful statements: {$ok}. Failures: ".count($failed)."\n");

if ($failed !== []) {
    fwrite(STDOUT, "Sample failures (up to 10):\n");
    $limit = min(10, count($failed));
    for ($i = 0; $i < $limit; $i++) {
        fwrite(STDOUT, ($i + 1).") ".$failed[$i]['error']."\n   SQL: ".$failed[$i]['sql']."\n");
    }
}

$mysqli->close();
