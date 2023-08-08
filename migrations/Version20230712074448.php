<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230712074448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_23A0E661BC7E6B6');
        $this->addSql('DROP INDEX IDX_23A0E661BC7E6B6 ON articles');
        $this->addSql('ALTER TABLE articles CHANGE writer_id writter_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_23A0E66679E91B3 FOREIGN KEY (writter_id) REFERENCES `member` (id)');
        $this->addSql('CREATE INDEX IDX_23A0E66679E91B3 ON articles (writter_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_23A0E66679E91B3');
        $this->addSql('DROP INDEX IDX_23A0E66679E91B3 ON articles');
        $this->addSql('ALTER TABLE articles CHANGE writter_id writer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_23A0E661BC7E6B6 FOREIGN KEY (writer_id) REFERENCES `member` (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_23A0E661BC7E6B6 ON articles (writer_id)');
    }
}
