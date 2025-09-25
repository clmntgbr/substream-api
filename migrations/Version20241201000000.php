<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241201000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create job table for CQRS command tracking';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE job (
            id UUID NOT NULL,
            status VARCHAR(20) NOT NULL,
            command_class VARCHAR(255) NOT NULL,
            command_data JSON DEFAULT NULL,
            metadata JSON DEFAULT NULL,
            error_message TEXT DEFAULT NULL,
            error_trace TEXT DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        
        $this->addSql('COMMENT ON COLUMN job.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN job.status IS \'(DC2Type:App\\Enum\\JobStatusEnum)\'');
        $this->addSql('COMMENT ON COLUMN job.command_data IS \'(DC2Type:json)\'');
        $this->addSql('COMMENT ON COLUMN job.metadata IS \'(DC2Type:json)\'');
        $this->addSql('COMMENT ON COLUMN job.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN job.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE job');
    }
}
