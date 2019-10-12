<?php declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191012111056 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Job (id BINARY(16) NOT NULL COMMENT \'The id of the export job.(DC2Type:uuid_binary)\', combinationId BINARY(16) NOT NULL COMMENT \'The id of the combination to be exported.(DC2Type:uuid_binary)\', modNames JSON NOT NULL COMMENT \'The mod names to be exported as combination.\', status ENUM(\'queued\',\'downloading\',\'processing\',\'uploading\',\'uploaded\',\'importing\',\'done\',\'error\') NOT NULL COMMENT \'The status of the export job.\', errorMessage LONGTEXT NOT NULL COMMENT \'The error message in case the export job failed.\', creator VARCHAR(255) NOT NULL COMMENT \'The creator of the export job.\', creationTime DATETIME DEFAULT NULL COMMENT \'The time when the export job has was created.\', exporter VARCHAR(255) NOT NULL COMMENT \'The exporter processing the job.\', exportTime DATETIME DEFAULT NULL COMMENT \'The time when the export job was processed.\', importer VARCHAR(255) NOT NULL COMMENT \'The importer adding the data to the database.\', importTime DATETIME DEFAULT NULL COMMENT \'The time when the export job was imported into the database.\', INDEX idx_combinationId (combinationId), INDEX idx_status (status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'The table holding the export jobs.\' ');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE Job');
    }
}
