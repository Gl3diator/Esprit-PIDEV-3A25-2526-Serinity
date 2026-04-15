<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260413224219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE audit_logs (id VARCHAR(36) NOT NULL, action VARCHAR(100) NOT NULL, os_name VARCHAR(50) DEFAULT NULL, hostname VARCHAR(100) DEFAULT NULL, private_ip_address VARCHAR(45) NOT NULL, mac_address VARCHAR(17) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, auth_session_id VARCHAR(36) NOT NULL, INDEX idx_audit_created (created_at), INDEX fk_audit_logs_auth_session_id (auth_session_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE auth_sessions (id VARCHAR(36) NOT NULL, refresh_token VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, revoked TINYINT NOT NULL, user_id VARCHAR(36) NOT NULL, UNIQUE INDEX UNIQ_BE9A1A95C74F2195 (refresh_token), INDEX idx_session_token (refresh_token), INDEX idx_session_user (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE emotion (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(40) NOT NULL, UNIQUE INDEX UNIQ_DEBC775E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE exercise (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, type VARCHAR(100) NOT NULL, level INT NOT NULL, duration_minutes INT NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE exercise_session (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(100) NOT NULL, started_at DATETIME DEFAULT NULL, completed_at DATETIME DEFAULT NULL, feedback LONGTEXT DEFAULT NULL, active_seconds INT DEFAULT 0 NOT NULL, last_resumed_at DATETIME DEFAULT NULL, user_id VARCHAR(36) NOT NULL, exercise_id INT NOT NULL, INDEX IDX_A512291A76ED395 (user_id), INDEX IDX_A512291E934951A (exercise_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE favorite (id INT AUTO_INCREMENT NOT NULL, favorite_type VARCHAR(255) NOT NULL, item_id INT NOT NULL, created_at DATETIME NOT NULL, user_id VARCHAR(36) NOT NULL, INDEX IDX_68C58ED9A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE influence (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(60) NOT NULL, UNIQUE INDEX UNIQ_487304315E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE journal_entry (id BIGINT AUTO_INCREMENT NOT NULL, user_id VARCHAR(36) NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, ai_tags LONGTEXT DEFAULT NULL, ai_model_version VARCHAR(32) DEFAULT NULL, ai_generated_at DATETIME DEFAULT NULL, INDEX idx_journal_user_created (user_id, created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mood_entry (id BIGINT AUTO_INCREMENT NOT NULL, user_id VARCHAR(36) NOT NULL, entry_date DATETIME NOT NULL, moment_type ENUM(\'MOMENT\',\'DAY\'), mood_level SMALLINT NOT NULL, updated_at DATETIME NOT NULL, INDEX idx_mood_entry_user_date (user_id, entry_date), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mood_entry_emotion (mood_entry_id BIGINT NOT NULL, emotion_id INT NOT NULL, INDEX IDX_EB264C1975B2D1A3 (mood_entry_id), INDEX IDX_EB264C191EE4A582 (emotion_id), PRIMARY KEY (mood_entry_id, emotion_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mood_entry_influence (mood_entry_id BIGINT NOT NULL, influence_id INT NOT NULL, INDEX IDX_EB05BFC75B2D1A3 (mood_entry_id), INDEX IDX_EB05BFCEE88AF5D (influence_id), PRIMARY KEY (mood_entry_id, influence_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE profiles (id VARCHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, username VARCHAR(255) NOT NULL, firstName VARCHAR(255) DEFAULT NULL, lastName VARCHAR(255) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, profile_image_url VARCHAR(512) DEFAULT NULL, country VARCHAR(100) DEFAULT NULL, state VARCHAR(100) DEFAULT NULL, aboutMe VARCHAR(500) DEFAULT NULL, user_id VARCHAR(36) NOT NULL, UNIQUE INDEX UNIQ_8B308530A76ED395 (user_id), UNIQUE INDEX uk_profile_username (username), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE resource (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, media_type VARCHAR(255) NOT NULL, url LONGTEXT DEFAULT NULL, content LONGTEXT DEFAULT NULL, duration_seconds INT DEFAULT NULL, exercise_id INT DEFAULT NULL, INDEX IDX_BC91F416E934951A (exercise_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_faces (id VARCHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, embedding LONGBLOB NOT NULL, user_id VARCHAR(36) NOT NULL, INDEX IDX_2A1FD4D3A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id VARCHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, email VARCHAR(150) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, presence_status VARCHAR(255) NOT NULL, account_status VARCHAR(255) NOT NULL, face_recognition_enabled TINYINT NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE audit_logs ADD CONSTRAINT FK_D62F28587F3AE16B FOREIGN KEY (auth_session_id) REFERENCES auth_sessions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE auth_sessions ADD CONSTRAINT FK_BE9A1A95A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE exercise_session ADD CONSTRAINT FK_A512291A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE exercise_session ADD CONSTRAINT FK_A512291E934951A FOREIGN KEY (exercise_id) REFERENCES exercise (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED9A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mood_entry_emotion ADD CONSTRAINT FK_EB264C1975B2D1A3 FOREIGN KEY (mood_entry_id) REFERENCES mood_entry (id)');
        $this->addSql('ALTER TABLE mood_entry_emotion ADD CONSTRAINT FK_EB264C191EE4A582 FOREIGN KEY (emotion_id) REFERENCES emotion (id)');
        $this->addSql('ALTER TABLE mood_entry_influence ADD CONSTRAINT FK_EB05BFC75B2D1A3 FOREIGN KEY (mood_entry_id) REFERENCES mood_entry (id)');
        $this->addSql('ALTER TABLE mood_entry_influence ADD CONSTRAINT FK_EB05BFCEE88AF5D FOREIGN KEY (influence_id) REFERENCES influence (id)');
        $this->addSql('ALTER TABLE profiles ADD CONSTRAINT FK_8B308530A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE resource ADD CONSTRAINT FK_BC91F416E934951A FOREIGN KEY (exercise_id) REFERENCES exercise (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user_faces ADD CONSTRAINT FK_2A1FD4D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        // ❌ SUPPRIMÉ : blocs ALTER TABLE reves + sommeil (tables déjà existantes)
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE audit_logs DROP FOREIGN KEY FK_D62F28587F3AE16B');
        $this->addSql('ALTER TABLE auth_sessions DROP FOREIGN KEY FK_BE9A1A95A76ED395');
        $this->addSql('ALTER TABLE exercise_session DROP FOREIGN KEY FK_A512291A76ED395');
        $this->addSql('ALTER TABLE exercise_session DROP FOREIGN KEY FK_A512291E934951A');
        $this->addSql('ALTER TABLE favorite DROP FOREIGN KEY FK_68C58ED9A76ED395');
        $this->addSql('ALTER TABLE mood_entry_emotion DROP FOREIGN KEY FK_EB264C1975B2D1A3');
        $this->addSql('ALTER TABLE mood_entry_emotion DROP FOREIGN KEY FK_EB264C191EE4A582');
        $this->addSql('ALTER TABLE mood_entry_influence DROP FOREIGN KEY FK_EB05BFC75B2D1A3');
        $this->addSql('ALTER TABLE mood_entry_influence DROP FOREIGN KEY FK_EB05BFCEE88AF5D');
        $this->addSql('ALTER TABLE profiles DROP FOREIGN KEY FK_8B308530A76ED395');
        $this->addSql('ALTER TABLE resource DROP FOREIGN KEY FK_BC91F416E934951A');
        $this->addSql('ALTER TABLE user_faces DROP FOREIGN KEY FK_2A1FD4D3A76ED395');
        $this->addSql('DROP TABLE audit_logs');
        $this->addSql('DROP TABLE auth_sessions');
        $this->addSql('DROP TABLE emotion');
        $this->addSql('DROP TABLE exercise');
        $this->addSql('DROP TABLE exercise_session');
        $this->addSql('DROP TABLE favorite');
        $this->addSql('DROP TABLE influence');
        $this->addSql('DROP TABLE journal_entry');
        $this->addSql('DROP TABLE mood_entry');
        $this->addSql('DROP TABLE mood_entry_emotion');
        $this->addSql('DROP TABLE mood_entry_influence');
        $this->addSql('DROP TABLE profiles');
        $this->addSql('DROP TABLE resource');
        $this->addSql('DROP TABLE user_faces');
        $this->addSql('DROP TABLE users');
    }
}