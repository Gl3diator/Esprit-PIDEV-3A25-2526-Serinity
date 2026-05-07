<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260506212350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE consultation (id INT AUTO_INCREMENT NOT NULL, date_consultation DATETIME NOT NULL, diagnostic LONGTEXT DEFAULT NULL, prescription LONGTEXT DEFAULT NULL, notes LONGTEXT DEFAULT NULL, rapport_id INT NOT NULL, rendez_vous_id INT DEFAULT NULL, doctor_id VARCHAR(36) NOT NULL, INDEX IDX_964685A61DFBCC46 (rapport_id), UNIQUE INDEX UNIQ_964685A691EF7EAA (rendez_vous_id), INDEX IDX_964685A687F4FB17 (doctor_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rapport (id INT AUTO_INCREMENT NOT NULL, date_creation DATE NOT NULL, resume_general LONGTEXT DEFAULT NULL, patient_id VARCHAR(36) NOT NULL, UNIQUE INDEX UNIQ_BE34A09C6B899279 (patient_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rendez_vous (id INT AUTO_INCREMENT NOT NULL, motif VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, date_time DATETIME DEFAULT NULL, status VARCHAR(30) NOT NULL, created_at DATETIME NOT NULL, proposed_date_time DATETIME DEFAULT NULL, doctor_note LONGTEXT DEFAULT NULL, patient_id VARCHAR(36) NOT NULL, doctor_id VARCHAR(36) NOT NULL, INDEX IDX_65E8AA0A6B899279 (patient_id), INDEX IDX_65E8AA0A87F4FB17 (doctor_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A61DFBCC46 FOREIGN KEY (rapport_id) REFERENCES rapport (id)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A691EF7EAA FOREIGN KEY (rendez_vous_id) REFERENCES rendez_vous (id)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A687F4FB17 FOREIGN KEY (doctor_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE rapport ADD CONSTRAINT FK_BE34A09C6B899279 FOREIGN KEY (patient_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A6B899279 FOREIGN KEY (patient_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0A87F4FB17 FOREIGN KEY (doctor_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_faces DROP INDEX IDX_2A1FD4D3A76ED395, ADD UNIQUE INDEX uniq_user_faces_user (user_id)');
        $this->addSql('ALTER TABLE users ADD risk_level VARCHAR(20) DEFAULT NULL, CHANGE is_two_factor_enabled is_two_factor_enabled TINYINT NOT NULL');
        $this->addSql('DROP INDEX uniq_users_google_id ON users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E976F5C865 ON users (google_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A61DFBCC46');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A691EF7EAA');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A687F4FB17');
        $this->addSql('ALTER TABLE rapport DROP FOREIGN KEY FK_BE34A09C6B899279');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A6B899279');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0A87F4FB17');
        $this->addSql('DROP TABLE consultation');
        $this->addSql('DROP TABLE rapport');
        $this->addSql('DROP TABLE rendez_vous');
        $this->addSql('ALTER TABLE users DROP risk_level, CHANGE is_two_factor_enabled is_two_factor_enabled TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('DROP INDEX uniq_1483a5e976f5c865 ON users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_users_google_id ON users (google_id)');
        $this->addSql('ALTER TABLE user_faces DROP INDEX uniq_user_faces_user, ADD INDEX IDX_2A1FD4D3A76ED395 (user_id)');
    }
}
