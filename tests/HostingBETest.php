<?php

/**
 * @author Constan van Suchtelen van de Haere <constan@hostingbe.com>
 * @copyright 2023 HostingBE
 */

namespace App\Tests;

use App\Api\Logging\ApiLogger;
use App\Api\HostingBE;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

class HostingBETest extends PhpUnitTestCase {

private $loggername = 'hostingbe-app';
private $logger;
    
public function setUp(): void {
    $this->logger = (new ApiLogger)->create($this->loggername);
}

    /** @test */
    public function it_gets_tasks() {
        $api = new HostingBE($this->logger);
        $response = $api->getTasks();
        $contents = $response->getBody()->getContents();
        $this->assertCount(2, json_decode($contents));
        }

    /** @test */
    public function it_creates_a_task() {
        $api = new HostingBE($this->logger);
        $response = $api->createTask();
        $contents = $response->getBody()->getContents();
        $description = json_decode($contents)->description;
        $this->assertEquals('Write a blog post', $description);
        }
  
    /** @test */
    public function it_fails_to_get_tasks() {
        $api = new HostingBE($this->logger);
        $this->expectException(RequestException::class);
        $api->failedGetTasks();
        }
}

?>