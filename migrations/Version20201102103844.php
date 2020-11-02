<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201102103844 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE campus (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etat (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lieu (id INT AUTO_INCREMENT NOT NULL, ville_id INT NOT NULL, nom VARCHAR(255) NOT NULL, rue VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, INDEX IDX_2F577D59A73F0036 (ville_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participant (id INT AUTO_INCREMENT NOT NULL, campus_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, administrateur TINYINT(1) NOT NULL, actif TINYINT(1) NOT NULL, INDEX IDX_D79F6B11AF5D55E1 (campus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sortie (id INT AUTO_INCREMENT NOT NULL, etat_id INT NOT NULL, lieu_id INT NOT NULL, campus_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, date_heure_debut DATETIME NOT NULL, duree INT NOT NULL, date_limite_inscription DATETIME NOT NULL, nb_inscription_max INT NOT NULL, infos_sortie VARCHAR(255) NOT NULL, INDEX IDX_3C3FD3F2D5E86FF (etat_id), INDEX IDX_3C3FD3F26AB213CC (lieu_id), INDEX IDX_3C3FD3F2AF5D55E1 (campus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ville (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, code_postal VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lieu ADD CONSTRAINT FK_2F577D59A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2D5E86FF FOREIGN KEY (etat_id) REFERENCES etat (id)');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F26AB213CC FOREIGN KEY (lieu_id) REFERENCES lieu (id)');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11AF5D55E1');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F2AF5D55E1');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F2D5E86FF');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F26AB213CC');
        $this->addSql('ALTER TABLE lieu DROP FOREIGN KEY FK_2F577D59A73F0036');
        $this->addSql('DROP TABLE campus');
        $this->addSql('DROP TABLE etat');
        $this->addSql('DROP TABLE lieu');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE sortie');
        $this->addSql('DROP TABLE ville');
    }
}
