<?php

namespace Tests\App\Service\ElasticSearch\ElasticSearchClientFactoryTest;

use Elasticsearch\{Client, ClientBuilder};
use PHPUnit\Framework\MockObject\RuntimeException;
use Tests\App\Service\ElasticSearch\ElasticSearchClientFactoryTest;

/**
 * Class GetClientTest
 *
 * @package Tests\App\Service\ElasticSearch\ElasticSearchClientFactoryTest
 */
class GetClientTest extends ElasticSearchClientFactoryTest
{
    /**
     * Test method on success.
     *
     * @covers \App\Service\ElasticSearch\ElasticSearchClientFactory::getClient()
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $testConfigs = [
            'config1' => 'test config 1',
            'config2' => 'test config 2',
        ];
        $clientBuilderMock = \Mockery::mock('overload:' . ClientBuilder::class);
        $clientMock = $this->createMock(Client::class);
        
        // Mocking
        $clientBuilderMock
            ->shouldReceive('fromConfig')
            ->withArgs([$testConfigs, true])
            ->andReturn($clientMock);
        
        //Execution
        $methodResult = $this->elasticSearchClientFactory->getClient($testConfigs);
        
        $this->assertEquals($clientMock, $methodResult);
    }
}