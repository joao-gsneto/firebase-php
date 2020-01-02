<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Auth\AuthenticateUserWithRefreshToken;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Response;
use Kreait\Firebase\Auth\AuthenticateUserWithRefreshToken;
use Kreait\Firebase\Auth\AuthenticateUserWithRefreshToken\FailedToAuthenticateUserWithRefreshToken;
use Kreait\Firebase\Auth\AuthenticateUserWithRefreshToken\GuzzleApiClientHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * @internal
 */
final class GuzzleApiClientHandlerTest extends TestCase
{
    private $client;

    /** @var AuthenticateUserWithRefreshToken */
    private $action;

    /** @var GuzzleApiClientHandler */
    private $handler;

    protected function setUp()
    {
        $this->client = $this->prophesize(ClientInterface::class);
        $this->action = AuthenticateUserWithRefreshToken::fromValue('refresh-token');

        $this->handler = new AuthenticateUserWithRefreshToken\GuzzleApiClientHandler($this->client->reveal());
    }

    /** @test */
    public function it_handles_an_unknown_guzzle_error()
    {
        $this->client->send(Argument::cetera())->willThrow(new TransferException('Something happened'));

        $this->expectException(FailedToAuthenticateUserWithRefreshToken::class);
        $this->handler->handle($this->action);
    }

    /** @test */
    public function it_fails_on_unsuccessful_responses()
    {
        $this->client->send(Argument::cetera())->willReturn(new Response(400));

        $this->expectException(FailedToAuthenticateUserWithRefreshToken::class);
        $this->handler->handle($this->action);
    }

    /** @test */
    public function it_fails_on_unparseable_json_responses()
    {
        $this->client->send(Argument::cetera())->willReturn(new Response(200, [], ','));

        $this->expectException(FailedToAuthenticateUserWithRefreshToken::class);
        $this->handler->handle($this->action);
    }

    /** @test */
    public function exceptions_contain_the_action_and_a_response()
    {
        $this->client->send(Argument::cetera())->willReturn($response = new Response(400));

        try {
            $this->handler->handle($this->action);
            $this->fail('An exception should have been thrown');
        } catch (FailedToAuthenticateUserWithRefreshToken $e) {
            $this->assertSame($this->action, $e->action());
            $this->assertSame($response, $e->response());
        }
    }
}
