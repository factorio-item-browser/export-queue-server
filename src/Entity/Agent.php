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
}
