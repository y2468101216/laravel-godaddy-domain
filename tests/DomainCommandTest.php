<?php
/**
 * Created by PhpStorm.
 * User: lucas_chang
 * Date: 2018/2/14
 * Time: 上午 10:50
 */

namespace Tests;

use Y2468101216\Godaddy\Console\DomainCommand;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use \Mockery;
use Illuminate\Config\Repository as Config;

class DomainCommandTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testDomainCommandCreate()
    {
        $mockConfig = \Mockery::mock(Config::class);
        $mockConfig->shouldReceive('get')->andReturn([
            'key' => 'godaddy-key',
            'secret' => 'godaddy-secret',
            'domain' => 'test.com',
        ]);

        $expect = 0;
        $mockHandler = new MockHandler([
            new Response(200, [], '127.0.0.1'),
            new Response(200),
            new Response(200),
            new Response(200),
        ]);

        $handler = HandlerStack::create($mockHandler);
        $client = new Client(['handler' => $handler]);

        $command = Mockery::mock(DomainCommand::class)
            ->makePartial()
            ->shouldReceive([
                'info' => '',
                'option' => ''
            ])
            ->getMock();

        $actual = $command->handle($client, $mockConfig);

        $this->assertEquals($expect, $actual);
    }
}