<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250829071914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD slug VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD cover VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD article_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD content TEXT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD is_published BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE comment DROP title');
        $this->addSql('ALTER TABLE comment DROP slug');
        $this->addSql('ALTER TABLE comment DROP catch');
        $this->addSql('ALTER TABLE comment DROP categories');
        $this->addSql('ALTER TABLE comment DROP cover');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9474526CF675F31B ON comment (author_id)');
        $this->addSql('CREATE INDEX IDX_9474526C7294869C ON comment (article_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C7294869C');
        $this->addSql('DROP INDEX IDX_9474526CF675F31B');
        $this->addSql('DROP INDEX IDX_9474526C7294869C');
        $this->addSql('ALTER TABLE comment ADD title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE comment ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE comment ADD catch VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE comment ADD categories TEXT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD cover VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE comment DROP author_id');
        $this->addSql('ALTER TABLE comment DROP article_id');
        $this->addSql('ALTER TABLE comment DROP content');
        $this->addSql('ALTER TABLE comment DROP is_published');
        $this->addSql('COMMENT ON COLUMN comment.categories IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE article DROP slug');
        $this->addSql('ALTER TABLE article DROP cover');
    }
}
