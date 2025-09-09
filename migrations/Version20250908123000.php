<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250908123000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add category relation to article and create article_tag join table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article ADD category_id INT DEFAULT NULL');
        $this->addSql('CREATE TABLE article_tag (article_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(article_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_5A8A6C8A9E6A7F20 ON article_tag (article_id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8A9F0F5C8B ON article_tag (tag_id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_5A8A6C8A3C3C4EBA FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE article_tag ADD CONSTRAINT FK_5A8A6C8A9E6A7F20 FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_tag ADD CONSTRAINT FK_5A8A6C8A9F0F5C8B FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article_tag DROP CONSTRAINT FK_5A8A6C8A9E6A7F20');
        $this->addSql('ALTER TABLE article_tag DROP CONSTRAINT FK_5A8A6C8A9F0F5C8B');
        $this->addSql('ALTER TABLE article DROP CONSTRAINT FK_5A8A6C8A3C3C4EBA');
        $this->addSql('DROP TABLE article_tag');
        $this->addSql('ALTER TABLE article DROP category_id');
    }
}
