<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260507121000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add dynamic text guidance fields to exercice.';
    }

    public function up(Schema $schema): void
    {
        if (!$this->tableExists('exercice')) {
            return;
        }

        if (!$this->columnExists('exercice', 'benefits')) {
            $this->addSql('ALTER TABLE exercice ADD benefits LONGTEXT DEFAULT NULL');
        }
        if (!$this->columnExists('exercice', 'tips')) {
            $this->addSql('ALTER TABLE exercice ADD tips LONGTEXT DEFAULT NULL');
        }
        if (!$this->columnExists('exercice', 'theme')) {
            $this->addSql('ALTER TABLE exercice ADD theme VARCHAR(50) DEFAULT NULL');
        }
        if (!$this->columnExists('exercice', 'guided_instructions')) {
            $this->addSql('ALTER TABLE exercice ADD guided_instructions JSON DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        if (!$this->tableExists('exercice')) {
            return;
        }

        if ($this->columnExists('exercice', 'benefits')) {
            $this->addSql('ALTER TABLE exercice DROP benefits');
        }
        if ($this->columnExists('exercice', 'tips')) {
            $this->addSql('ALTER TABLE exercice DROP tips');
        }
        if ($this->columnExists('exercice', 'theme')) {
            $this->addSql('ALTER TABLE exercice DROP theme');
        }
        if ($this->columnExists('exercice', 'guided_instructions')) {
            $this->addSql('ALTER TABLE exercice DROP guided_instructions');
        }
    }

    private function tableExists(string $tableName): bool
    {
        return $this->connection->createSchemaManager()->tablesExist([$tableName]);
    }

    private function columnExists(string $tableName, string $columnName): bool
    {
        if (!$this->tableExists($tableName)) {
            return false;
        }

        return array_key_exists($columnName, $this->connection->createSchemaManager()->listTableColumns($tableName));
    }
}
