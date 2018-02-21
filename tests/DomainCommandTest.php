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
        $mock = \Mockery::mock('overload:'.Config::class);
        $mock->shouldReceive('get')->andReturn([
            'key' => 'godaddy-key',
            'secret' => 'godaddy-secret',
            'domain' => 'test.com',
        ]);

        $expect = 0;
        $mock = new MockHandler([
            new Response(200, [], '127.0.0.1'),
            new Response(200),
            new Response(200),
            new Response(200),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $command = Mockery::mock(DomainCommand::class)
            ->makePartial()
            ->shouldReceive([
                'info' => '',
                'option' => ''
            ])
            ->getMock();

        $actual = $command->handle($client);

        $this->assertEquals($expect, $actual);
    }
}