<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260406000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add forum module tables (categories, threads, replies, interactions, notifications)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(120) NOT NULL, slug VARCHAR(140) NOT NULL, description LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_3AF34668989D9B62 (slug), INDEX IDX_3AF34668727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE threads (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, user_id VARCHAR(36) NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, type VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, is_pinned TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT ''(DC2Type:datetime_immutable)'', updated_at DATETIME NOT NULL COMMENT ''(DC2Type:datetime_immutable)'', image_url VARCHAR(500) DEFAULT NULL, likecount INT NOT NULL, dislikecount INT NOT NULL, followcount INT NOT NULL, repliescount INT NOT NULL, INDEX IDX_6F8E3DDD12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE replies (id INT AUTO_INCREMENT NOT NULL, thread_id INT NOT NULL, parent_id INT DEFAULT NULL, user_id VARCHAR(36) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT ''(DC2Type:datetime_immutable)'', updated_at DATETIME NOT NULL COMMENT ''(DC2Type:datetime_immutable)'', INDEX IDX_A000672AE2904019 (thread_id), INDEX IDX_A000672A727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE postinteraction (id INT AUTO_INCREMENT NOT NULL, thread_id INT NOT NULL, user_id VARCHAR(36) NOT NULL, follow TINYINT(1) NOT NULL, vote INT NOT NULL, UNIQUE INDEX UNIQ_AE53A265E2904019A76ED395 (thread_id, user_id), INDEX IDX_AE53A265E2904019 (thread_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notifications (id INT AUTO_INCREMENT NOT NULL, thread_id INT NOT NULL, user_id VARCHAR(36) NOT NULL, type VARCHAR(255) NOT NULL, content VARCHAR(200) NOT NULL, seen TINYINT(1) NOT NULL, date DATE NOT NULL COMMENT ''(DC2Type:date_immutable)'', INDEX IDX_6000B0D3E2904019 (thread_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE threads ADD CONSTRAINT FK_6F8E3DDD12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE replies ADD CONSTRAINT FK_A000672AE2904019 FOREIGN KEY (thread_id) REFERENCES threads (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE replies ADD CONSTRAINT FK_A000672A727ACA70 FOREIGN KEY (parent_id) REFERENCES replies (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE postinteraction ADD CONSTRAINT FK_AE53A265E2904019 FOREIGN KEY (thread_id) REFERENCES threads (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3E2904019 FOREIGN KEY (thread_id) REFERENCES threads (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668727ACA70');
        $this->addSql('ALTER TABLE threads DROP FOREIGN KEY FK_6F8E3DDD12469DE2');
        $this->addSql('ALTER TABLE replies DROP FOREIGN KEY FK_A000672AE2904019');
        $this->addSql('ALTER TABLE replies DROP FOREIGN KEY FK_A000672A727ACA70');
        $this->addSql('ALTER TABLE postinteraction DROP FOREIGN KEY FK_AE53A265E2904019');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3E2904019');
        $this->addSql('DROP TABLE notifications');
        $this->addSql('DROP TABLE postinteraction');
        $this->addSql('DROP TABLE replies');
        $this->addSql('DROP TABLE threads');
        $this->addSql('DROP TABLE categories');
    }
}
