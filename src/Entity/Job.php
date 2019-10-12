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
     * @var int|null
     */
    protected $id;

    /**
     * The id of the combination to be exported.
     * @var UuidInterface
     */
    protected $combinationId;

    /**
     * The mod names to be exported as combination.
     * @var array|string[]
     */
    protected $modNames = [];

    /**
     * The status of the export job.
     * @var string
     */
    protected $status = '';

    /**
     * The node processing the export.
     * @var string
     */
    protected $node = '';

    /**
     * The error message in case the export job failed.
     * @var string
     */
    protected $errorMessage = '';

    /**
     * The time when the export job has was created.
     * @var DateTimeInterface|null
     */
    protected $creationTime = null;

    /**
     * The time when the export job was processed.
     * @var DateTimeInterface|null
     */
    protected $exportTime = null;

    /**
     * The time when the export job was imported into the database.
     * @var DateTimeInterface|null
     */
    protected $importTime = null;

    /**
     * Sets the id of the export job.
     * @param int|null $id
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the id of the export job.
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Sets the id of the combination to be exported.
     * @param UuidInterface $combinationId
     * @return $this
     */
    public function setCombinationId(UuidInterface $combinationId): self
    {
        $this->combinationId = $combinationId;
        return $this;
    }

    /**
     * Returns the id of the combination to be exported.
     * @return UuidInterface
     */
    public function getCombinationId(): UuidInterface
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
     * Sets the node processing the export.
     * @param string $node
     * @return $this
     */
    public function setNode(string $node): self
    {
        $this->node = $node;
        return $this;
    }

    /**
     * Returns the node processing the export.
     * @return string
     */
    public function getNode(): string
    {
        return $this->node;
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
