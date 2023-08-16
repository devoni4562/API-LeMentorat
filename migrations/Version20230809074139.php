<?php

    declare(strict_types=1);

    namespace DoctrineMigrations;

    use Doctrine\DBAL\Schema\Schema;
    use Doctrine\Migrations\AbstractMigration;

    /**
     * Auto-generated Migration: Please modify to your needs!
     */
    final class Version20230809074139 extends AbstractMigration
    {
        public function getDescription(): string
        {
            return '';
        }

        public function up(Schema $schema): void
        {
            // this up() migration is auto-generated, please modify it to your needs
            $this->addSql('CREATE SEQUENCE article_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
            $this->addSql('CREATE SEQUENCE case_study_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
            $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
            $this->addSql('CREATE SEQUENCE "member_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
            $this->addSql('CREATE SEQUENCE paragraph_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
            $this->addSql('CREATE SEQUENCE role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
            $this->addSql('CREATE TABLE article (id INT NOT NULL, writter_id INT DEFAULT NULL, category_id INT NOT NULL, image VARCHAR(255) DEFAULT NULL, video VARCHAR(255) DEFAULT NULL, date DATE NOT NULL, title VARCHAR(255) NOT NULL, summary TEXT NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_23A0E66679E91B3 ON article (writter_id)');
            $this->addSql('CREATE INDEX IDX_23A0E6612469DE2 ON article (category_id)');
            $this->addSql('CREATE TABLE case_study (id INT NOT NULL, link VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, html_id VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE TABLE category (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE TABLE "member" (id INT NOT NULL, job_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(50) NOT NULL, pseudo VARCHAR(20) DEFAULT NULL, avatar VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_70E4FA78E7927C74 ON "member" (email)');
            $this->addSql('CREATE INDEX IDX_70E4FA78BE04EA9 ON "member" (job_id)');
            $this->addSql('CREATE TABLE paragraph (id INT NOT NULL, article_id INT NOT NULL, title VARCHAR(255) NOT NULL, picture VARCHAR(255) DEFAULT NULL, text TEXT NOT NULL, link VARCHAR(255) DEFAULT NULL, link_text VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_7DD398627294869C ON paragraph (article_id)');
            $this->addSql('CREATE TABLE role (id INT NOT NULL, name VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
            $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66679E91B3 FOREIGN KEY (writter_id) REFERENCES "member" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE "member" ADD CONSTRAINT FK_70E4FA78BE04EA9 FOREIGN KEY (job_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE paragraph ADD CONSTRAINT FK_7DD398627294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        }

        public function down(Schema $schema): void
        {
            // this down() migration is auto-generated, please modify it to your needs
            $this->addSql('CREATE SCHEMA public');
            $this->addSql('CREATE SCHEMA heroku_ext');
            $this->addSql('DROP SEQUENCE article_id_seq CASCADE');
            $this->addSql('DROP SEQUENCE case_study_id_seq CASCADE');
            $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
            $this->addSql('DROP SEQUENCE "member_id_seq" CASCADE');
            $this->addSql('DROP SEQUENCE paragraph_id_seq CASCADE');
            $this->addSql('DROP SEQUENCE role_id_seq CASCADE');
            $this->addSql('ALTER TABLE article DROP CONSTRAINT FK_23A0E66679E91B3');
            $this->addSql('ALTER TABLE article DROP CONSTRAINT FK_23A0E6612469DE2');
            $this->addSql('ALTER TABLE "member" DROP CONSTRAINT FK_70E4FA78BE04EA9');
            $this->addSql('ALTER TABLE paragraph DROP CONSTRAINT FK_7DD398627294869C');
            $this->addSql('DROP TABLE article');
            $this->addSql('DROP TABLE case_study');
            $this->addSql('DROP TABLE category');
            $this->addSql('DROP TABLE "member"');
            $this->addSql('DROP TABLE paragraph');
            $this->addSql('DROP TABLE role');
        }
    }
