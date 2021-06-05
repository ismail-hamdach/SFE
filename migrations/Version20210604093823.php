<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210604093823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projet ADD client_id INT DEFAULT NULL, CHANGE date_creation date_creation DATETIME NOT NULL');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA919EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_50159CA919EB6921 ON projet (client_id)');
        $this->addSql('ALTER TABLE tache CHANGE etat etat TINYINT(1) NOT NULL, CHANGE duree duree INT NOT NULL, CHANGE date_creation date_creation DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA919EB6921');
        $this->addSql('DROP INDEX IDX_50159CA919EB6921 ON projet');
        $this->addSql('ALTER TABLE projet DROP client_id, CHANGE date_creation date_creation DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE tache CHANGE etat etat TINYINT(1) DEFAULT NULL, CHANGE duree duree INT DEFAULT NULL, CHANGE date_creation date_creation DATETIME DEFAULT CURRENT_TIMESTAMP');
    }
}
