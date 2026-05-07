<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260507003958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if (!$this->tableExists('categories')) {
            $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, slug VARCHAR(140) NOT NULL, description LONGTEXT NOT NULL, parent_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_3AF34668989D9B62 (slug), INDEX IDX_3AF34668727ACA70 (parent_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        }
        if (!$this->tableExists('consultation')) {
            $this->addSql('CREATE TABLE consultation (id INT AUTO_INCREMENT NOT NULL, date_consultation DATETIME NOT NULL, diagnostic LONGTEXT DEFAULT NULL, prescription LONGTEXT DEFAULT NULL, notes LONGTEXT DEFAULT NULL, rapport_id INT NOT NULL, rendez_vous_id INT DEFAULT NULL, doctor_id VARCHAR(36) NOT NULL, INDEX IDX_964685A61DFBCC46 (rapport_id), UNIQUE INDEX UNIQ_964685A691EF7EAA (rendez_vous_id), INDEX IDX_964685A687F4FB17 (doctor_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        }
        if (!$this->tableExists('notifications')) {
            $this->addSql('CREATE TABLE notifications (id INT AUTO_INCREMENT NOT NULL, user_id VARCHAR(36) NOT NULL, type VARCHAR(255) NOT NULL, content VARCHAR(200) NOT NULL, seen TINYINT NOT NULL, date DATE NOT NULL, thread_id INT NOT NULL, INDEX IDX_6000B0D3E2904019 (thread_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        }
        if (!$this->tableExists('postinteraction')) {
            $this->addSql('CREATE TABLE postinteraction (id INT AUTO_INCREMENT NOT NULL, user_id VARCHAR(36) NOT NULL, follow TINYINT NOT NULL, vote INT NOT NULL, thread_id INT NOT NULL, INDEX IDX_77BE041EE2904019 (thread_id), UNIQUE INDEX UNIQ_77BE041EE2904019A76ED395 (thread_id, user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        }
        if (!$this->tableExists('rapport')) {
            $this->addSql('CREATE TABLE rapport (id INT AUTO_INCREMENT NOT NULL, date_creation DATE NOT NULL, resume_general LONGTEXT DEFAULT NULL, patient_id VARCHAR(36) NOT NULL, UNIQUE INDEX UNIQ_BE34A09C6B899279 (patient_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        }
        if (!$this->tableExists('rendez_vous')) {
            $this->addSql('CREATE TABLE rendez_vous (id INT AUTO_INCREMENT NOT NULL, motif VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, date_time DATETIME DEFAULT NULL, status VARCHAR(30) NOT NULL, created_at DATETIME NOT NULL, proposed_date_time DATETIME DEFAULT NULL, doctor_note LONGTEXT DEFAULT NULL, patient_id VARCHAR(36) NOT NULL, doctor_id VARCHAR(36) NOT NULL, INDEX IDX_65E8AA0A6B899279 (patient_id), INDEX IDX_65E8AA0A87F4FB17 (doctor_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        }
        if (!$this->tableExists('replies')) {
            $this->addSql('CREATE TABLE replies (id INT AUTO_INCREMENT NOT NULL, user_id VARCHAR(36) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, thread_id INT NOT NULL, parent_id INT DEFAULT NULL, INDEX IDX_A000672AE2904019 (thread_id), INDEX IDX_A000672A727ACA70 (parent_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        }
        if (!$this->tableExists('threads')) {
            $this->addSql('CREATE TABLE threads (id INT AUTO_INCREMENT NOT NULL, user_id VARCHAR(36) NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, type VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, is_pinned TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, image_url VARCHAR(500) DEFAULT NULL, likecount INT NOT NULL, dislikecount INT NOT NULL, followcount INT NOT NULL, repliescount INT NOT NULL, category_id INT NOT NULL, INDEX IDX_6F8E3DDD12469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        }
        if ($this->tableExists('categories') && !$this->foreignKeyExists('categories', 'FK_3AF34668727ACA70')) {
            $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id) ON DELETE SET NULL');
        }
        if ($this->tableExists('consultation') && !$this->foreignKeyExists('consultation', 'FK_964685A61DFBCC46')) {
            $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A61DFBCC46 FOREIGN KEY (rapport_id) REFERENCES rapport (id)');
        }
        if ($this->tableExists('consultation') && !$this->foreignKeyExists('consultation', 'FK_964685A691EF7EAA')) {
            $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A691EF7EAA FOREIGN KEY (rendez_vous_id) REFERENCES rendez_vous (id)');
        }
        if ($this->tableExists('consultation') && !$this->foreignKeyExists('consultation', 'FK_964685A687F4FB17')) {
            $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A687F4FB17 FOREIGN KEY (doctor_id) REFERENCES users (id)');
        }
        if ($this->tableExists('notifications') && !$this->foreignKeyExists('notifications', 'FK_6000B0D3E2904019')) {
            $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3E2904019 FOREIGN KEY (thread_id) REFERENCES threads (id) ON DELETE CASCADE');
        }
        if ($this->tableExists('postinteraction') && !$this->foreignKeyExists('postinteraction', 'FK_77BE041EE2904019')) {
            $this->addSql('ALTER TABLE postinteraction ADD CONSTRAINT FK_77BE041EE2904019 FOREIGN KEY (thread_id) REFERENCES threads (id) ON DELETE CASCADE');
        }
        if ($this->tableExists('rapport') && !$this->foreignKeyExists('rapport', 'FK_BE34A09C6B899279')) {
            $this->addSql('ALTER TABLE rapport ADD CONSTRAINT FK_BE34A09C6B899279 FOREIGN KEY (patient_id) REFERENCES users (id)');
        }
        if ($this->tableExists('rendez_vous') && !$this->foreignKeyExists('rendez_vous', 'FK_65E8AA0A6B899279')) {
            $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A6B899279 FOREIGN KEY (patient_id) REFERENCES users (id)');
        }
        if ($this->tableExists('rendez_vous') && !$this->foreignKeyExists('rendez_vous', 'FK_65E8AA0A87F4FB17')) {
            $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A87F4FB17 FOREIGN KEY (doctor_id) REFERENCES users (id)');
        }
        if ($this->tableExists('replies') && !$this->foreignKeyExists('replies', 'FK_A000672AE2904019')) {
            $this->addSql('ALTER TABLE replies ADD CONSTRAINT FK_A000672AE2904019 FOREIGN KEY (thread_id) REFERENCES threads (id) ON DELETE CASCADE');
        }
        if ($this->tableExists('replies') && !$this->foreignKeyExists('replies', 'FK_A000672A727ACA70')) {
            $this->addSql('ALTER TABLE replies ADD CONSTRAINT FK_A000672A727ACA70 FOREIGN KEY (parent_id) REFERENCES replies (id) ON DELETE CASCADE');
        }
        if ($this->tableExists('threads') && !$this->foreignKeyExists('threads', 'FK_6F8E3DDD12469DE2')) {
            $this->addSql('ALTER TABLE threads ADD CONSTRAINT FK_6F8E3DDD12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        }
        if ($this->tableExists('exercice')) {
            if ($this->columnExists('exercice', 'benefits')) {
                $this->addSql('ALTER TABLE exercice DROP benefits');
            }
            if ($this->columnExists('exercice', 'guided_instructions')) {
                $this->addSql('ALTER TABLE exercice DROP guided_instructions');
            }
            if ($this->columnExists('exercice', 'tips')) {
                $this->addSql('ALTER TABLE exercice DROP tips');
            }
            if ($this->columnExists('exercice', 'image_url')) {
                $this->addSql('ALTER TABLE exercice DROP image_url');
            }
            if ($this->columnExists('exercice', 'theme')) {
                $this->addSql('ALTER TABLE exercice DROP theme');
            }
        }
        if ($this->tableExists('profiles')) {
            if (!$this->columnExists('profiles', 'anime_avatar_image_url')) {
                $this->addSql('ALTER TABLE profiles ADD anime_avatar_image_url VARCHAR(512) DEFAULT NULL');
            }
            if ($this->columnExists('profiles', 'anime_avatar_image')) {
                $this->addSql('ALTER TABLE profiles DROP anime_avatar_image');
            }
            if ($this->columnExists('profiles', 'anime_avatar_source_hash')) {
                $this->addSql('ALTER TABLE profiles DROP anime_avatar_source_hash');
            }
        }
        if ($this->tableExists('reves')) {
            if ($this->indexExists('reves', 'idx_reves_created')) {
                $this->addSql('DROP INDEX idx_reves_created ON reves');
            }
            if ($this->indexExists('reves', 'idx_reves_type')) {
                $this->addSql('DROP INDEX idx_reves_type ON reves');
            }
            $this->addSql('ALTER TABLE reves CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE sommeil_id sommeil_id INT DEFAULT NULL, CHANGE titre titre VARCHAR(200) DEFAULT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE couleur couleur TINYINT DEFAULT 1 NOT NULL, CHANGE recurrent recurrent TINYINT DEFAULT 0 NOT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        }
        if ($this->tableExists('sommeil')) {
            if ($this->indexExists('sommeil', 'idx_sommeil_qualite')) {
                $this->addSql('DROP INDEX idx_sommeil_qualite ON sommeil');
            }
            if ($this->indexExists('sommeil', 'idx_sommeil_user_date')) {
                $this->addSql('DROP INDEX idx_sommeil_user_date ON sommeil');
            }
            $this->addSql('ALTER TABLE sommeil CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE date_nuit date_nuit DATE DEFAULT NULL, CHANGE heure_coucher heure_coucher VARCHAR(255) DEFAULT NULL, CHANGE heure_reveil heure_reveil VARCHAR(255) DEFAULT NULL, CHANGE qualite qualite VARCHAR(50) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        }
        if ($this->tableExists('users') && !$this->columnExists('users', 'risk_level')) {
            $this->addSql('ALTER TABLE users ADD risk_level VARCHAR(20) DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        if ($this->tableExists('consultation') && $this->foreignKeyExists('consultation', 'FK_964685A61DFBCC46')) {
            $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A61DFBCC46');
        }
        if ($this->tableExists('consultation') && $this->foreignKeyExists('consultation', 'FK_964685A691EF7EAA')) {
            $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A691EF7EAA');
        }
        if ($this->tableExists('consultation') && $this->foreignKeyExists('consultation', 'FK_964685A687F4FB17')) {
            $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A687F4FB17');
        }
        if ($this->tableExists('notifications') && $this->foreignKeyExists('notifications', 'FK_6000B0D3E2904019')) {
            $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3E2904019');
        }
        if ($this->tableExists('postinteraction') && $this->foreignKeyExists('postinteraction', 'FK_77BE041EE2904019')) {
            $this->addSql('ALTER TABLE postinteraction DROP FOREIGN KEY FK_77BE041EE2904019');
        }
        if ($this->tableExists('rapport') && $this->foreignKeyExists('rapport', 'FK_BE34A09C6B899279')) {
            $this->addSql('ALTER TABLE rapport DROP FOREIGN KEY FK_BE34A09C6B899279');
        }
        if ($this->tableExists('rendez_vous') && $this->foreignKeyExists('rendez_vous', 'FK_65E8AA0A6B899279')) {
            $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A6B899279');
        }
        if ($this->tableExists('rendez_vous') && $this->foreignKeyExists('rendez_vous', 'FK_65E8AA0A87F4FB17')) {
            $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A87F4FB17');
        }
        if ($this->tableExists('replies') && $this->foreignKeyExists('replies', 'FK_A000672AE2904019')) {
            $this->addSql('ALTER TABLE replies DROP FOREIGN KEY FK_A000672AE2904019');
        }
        if ($this->tableExists('replies') && $this->foreignKeyExists('replies', 'FK_A000672A727ACA70')) {
            $this->addSql('ALTER TABLE replies DROP FOREIGN KEY FK_A000672A727ACA70');
        }
        if ($this->tableExists('threads') && $this->foreignKeyExists('threads', 'FK_6F8E3DDD12469DE2')) {
            $this->addSql('ALTER TABLE threads DROP FOREIGN KEY FK_6F8E3DDD12469DE2');
        }
        if ($this->tableExists('consultation')) {
            $this->addSql('DROP TABLE consultation');
        }
        if ($this->tableExists('notifications')) {
            $this->addSql('DROP TABLE notifications');
        }
        if ($this->tableExists('postinteraction')) {
            $this->addSql('DROP TABLE postinteraction');
        }
        if ($this->tableExists('rapport')) {
            $this->addSql('DROP TABLE rapport');
        }
        if ($this->tableExists('rendez_vous')) {
            $this->addSql('DROP TABLE rendez_vous');
        }
        if ($this->tableExists('replies')) {
            $this->addSql('DROP TABLE replies');
        }
        if ($this->tableExists('threads')) {
            $this->addSql('DROP TABLE threads');
        }
        if ($this->tableExists('exercice')) {
            if (!$this->columnExists('exercice', 'benefits')) {
                $this->addSql('ALTER TABLE exercice ADD benefits TEXT DEFAULT NULL');
            }
            if (!$this->columnExists('exercice', 'guided_instructions')) {
                $this->addSql('ALTER TABLE exercice ADD guided_instructions JSON DEFAULT NULL');
            }
            if (!$this->columnExists('exercice', 'tips')) {
                $this->addSql('ALTER TABLE exercice ADD tips TEXT DEFAULT NULL');
            }
            if (!$this->columnExists('exercice', 'image_url')) {
                $this->addSql('ALTER TABLE exercice ADD image_url VARCHAR(512) DEFAULT NULL');
            }
            if (!$this->columnExists('exercice', 'theme')) {
                $this->addSql('ALTER TABLE exercice ADD theme VARCHAR(50) DEFAULT NULL');
            }
        }
        if ($this->tableExists('profiles')) {
            if (!$this->columnExists('profiles', 'anime_avatar_image')) {
                $this->addSql('ALTER TABLE profiles ADD anime_avatar_image LONGTEXT DEFAULT NULL');
            }
            if (!$this->columnExists('profiles', 'anime_avatar_source_hash')) {
                $this->addSql('ALTER TABLE profiles ADD anime_avatar_source_hash VARCHAR(64) DEFAULT NULL');
            }
            if ($this->columnExists('profiles', 'anime_avatar_image_url')) {
                $this->addSql('ALTER TABLE profiles DROP anime_avatar_image_url');
            }
        }
        if ($this->tableExists('reves')) {
            $this->addSql('ALTER TABLE reves CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE titre titre VARCHAR(200) NOT NULL, CHANGE description description LONGTEXT NOT NULL, CHANGE couleur couleur TINYINT NOT NULL, CHANGE recurrent recurrent TINYINT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL, CHANGE sommeil_id sommeil_id BIGINT DEFAULT NULL');
            if (!$this->indexExists('reves', 'idx_reves_created')) {
                $this->addSql('CREATE INDEX idx_reves_created ON reves (created_at)');
            }
            if (!$this->indexExists('reves', 'idx_reves_type')) {
                $this->addSql('CREATE INDEX idx_reves_type ON reves (type_reve)');
            }
        }
        if ($this->tableExists('sommeil')) {
            $this->addSql('ALTER TABLE sommeil CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE date_nuit date_nuit DATE NOT NULL, CHANGE heure_coucher heure_coucher VARCHAR(10) NOT NULL, CHANGE heure_reveil heure_reveil VARCHAR(10) NOT NULL, CHANGE qualite qualite VARCHAR(50) NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
            if (!$this->indexExists('sommeil', 'idx_sommeil_qualite')) {
                $this->addSql('CREATE INDEX idx_sommeil_qualite ON sommeil (qualite)');
            }
            if (!$this->indexExists('sommeil', 'idx_sommeil_user_date')) {
                $this->addSql('CREATE INDEX idx_sommeil_user_date ON sommeil (user_id, date_nuit)');
            }
        }
        if ($this->tableExists('users') && $this->columnExists('users', 'risk_level')) {
            $this->addSql('ALTER TABLE users DROP risk_level');
        }
    }

    private function tableExists(string $tableName): bool
    {
        return $this->connection->createSchemaManager()->tablesExist([$tableName]);
    }

    private function foreignKeyExists(string $tableName, string $foreignKeyName): bool
    {
        foreach ($this->connection->createSchemaManager()->listTableForeignKeys($tableName) as $foreignKey) {
            if ($foreignKey->getName() === $foreignKeyName) {
                return true;
            }
        }

        return false;
    }

    private function columnExists(string $tableName, string $columnName): bool
    {
        if (!$this->tableExists($tableName)) {
            return false;
        }

        return array_key_exists($columnName, $this->connection->createSchemaManager()->listTableColumns($tableName));
    }

    private function indexExists(string $tableName, string $indexName): bool
    {
        if (!$this->tableExists($tableName)) {
            return false;
        }

        foreach ($this->connection->createSchemaManager()->listTableIndexes($tableName) as $index) {
            if ($index->getName() === $indexName) {
                return true;
            }
        }

        return false;
    }
}
