<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\ExportQueue\Server\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use FactorioItemBrowser\ExportQueue\Server\Entity\Agent;
use FactorioItemBrowser\ExportQueue\Server\Repository\AgentRepository;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the AgentRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\ExportQueue\Server\Repository\AgentRepository
 */
class AgentRepositoryTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $agents = [
            $this->createMock(Agent::class),
            $this->createMock(Agent::class),
        ];

        $repository = new AgentRepository($agents);

        $this->assertSame($agents, $this->extractProperty($repository, 'agents'));
    }

    /**
     * Provides the data for the getByAccessKey test.
     * @return array
     */
    public function provideGetByAccessKey(): array
    {
        $agent1 = new Agent();
        $agent1->setAccessKey('foo');
        $agent2 = new Agent();
        $agent2->setAccessKey('bar');
        $agent3 = new Agent(); // Will never be returned as of missing access key.
        $agent3->setAccessKey('');

        return [
            [[$agent1, $agent2, $agent3], 'foo', $agent1],
            [[$agent1, $agent2, $agent3], 'bar', $agent2],
            [[$agent1, $agent2, $agent3], 'baz', null],
            [[$agent1, $agent2, $agent3], '', null],
        ];
    }

    /**
     * Tests the getByAccessKey method.
     * @param array|Agent[] $agents
     * @param string $accessKey
     * @param Agent|null $expectedResult
     * @covers ::getByAccessKey
     * @dataProvider provideGetByAccessKey
     */
    public function testGetByAccessKey(array $agents, string $accessKey, ?Agent $expectedResult): void
    {
        $repository = new AgentRepository($agents);
        $result = $repository->getByAccessKey($accessKey);

        $this->assertSame($expectedResult, $result);
    }
}
