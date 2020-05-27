<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Entity;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity representing an actual export job.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Job
{
    /**
     * The id of the export job.
     * @var UuidInterface|null
     */
    protected $id;

    /**
     * The id of the combination to be exported.
     * @var UuidInterface|null
     */
    protected $combinationId;

    /**
     * The mod names to be exported as combination.
     * @var array|string[]
     */
    protected $modNames = [];

    /**
     * The priority of the export job.
     * @var string
     */
    protected $priority = '';

    /**
     * The status of the export job.
     * @var string
     */
    protected $status = '';

    /**
     * The error message in case the export job failed.
     * @var string
     */
    protected $errorMessage = '';

    /**
     * The creator of the export job.
     * @var string
     */
    protected $creator = '';

    /**
     * The time when the export job has was created.
     * @var DateTimeInterface|null
     */
    protected $creationTime = null;

    /**
     * The exporter processing the job.
     * @var string
     */
    protected $exporter = '';

    /**
     * The time when the export job was processed.
     * @var DateTimeInterface|null
     */
    protected $exportTime = null;

    /**
     * The importer adding the data to the database.
     * @var string
     */
    protected $importer = '';

    /**
     * The time when the export job was imported into the database.
     * @var DateTimeInterface|null
     */
    protected $importTime = null;

    /**
     * Sets the id of the export job.
     * @param UuidInterface|null $id
     * @return $this
     */
    public function setId(?UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the id of the export job.
     * @return UuidInterface|null
     */
    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    /**
     * Sets the id of the combination to be exported.
     * @param UuidInterface|null $combinationId
     * @return $this
     */
    public function setCombinationId(?UuidInterface $combinationId): self
    {
        $this->combinationId = $combinationId;
        return $this;
    }

    /**
     * Returns the id of the combination to be exported.
     * @return UuidInterface|null
     */
    public function getCombinationId(): ?UuidInterface
    {
        return $this->combinationId;
    }

    /**
     * Sets the mod names to be exported as combination.
     * @param array|string[] $modNames
     * @return $this
     */
    public function setModNames(array $modNames): self
    {
        $this->modNames = $modNames;
        return $this;
    }

    /**
     * Returns the mod names to be exported as combination.
     * @return array|string[]
     */
    public function getModNames(): array
    {
        return $this->modNames;
    }

    /**
     * Sets the priority of the export job.
     * @param string $priority
     * @return $this
     */
    public function setPriority(string $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Returns the priority of the export job.
     * @return string
     */
    public function getPriority(): string
    {
        return $this->priority;
    }

    /**
     * Sets the status of the export job.
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Returns the status of the export job.
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Sets the error message in case the export job failed.
     * @param string $errorMessage
     * @return $this
     */
    public function setErrorMessage(string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    /**
     * Returns the error message in case the export job failed.
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * Sets the creator of the export job.
     * @param string $creator
     * @return $this
     */
    public function setCreator(string $creator): self
    {
        $this->creator = $creator;
        return $this;
    }

    /**
     * Returns the creator of the export job.
     * @return string
     */
    public function getCreator(): string
    {
        return $this->creator;
    }

    /**
     * Sets the time when the export job has was created.
     * @param DateTimeInterface|null $creationTime
     * @return $this
     */
    public function setCreationTime(?DateTimeInterface $creationTime): self
    {
        $this->creationTime = $creationTime;
        return $this;
    }

    /**
     * Returns the time when the export job has was created.
     * @return DateTimeInterface|null
     */
    public function getCreationTime(): ?DateTimeInterface
    {
        return $this->creationTime;
    }

    /**
     * Sets the exporter processing the job.
     * @param string $exporter
     * @return $this
     */
    public function setExporter(string $exporter): self
    {
        $this->exporter = $exporter;
        return $this;
    }

    /**
     * Returns the exporter processing the job.
     * @return string
     */
    public function getExporter(): string
    {
        return $this->exporter;
    }

    /**
     * Sets the time when the export job was processed.
     * @param DateTimeInterface|null $exportTime
     * @return $this
     */
    public function setExportTime(?DateTimeInterface $exportTime): self
    {
        $this->exportTime = $exportTime;
        return $this;
    }

    /**
     * Returns the time when the export job was processed.
     * @return DateTimeInterface|null
     */
    public function getExportTime(): ?DateTimeInterface
    {
        return $this->exportTime;
    }

    /**
     * Sets the importer adding the data to the database.
     * @param string $importer
     * @return $this
     */
    public function setImporter(string $importer): self
    {
        $this->importer = $importer;
        return $this;
    }

    /**
     * Returns the importer adding the data to the database.
     * @return string
     */
    public function getImporter(): string
    {
        return $this->importer;
    }

    /**
     * Sets the time when the export job was imported into the database.
     * @param DateTimeInterface|null $importTime
     * @return $this
     */
    public function setImportTime(?DateTimeInterface $importTime): self
    {
        $this->importTime = $importTime;
        return $this;
    }

    /**
     * Returns the time when the export job was imported into the database.
     * @return DateTimeInterface|null
     */
    public function getImportTime(): ?DateTimeInterface
    {
        return $this->importTime;
    }
}
