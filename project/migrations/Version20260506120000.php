<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260506120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add dynamic content fields to exercice for benefits, guided instructions, tips, image, and theme';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('exercice')) {
            return;
        }

        $table = $schema->getTable('exercice');

        if (!$table->hasColumn('benefits')) {
            $this->addSql('ALTER TABLE exercice ADD benefits TEXT DEFAULT NULL');
        }

        if (!$table->hasColumn('guided_instructions')) {
            $this->addSql('ALTER TABLE exercice ADD guided_instructions JSON DEFAULT NULL');
        }

        if (!$table->hasColumn('tips')) {
            $this->addSql('ALTER TABLE exercice ADD tips TEXT DEFAULT NULL');
        }

        if (!$table->hasColumn('image_url')) {
            $this->addSql('ALTER TABLE exercice ADD image_url VARCHAR(512) DEFAULT NULL');
        }

        if (!$table->hasColumn('theme')) {
            $this->addSql('ALTER TABLE exercice ADD theme VARCHAR(50) DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        if (!$schema->hasTable('exercice')) {
            return;
        }

        $table = $schema->getTable('exercice');

        if ($table->hasColumn('theme')) {
            $this->addSql('ALTER TABLE exercice DROP COLUMN theme');
        }

        if ($table->hasColumn('image_url')) {
            $this->addSql('ALTER TABLE exercice DROP COLUMN image_url');
        }

        if ($table->hasColumn('tips')) {
            $this->addSql('ALTER TABLE exercice DROP COLUMN tips');
        }

        if ($table->hasColumn('guided_instructions')) {
            $this->addSql('ALTER TABLE exercice DROP COLUMN guided_instructions');
        }

        if ($table->hasColumn('benefits')) {
            $this->addSql('ALTER TABLE exercice DROP COLUMN benefits');
        }
    }
}
