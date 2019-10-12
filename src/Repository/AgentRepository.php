<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Repository;

use FactorioItemBrowser\ExportQueue\Server\Entity\Agent;

/**
 * The repository managing the agents.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class AgentRepository
{
    /**
     * The agents.
     * @var array|Agent[]
     */
    protected $agents;

    /**
     * Initializes the repository.
     * @param array|Agent[] $agents
     */
    public function __construct(array $agents)
    {
        $this->agents = $agents;
    }

    /**
     * Returns the agent with the specified access key.
     * @param string $accessKey
     * @return Agent|null
     */
    public function getByAccessKey(string $accessKey): ?Agent
    {
        if ($accessKey !== '') {
            foreach ($this->agents as $agent) {
                if ($accessKey === $agent->getAccessKey()) {
                    return $agent;
                }
            }
        }

        return null;
    }
}
