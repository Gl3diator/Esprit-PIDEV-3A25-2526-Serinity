<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260415132928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reves (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(200) DEFAULT NULL, description LONGTEXT DEFAULT NULL, humeur VARCHAR(50) DEFAULT NULL, type_reve VARCHAR(50) DEFAULT NULL, intensite INT DEFAULT NULL, couleur TINYINT DEFAULT 1 NOT NULL, emotions VARCHAR(200) DEFAULT NULL, symboles LONGTEXT DEFAULT NULL, recurrent TINYINT DEFAULT 0 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, sommeil_id INT DEFAULT NULL, INDEX IDX_C832E805F84A5F9B (sommeil_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sommeil (id INT AUTO_INCREMENT NOT NULL, date_nuit DATE DEFAULT NULL, heure_coucher VARCHAR(255) DEFAULT NULL, heure_reveil VARCHAR(255) DEFAULT NULL, qualite VARCHAR(50) DEFAULT NULL, commentaire LONGTEXT DEFAULT NULL, duree_sommeil DOUBLE PRECISION DEFAULT NULL, interruptions INT DEFAULT NULL, humeur_reveil VARCHAR(50) DEFAULT NULL, environnement VARCHAR(100) DEFAULT NULL, temperature DOUBLE PRECISION DEFAULT NULL, bruit_niveau VARCHAR(50) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, user_id INT DEFAULT 1 NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE reves ADD CONSTRAINT FK_C832E805F84A5F9B FOREIGN KEY (sommeil_id) REFERENCES sommeil (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mood_entry CHANGE moment_type moment_type ENUM(\'MOMENT\',\'DAY\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reves DROP FOREIGN KEY FK_C832E805F84A5F9B');
        $this->addSql('DROP TABLE reves');
        $this->addSql('DROP TABLE sommeil');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE mood_entry CHANGE moment_type moment_type ENUM(\'MOMENT\', \'DAY\') DEFAULT NULL');
    }
}
