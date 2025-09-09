<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250908135725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER INDEX idx_5a8a6c8a9e6a7f20 RENAME TO IDX_919694F97294869C');
        $this->addSql('ALTER INDEX idx_5a8a6c8a9f0f5c8b RENAME TO IDX_919694F9BAD26311');
        $this->addSql('ALTER TABLE tag ADD slug VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER INDEX idx_919694f97294869c RENAME TO idx_5a8a6c8a9e6a7f20');
        $this->addSql('ALTER INDEX idx_919694f9bad26311 RENAME TO idx_5a8a6c8a9f0f5c8b');
        $this->addSql('ALTER TABLE tag DROP slug');
    }
}
