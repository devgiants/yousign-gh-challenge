<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200617143409 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE github_repo CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('DROP INDEX unique_index ON commit');
        $this->addSql('ALTER TABLE commit ADD github_repo_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE commit ADD CONSTRAINT FK_4ED42EADB23C03A9 FOREIGN KEY (github_repo_id) REFERENCES github_repo (id)');
        $this->addSql('CREATE INDEX IDX_4ED42EADB23C03A9 ON commit (github_repo_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_index ON commit (sha, github_repo_id, push_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commit DROP FOREIGN KEY FK_4ED42EADB23C03A9');
        $this->addSql('DROP INDEX IDX_4ED42EADB23C03A9 ON commit');
        $this->addSql('DROP INDEX unique_index ON commit');
        $this->addSql('ALTER TABLE commit DROP github_repo_id');
        $this->addSql('CREATE UNIQUE INDEX unique_index ON commit (sha, push_id)');
        $this->addSql('ALTER TABLE github_repo CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
