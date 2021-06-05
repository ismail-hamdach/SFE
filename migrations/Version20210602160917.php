<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210602160917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projet ADD date_creation DATETIME NOT NULL, ADD datetime DATETIME NOT NULL');
        $this->addSql('ALTER TABLE tache CHANGE etat etat TINYINT(1) NOT NULL, CHANGE duree duree INT NOT NULL, CHANGE date_creation date_creation DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projet DROP date_creation, DROP datetime');
        $this->addSql('ALTER TABLE tache CHANGE etat etat TINYINT(1) DEFAULT NULL, CHANGE duree duree INT DEFAULT NULL, CHANGE date_creation date_creation DATETIME DEFAULT CURRENT_TIMESTAMP');
    }
}
