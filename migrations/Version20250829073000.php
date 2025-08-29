<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250829073000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Populate missing slugs for articles from title';
    }

    public function up(Schema $schema): void
    {
        // This SQL will generate a simple slug from title by lowercasing, replacing non-word chars with '-', trimming, and appending id to ensure uniqueness when needed.
        $this->addSql("UPDATE article SET slug = LOWER(REGEXP_REPLACE(title, '[^A-Za-z0-9]+', '-', 'g')) WHERE slug IS NULL OR slug = ''");
        $this->addSql("UPDATE article SET slug = slug || '-' || id WHERE slug IS NOT NULL AND slug NOT LIKE '%' || id");
    }

    public function down(Schema $schema): void
    {
        // No-op: don't revert slugs
    }
}
