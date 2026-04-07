<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260407102231 extends AbstractMigration
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
        $this->addSql('CREATE TABLE exercice (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, type VARCHAR(100) NOT NULL, level SMALLINT NOT NULL, duration_minutes SMALLINT NOT NULL, description LONGTEXT DEFAULT NULL, is_active TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX idx_exercice_active (is_active), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE exercice_control (id BIGINT AUTO_INCREMENT NOT NULL, status VARCHAR(20) NOT NULL, started_at DATETIME DEFAULT NULL, completed_at DATETIME DEFAULT NULL, active_seconds INT NOT NULL, feedback LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, user_id VARCHAR(36) NOT NULL, exercice_id INT NOT NULL, assigned_by VARCHAR(36) DEFAULT NULL, INDEX IDX_10605AE689D40298 (exercice_id), INDEX IDX_10605AE661A2AF17 (assigned_by), INDEX idx_exercice_control_user (user_id), INDEX idx_exercice_control_status (status), INDEX idx_exercice_control_started_at (started_at), INDEX idx_exercice_control_completed_at (completed_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE exercice_favorite (id INT AUTO_INCREMENT NOT NULL, favorite_type VARCHAR(20) NOT NULL, item_id INT NOT NULL, created_at DATETIME NOT NULL, user_id VARCHAR(36) NOT NULL, INDEX idx_exercice_favorite_user (user_id), UNIQUE INDEX uniq_exercice_favorite (user_id, favorite_type, item_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE exercice_resource (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, resource_type VARCHAR(40) NOT NULL, resource_url VARCHAR(512) NOT NULL, created_at DATETIME NOT NULL, exercice_id INT NOT NULL, INDEX idx_exercice_resource_exercice (exercice_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE influence (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(60) NOT NULL, UNIQUE INDEX UNIQ_487304315E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE journal_entry (id BIGINT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, ai_tags LONGTEXT DEFAULT NULL, ai_model_version VARCHAR(32) DEFAULT NULL, ai_generated_at DATETIME DEFAULT NULL, user_id VARCHAR(36) NOT NULL, INDEX IDX_C8FAAE5AA76ED395 (user_id), INDEX idx_journal_user_created (user_id, created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mood_entry (id BIGINT AUTO_INCREMENT NOT NULL, entry_date DATETIME NOT NULL, moment_type VARCHAR(16) NOT NULL, mood_level SMALLINT NOT NULL, updated_at DATETIME NOT NULL, user_id VARCHAR(36) NOT NULL, INDEX IDX_22A0A36DA76ED395 (user_id), INDEX idx_mood_entry_user_date (user_id, entry_date), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mood_entry_emotion (mood_entry_id BIGINT NOT NULL, emotion_id INT NOT NULL, INDEX IDX_EB264C1975B2D1A3 (mood_entry_id), INDEX IDX_EB264C191EE4A582 (emotion_id), PRIMARY KEY (mood_entry_id, emotion_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mood_entry_influence (mood_entry_id BIGINT NOT NULL, influence_id INT NOT NULL, INDEX IDX_EB05BFC75B2D1A3 (mood_entry_id), INDEX IDX_EB05BFCEE88AF5D (influence_id), PRIMARY KEY (mood_entry_id, influence_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE profiles (id VARCHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, username VARCHAR(255) NOT NULL, firstName VARCHAR(255) DEFAULT NULL, lastName VARCHAR(255) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, profile_image_url VARCHAR(512) DEFAULT NULL, country VARCHAR(100) DEFAULT NULL, state VARCHAR(100) DEFAULT NULL, aboutMe VARCHAR(500) DEFAULT NULL, user_id VARCHAR(36) NOT NULL, UNIQUE INDEX UNIQ_8B308530A76ED395 (user_id), UNIQUE INDEX uk_profile_username (username), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reves (id BIGINT AUTO_INCREMENT NOT NULL, titre VARCHAR(200) NOT NULL, description LONGTEXT NOT NULL, humeur VARCHAR(50) DEFAULT NULL, type_reve VARCHAR(50) DEFAULT NULL, intensite INT DEFAULT NULL, couleur TINYINT NOT NULL, emotions VARCHAR(200) DEFAULT NULL, symboles LONGTEXT DEFAULT NULL, recurrent TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, sommeil_id BIGINT DEFAULT NULL, INDEX IDX_C832E805F84A5F9B (sommeil_id), INDEX idx_reves_created (created_at), INDEX idx_reves_type (type_reve), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sommeil (id BIGINT AUTO_INCREMENT NOT NULL, date_nuit DATE NOT NULL, heure_coucher VARCHAR(10) NOT NULL, heure_reveil VARCHAR(10) NOT NULL, qualite VARCHAR(50) NOT NULL, commentaire LONGTEXT DEFAULT NULL, duree_sommeil DOUBLE PRECISION DEFAULT NULL, interruptions INT DEFAULT NULL, humeur_reveil VARCHAR(50) DEFAULT NULL, environnement VARCHAR(100) DEFAULT NULL, temperature DOUBLE PRECISION DEFAULT NULL, bruit_niveau VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, user_id VARCHAR(36) NOT NULL, INDEX IDX_AF87C033A76ED395 (user_id), INDEX idx_sommeil_user_date (user_id, date_nuit), INDEX idx_sommeil_qualite (qualite), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_faces (id VARCHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, embedding LONGBLOB NOT NULL, user_id VARCHAR(36) NOT NULL, INDEX IDX_2A1FD4D3A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id VARCHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, email VARCHAR(150) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, presence_status VARCHAR(255) NOT NULL, account_status VARCHAR(255) NOT NULL, face_recognition_enabled TINYINT NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE audit_logs ADD CONSTRAINT FK_D62F28587F3AE16B FOREIGN KEY (auth_session_id) REFERENCES auth_sessions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE auth_sessions ADD CONSTRAINT FK_BE9A1A95A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE exercice_control ADD CONSTRAINT FK_10605AE6A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE exercice_control ADD CONSTRAINT FK_10605AE689D40298 FOREIGN KEY (exercice_id) REFERENCES exercice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE exercice_control ADD CONSTRAINT FK_10605AE661A2AF17 FOREIGN KEY (assigned_by) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE exercice_favorite ADD CONSTRAINT FK_C05FEAFAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE exercice_resource ADD CONSTRAINT FK_140B903589D40298 FOREIGN KEY (exercice_id) REFERENCES exercice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE journal_entry ADD CONSTRAINT FK_C8FAAE5AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mood_entry ADD CONSTRAINT FK_22A0A36DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mood_entry_emotion ADD CONSTRAINT FK_EB264C1975B2D1A3 FOREIGN KEY (mood_entry_id) REFERENCES mood_entry (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mood_entry_emotion ADD CONSTRAINT FK_EB264C191EE4A582 FOREIGN KEY (emotion_id) REFERENCES emotion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mood_entry_influence ADD CONSTRAINT FK_EB05BFC75B2D1A3 FOREIGN KEY (mood_entry_id) REFERENCES mood_entry (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mood_entry_influence ADD CONSTRAINT FK_EB05BFCEE88AF5D FOREIGN KEY (influence_id) REFERENCES influence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profiles ADD CONSTRAINT FK_8B308530A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reves ADD CONSTRAINT FK_C832E805F84A5F9B FOREIGN KEY (sommeil_id) REFERENCES sommeil (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sommeil ADD CONSTRAINT FK_AF87C033A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_faces ADD CONSTRAINT FK_2A1FD4D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE audit_logs DROP FOREIGN KEY FK_D62F28587F3AE16B');
        $this->addSql('ALTER TABLE auth_sessions DROP FOREIGN KEY FK_BE9A1A95A76ED395');
        $this->addSql('ALTER TABLE exercice_control DROP FOREIGN KEY FK_10605AE6A76ED395');
        $this->addSql('ALTER TABLE exercice_control DROP FOREIGN KEY FK_10605AE689D40298');
        $this->addSql('ALTER TABLE exercice_control DROP FOREIGN KEY FK_10605AE661A2AF17');
        $this->addSql('ALTER TABLE exercice_favorite DROP FOREIGN KEY FK_C05FEAFAA76ED395');
        $this->addSql('ALTER TABLE exercice_resource DROP FOREIGN KEY FK_140B903589D40298');
        $this->addSql('ALTER TABLE journal_entry DROP FOREIGN KEY FK_C8FAAE5AA76ED395');
        $this->addSql('ALTER TABLE mood_entry DROP FOREIGN KEY FK_22A0A36DA76ED395');
        $this->addSql('ALTER TABLE mood_entry_emotion DROP FOREIGN KEY FK_EB264C1975B2D1A3');
        $this->addSql('ALTER TABLE mood_entry_emotion DROP FOREIGN KEY FK_EB264C191EE4A582');
        $this->addSql('ALTER TABLE mood_entry_influence DROP FOREIGN KEY FK_EB05BFC75B2D1A3');
        $this->addSql('ALTER TABLE mood_entry_influence DROP FOREIGN KEY FK_EB05BFCEE88AF5D');
        $this->addSql('ALTER TABLE profiles DROP FOREIGN KEY FK_8B308530A76ED395');
        $this->addSql('ALTER TABLE reves DROP FOREIGN KEY FK_C832E805F84A5F9B');
        $this->addSql('ALTER TABLE sommeil DROP FOREIGN KEY FK_AF87C033A76ED395');
        $this->addSql('ALTER TABLE user_faces DROP FOREIGN KEY FK_2A1FD4D3A76ED395');
        $this->addSql('DROP TABLE audit_logs');
        $this->addSql('DROP TABLE auth_sessions');
        $this->addSql('DROP TABLE emotion');
        $this->addSql('DROP TABLE exercice');
        $this->addSql('DROP TABLE exercice_control');
        $this->addSql('DROP TABLE exercice_favorite');
        $this->addSql('DROP TABLE exercice_resource');
        $this->addSql('DROP TABLE influence');
        $this->addSql('DROP TABLE journal_entry');
        $this->addSql('DROP TABLE mood_entry');
        $this->addSql('DROP TABLE mood_entry_emotion');
        $this->addSql('DROP TABLE mood_entry_influence');
        $this->addSql('DROP TABLE profiles');
        $this->addSql('DROP TABLE reves');
        $this->addSql('DROP TABLE sommeil');
        $this->addSql('DROP TABLE user_faces');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
