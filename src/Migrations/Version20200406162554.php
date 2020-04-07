<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200406162554 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, musician_id INT DEFAULT NULL, jobtitle VARCHAR(255) DEFAULT NULL, startingfrom DATE DEFAULT NULL, endedin DATE DEFAULT NULL, currentornot VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, INDEX IDX_FBD8E0F89523AA8A (musician_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, musician_id INT DEFAULT NULL, county VARCHAR(255) DEFAULT NULL, subcounty VARCHAR(255) DEFAULT NULL, INDEX IDX_5E9E89CB9523AA8A (musician_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE education (id INT AUTO_INCREMENT NOT NULL, musician_id INT DEFAULT NULL, institution VARCHAR(255) DEFAULT NULL, fromyear DATE DEFAULT NULL, toyear DATE DEFAULT NULL, coursename VARCHAR(255) DEFAULT NULL, degree VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, INDEX IDX_DB0A5ED29523AA8A (musician_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE advert (id INT AUTO_INCREMENT NOT NULL, task VARCHAR(255) DEFAULT NULL, skill VARCHAR(255) DEFAULT NULL, education VARCHAR(255) DEFAULT NULL, courselevel VARCHAR(255) DEFAULT NULL, jobdescription LONGTEXT DEFAULT NULL, deadline DATE DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_to_be_offered (id INT AUTO_INCREMENT NOT NULL, musician_id INT DEFAULT NULL, jobtitle VARCHAR(255) DEFAULT NULL, medium VARCHAR(255) DEFAULT NULL, INDEX IDX_9C7EF6019523AA8A (musician_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE specialty (id INT AUTO_INCREMENT NOT NULL, instrumentorskill VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skill (id INT AUTO_INCREMENT NOT NULL, musician_id INT DEFAULT NULL, skillname VARCHAR(255) DEFAULT NULL, levelofskill VARCHAR(255) DEFAULT NULL, experienceofskill VARCHAR(255) DEFAULT NULL, INDEX IDX_5E3DE4779523AA8A (musician_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F89523AA8A FOREIGN KEY (musician_id) REFERENCES musician (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB9523AA8A FOREIGN KEY (musician_id) REFERENCES musician (id)');
        $this->addSql('ALTER TABLE education ADD CONSTRAINT FK_DB0A5ED29523AA8A FOREIGN KEY (musician_id) REFERENCES musician (id)');
        $this->addSql('ALTER TABLE job_to_be_offered ADD CONSTRAINT FK_9C7EF6019523AA8A FOREIGN KEY (musician_id) REFERENCES musician (id)');
        $this->addSql('ALTER TABLE skill ADD CONSTRAINT FK_5E3DE4779523AA8A FOREIGN KEY (musician_id) REFERENCES musician (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE education');
        $this->addSql('DROP TABLE advert');
        $this->addSql('DROP TABLE job_to_be_offered');
        $this->addSql('DROP TABLE specialty');
        $this->addSql('DROP TABLE skill');
    }
}
