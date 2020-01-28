<?php

namespace Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest;

use App\Service\ElasticSearch\ElasticSearchIndexManager;
use Mockery\Exception\RuntimeException;
use Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest;
use Tests\PrivateMethodInvocationTrait;

/**
 * Class GetClientTest
 *
 * @package Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest
 */
class GetClientTest extends ElasticSearchIndexManagerTest
{
    use PrivateMethodInvocationTrait;
    
    /**
     * Test method on success with creating client.
     *
     * @covers \App\Service\ElasticSearch\ElasticSearchIndexManager::getClient()
     * @throws RuntimeException
     */
    public function testSuccessOnCreatingClient()
    {
        // Mocking
        $this->elasticSearchClientFactoryInterfaceMock
            ->expects($this->once())
            ->method('getClient')
            ->willReturn($this->clientMock);
        
        //Execution
        $methodResult = $this->invokeMethod($this->elasticSearchIndexManager, 'getClient');
    
        // Asserting
        $this->assertEquals($this->clientMock, $methodResult);
    }
    
    /**
     * Test method on success with already created client.
     *
     * @covers \App\Service\ElasticSearch\ElasticSearchIndexManager::getClient()
     * @throws RuntimeException
     */
    public function testSuccessWithAlreadyCreatedClient()
    {
        // Test data
        $reflector = new \ReflectionClass(ElasticSearchIndexManager::class);
        $propertyClient = $reflector->getProperty('client');
        $propertyClient->setAccessible(true);

        // Mocking
        $this->elasticSearchClientFactoryInterfaceMock
            ->expects($this->never())
            ->method('getClient')
            ->willReturn($this->clientMock);
        
        //Execution
        $propertyClient->setValue($this->elasticSearchIndexManager, $this->clientMock);

        $methodResult = $this->invokeMethod($this->elasticSearchIndexManager, 'getClient');
    
        // Asserting
        $this->assertEquals($this->clientMock, $methodResult);
    }
}