<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\ExportQueue\Server\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use FactorioItemBrowser\ExportQueue\Server\Constant\ConfigKey;
use FactorioItemBrowser\ExportQueue\Server\Entity\Agent;
use FactorioItemBrowser\ExportQueue\Server\Repository\AgentRepository;
use FactorioItemBrowser\ExportQueue\Server\Repository\AgentRepositoryFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the AgentRepositoryFactory class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\ExportQueue\Server\Repository\AgentRepositoryFactory
 */
class AgentRepositoryFactoryTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the invoking.
     * @covers ::__invoke
     */
    public function testInvoke(): void
    {
        $config = [
            ConfigKey::PROJECT => [
                ConfigKey::EXPORT_QUEUE_SERVER => [
                    ConfigKey::AGENTS => [
                        ['abc' => 'def'],
                        ['ghi' => 'jkl'],
                    ],
                ],
            ],
        ];

        /* @var Agent&MockObject $agent1 */
        $agent1 = $this->createMock(Agent::class);
        /* @var Agent&MockObject $agent2 */
        $agent2 = $this->createMock(Agent::class);

        $expectedResult = new AgentRepository([$agent1, $agent2]);

        /* @var ContainerInterface&MockObject $container */
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
                  ->method('get')
                  ->with($this->identicalTo('config'))
                  ->willReturn($config);


        /* @var AgentRepositoryFactory&MockObject $factory */
        $factory = $this->getMockBuilder(AgentRepositoryFactory::class)
                        ->onlyMethods(['createAgent'])
                        ->getMock();
        $factory->expects($this->exactly(2))
                ->method('createAgent')
                ->withConsecutive(
                    [$this->identicalTo(['abc' => 'def'])],
                    [$this->identicalTo(['ghi' => 'jkl'])]
                )
                ->willReturnOnConsecutiveCalls(
                    $agent1,
                    $agent2
                );

        $result = $factory($container, AgentRepository::class);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Tests the createAgent method.
     * @covers ::createAgent
     * @throws ReflectionException
     */
    public function testCreateAgent(): void
    {
        $config = [
            ConfigKey::AGENT_NAME => 'abc',
            ConfigKey::AGENT_ACCESS_KEY => 'def',
            ConfigKey::AGENT_CAN_CREATE => true,
            ConfigKey::AGENT_CAN_EXPORT => true,
            ConfigKey::AGENT_CAN_IMPORT => true,
        ];
        $expectedResult = new Agent();
        $expectedResult->setName('abc')
                       ->setAccessKey('def')
                       ->setCanCreate(true)
                       ->setCanExport(true)
                       ->setCanImport(true);

        $factory = new AgentRepositoryFactory();
        $result = $this->invokeMethod($factory, 'createAgent', $config);

        $this->assertEquals($expectedResult, $result);
    }
}
