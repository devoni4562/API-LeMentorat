<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230629091150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, writer_id INT DEFAULT NULL, category_id INT NOT NULL, image VARCHAR(255) DEFAULT NULL, paragraph LONGTEXT NOT NULL, video VARCHAR(255) DEFAULT NULL, INDEX IDX_23A0E661BC7E6B6 (writer_id), INDEX IDX_23A0E6612469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `member` (id INT AUTO_INCREMENT NOT NULL, job_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(50) NOT NULL, pseudo VARCHAR(20) DEFAULT NULL, avatar VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_70E4FA78E7927C74 (email), INDEX IDX_70E4FA78BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_23A0E661BC7E6B6 FOREIGN KEY (writer_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_23A0E6612469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE `member` ADD CONSTRAINT FK_70E4FA78BE04EA9 FOREIGN KEY (job_id) REFERENCES role (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_23A0E661BC7E6B6');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_23A0E6612469DE2');
        $this->addSql('ALTER TABLE `member` DROP FOREIGN KEY FK_70E4FA78BE04EA9');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE `member`');
    }
}
