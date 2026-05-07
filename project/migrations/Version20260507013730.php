<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260507013730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exercice ADD benefits LONGTEXT DEFAULT NULL, ADD tips LONGTEXT DEFAULT NULL, ADD theme VARCHAR(50) DEFAULT NULL, ADD guided_instructions JSON DEFAULT NULL');
        $this->addSql('DROP INDEX idx_sommeil_user_date ON sommeil');
        $this->addSql('ALTER TABLE sommeil CHANGE date_nuit date_nuit DATE DEFAULT NULL, CHANGE heure_coucher heure_coucher VARCHAR(255) DEFAULT NULL, CHANGE heure_reveil heure_reveil VARCHAR(255) DEFAULT NULL, CHANGE qualite qualite VARCHAR(50) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE users DROP risk_confidence, DROP risk_prediction, DROP risk_evaluated_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exercice DROP benefits, DROP tips, DROP theme, DROP guided_instructions');
        $this->addSql('ALTER TABLE sommeil CHANGE date_nuit date_nuit DATE NOT NULL, CHANGE heure_coucher heure_coucher VARCHAR(10) NOT NULL, CHANGE heure_reveil heure_reveil VARCHAR(10) NOT NULL, CHANGE qualite qualite VARCHAR(50) NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('CREATE INDEX idx_sommeil_user_date ON sommeil (user_id, date_nuit)');
        $this->addSql('ALTER TABLE users ADD risk_confidence DOUBLE PRECISION DEFAULT NULL, ADD risk_prediction INT DEFAULT NULL, ADD risk_evaluated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
