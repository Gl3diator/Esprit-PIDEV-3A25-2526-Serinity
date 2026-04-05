<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260401201454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, slug VARCHAR(140) NOT NULL, description LONGTEXT NOT NULL, parent_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_3AF34668989D9B62 (slug), INDEX IDX_3AF34668727ACA70 (parent_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE notifications (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, seen TINYINT NOT NULL, created_at DATETIME NOT NULL, thread_id INT NOT NULL, recipient_id INT NOT NULL, INDEX IDX_6000B0D3E2904019 (thread_id), INDEX IDX_6000B0D3E92F8F78 (recipient_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE post_interactions (id INT AUTO_INCREMENT NOT NULL, follow TINYINT NOT NULL, vote INT NOT NULL, thread_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F8B6096EE2904019 (thread_id), INDEX IDX_F8B6096EA76ED395 (user_id), UNIQUE INDEX UNIQ_F8B6096EE2904019A76ED395 (thread_id, user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE replies (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, thread_id INT NOT NULL, author_id INT NOT NULL, parent_id INT DEFAULT NULL, INDEX IDX_A000672AE2904019 (thread_id), INDEX IDX_A000672AF675F31B (author_id), INDEX IDX_A000672A727ACA70 (parent_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE threads (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, type VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, is_pinned TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, image_url VARCHAR(500) DEFAULT NULL, like_count INT NOT NULL, dislike_count INT NOT NULL, follow_count INT NOT NULL, reply_count INT NOT NULL, category_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_6F8E3DDD12469DE2 (category_id), INDEX IDX_6F8E3DDDF675F31B (author_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, username VARCHAR(80) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3E2904019 FOREIGN KEY (thread_id) REFERENCES threads (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3E92F8F78 FOREIGN KEY (recipient_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_interactions ADD CONSTRAINT FK_F8B6096EE2904019 FOREIGN KEY (thread_id) REFERENCES threads (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_interactions ADD CONSTRAINT FK_F8B6096EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE replies ADD CONSTRAINT FK_A000672AE2904019 FOREIGN KEY (thread_id) REFERENCES threads (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE replies ADD CONSTRAINT FK_A000672AF675F31B FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE replies ADD CONSTRAINT FK_A000672A727ACA70 FOREIGN KEY (parent_id) REFERENCES replies (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE threads ADD CONSTRAINT FK_6F8E3DDD12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE threads ADD CONSTRAINT FK_6F8E3DDDF675F31B FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668727ACA70');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3E2904019');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3E92F8F78');
        $this->addSql('ALTER TABLE post_interactions DROP FOREIGN KEY FK_F8B6096EE2904019');
        $this->addSql('ALTER TABLE post_interactions DROP FOREIGN KEY FK_F8B6096EA76ED395');
        $this->addSql('ALTER TABLE replies DROP FOREIGN KEY FK_A000672AE2904019');
        $this->addSql('ALTER TABLE replies DROP FOREIGN KEY FK_A000672AF675F31B');
        $this->addSql('ALTER TABLE replies DROP FOREIGN KEY FK_A000672A727ACA70');
        $this->addSql('ALTER TABLE threads DROP FOREIGN KEY FK_6F8E3DDD12469DE2');
        $this->addSql('ALTER TABLE threads DROP FOREIGN KEY FK_6F8E3DDDF675F31B');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE notifications');
        $this->addSql('DROP TABLE post_interactions');
        $this->addSql('DROP TABLE replies');
        $this->addSql('DROP TABLE threads');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
