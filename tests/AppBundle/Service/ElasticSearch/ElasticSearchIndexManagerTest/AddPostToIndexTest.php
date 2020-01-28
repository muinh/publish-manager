<?php

namespace Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest;

use Mockery\Exception\RuntimeException;
use Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest;
use Tests\App\Stub\ElasticSearchConstantBag;

/**
 * Class AddPostToIndexTest
 *
 * @package Tests\App\Service\ElasticSearch\ElasticSearchIndexManagerTest
 */
class AddPostToIndexTest extends ElasticSearchIndexManagerTest
{
    /**
     * @var array
     */
    protected $mockMethods = ['getClient'];

    /**
     * Test method on success with creating client.
     *
     * @covers \App\Service\ElasticSearch\ElasticSearchIndexManager::addPostToIndex()
     * @throws RuntimeException
     */
    public function testSuccess()
    {
        // Test data
        $testPostUrlHash = 'test-id';
        $testPostData = [
            'post_name' => 'test name',
            'post_content' => 'test content'
        ];
        $params = [
            'index' => $this->projectIndex,
            'type' => ElasticSearchConstantBag::TYPE_POST,
            'id' => $testPostUrlHash,
            'body' => $testPostData,
        ];
    
        // Mocking
        $this->elasticSearchIndexManager
            ->expects($this->once())
            ->method('getClient')
            ->willReturn($this->clientMock);
    
        $this->clientMock
            ->expects($this->once())
            ->method('index')
            ->with($params)
            ->willReturn($testPostData);
        
        //Execution
        $methodResult = $this->elasticSearchIndexManager->addPostToIndex($testPostUrlHash, $testPostData);
    
        // Asserting
        $this->assertEquals($testPostData, $methodResult);
    }
}