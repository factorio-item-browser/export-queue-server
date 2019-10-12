<?php

declare(strict_types=1);

namespace FactorioItemBrowser\ExportQueue\Server\Repository;

use FactorioItemBrowser\ExportQueue\Server\Constant\ConfigKey;
use FactorioItemBrowser\ExportQueue\Server\Entity\Agent;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * The factory of the AgentRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class AgentRepositoryFactory implements FactoryInterface
{
    /**
     * Creates the agent repository.
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return AgentRepository
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AgentRepository
    {
        $config = $container->get('config');
        $agentsConfig = $config[ConfigKey::PROJECT][ConfigKey::EXPORT_QUEUE_SERVER][ConfigKey::AGENTS] ?? [];

        $agents = [];
        foreach ($agentsConfig as $agentConfig) {
            $agents[] = $this->createAgent($agentConfig);
        }

        return new AgentRepository($agents);
    }

    /**
     * Creates an agent entity from its configuration.
     * @param array $agentConfig
     * @return Agent
     */
    protected function createAgent(array $agentConfig): Agent
    {
        $agent = new Agent();
        $agent->setName($agentConfig[ConfigKey::AGENT_NAME] ?? '')
              ->setAccessKey($agentConfig[ConfigKey::AGENT_ACCESS_KEY] ?? '')
              ->setCanCreate((bool) ($agentConfig[ConfigKey::AGENT_CAN_CREATE] ?? false))
              ->setCanExport((bool) ($agentConfig[ConfigKey::AGENT_CAN_EXPORT] ?? false))
              ->setCanImport((bool) ($agentConfig[ConfigKey::AGENT_CAN_IMPORT] ?? false));
        return $agent;
    }
}
