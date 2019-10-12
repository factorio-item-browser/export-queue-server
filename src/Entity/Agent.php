<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Entity;

/**
 * THe entity representing an agent of the export queue server.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Agent
{
    /**
     * The name of the agent.
     * @var string
     */
    protected $name = '';

    /**
     * The access key of the agent.
     * @var string
     */
    protected $accessKey = '';

    /**
     * Whether the agent can create new export jobs.
     * @var bool
     */
    protected $canCreate = false;

    /**
     * Whether the agent can process export jobs.
     * @var bool
     */
    protected $canExport = false;

    /**
     * Whether the agent can import data into the database.
     * @var bool
     */
    protected $canImport = false;

    /**
     * Sets the name of the agent.
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of the agent.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the access key of the agent.
     * @param string $accessKey
     * @return $this
     */
    public function setAccessKey(string $accessKey): self
    {
        $this->accessKey = $accessKey;
        return $this;
    }

    /**
     * Returns the access key of the agent.
     * @return string
     */
    public function getAccessKey(): string
    {
        return $this->accessKey;
    }

    /**
     * Sets whether the agent can create new export jobs.
     * @param bool $canCreate
     * @return $this
     */
    public function setCanCreate(bool $canCreate): self
    {
        $this->canCreate = $canCreate;
        return $this;
    }

    /**
     * Returns whether the agent can create new export jobs.
     * @return bool
     */
    public function getCanCreate(): bool
    {
        return $this->canCreate;
    }

    /**
     * Sets whether the agent can process export jobs.
     * @param bool $canExport
     * @return $this
     */
    public function setCanExport(bool $canExport): self
    {
        $this->canExport = $canExport;
        return $this;
    }

    /**
     * Returns whether the agent can process export jobs.
     * @return bool
     */
    public function getCanExport(): bool
    {
        return $this->canExport;
    }

    /**
     * Sets whether the agent can import data into the database.
     * @param bool $canImport
     * @return $this
     */
    public function setCanImport(bool $canImport): self
    {
        $this->canImport = $canImport;
        return $this;
    }

    /**
     * Returns whether the agent can import data into the database.
     * @return bool
     */
    public function getCanImport(): bool
    {
        return $this->canImport;
    }
}
