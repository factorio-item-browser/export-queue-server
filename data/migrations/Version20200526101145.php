<?php declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Adds field priority to Job table.
 */
final class Version20200526101145 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Job ADD priority ENUM(\'admin\',\'user\',\'script\') NOT NULL COMMENT \'The priority of the export job.(DC2Type:job_priority)\' AFTER modNames, CHANGE status status ENUM(\'queued\',\'downloading\',\'processing\',\'uploading\',\'uploaded\',\'importing\',\'done\',\'error\') NOT NULL COMMENT \'The status of the export job.(DC2Type:job_status)\'');
        $this->addSql('UPDATE Job SET priority = \'user\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Job DROP priority');
    }
}
