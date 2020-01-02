<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Auth\AuthenticateUserWithCustomToken;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Response;
use Kreait\Firebase\Auth\AuthenticateUserWithCustomToken;
use Kreait\Firebase\Auth\AuthenticateUserWithCustomToken\FailedToAuthenticateUserWithCustomToken;
use Kreait\Firebase\Auth\AuthenticateUserWithCustomToken\GuzzleApiClientHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * @internal
 */
final class GuzzleApiClientHandlerTest extends TestCase
{
    private $client;

    /** @var AuthenticateUserWithCustomToken */
    private $action;

    /** @var GuzzleApiClientHandler */
    private $handler;

    protected function setUp()
    {
        $this->client = $this->prophesize(ClientInterface::class);
        $this->action = AuthenticateUserWithCustomToken::fromValue('custom-token');

        $this->handler = new AuthenticateUserWithCustomToken\GuzzleApiClientHandler($this->client->reveal());
    }

    /** @test */
    public function it_handles_an_unknown_guzzle_error()
    {
        $this->client->send(Argument::cetera())->willThrow(new TransferException('Something happened'));

        $this->expectException(AuthenticateUserWithCustomToken\FailedToAuthenticateUserWithCustomToken::class);
        $this->handler->handle($this->action);
    }

    /** @test */
    public function it_fails_on_unsuccessful_responses()
    {
        $this->client->send(Argument::cetera())->willReturn(new Response(400));

        $this->expectException(FailedToAuthenticateUserWithCustomToken::class);
        $this->handler->handle($this->action);
    }

    /** @test */
    public function it_fails_on_unparseable_json_responses()
    {
        $this->client->send(Argument::cetera())->willReturn(new Response(200, [], ','));

        $this->expectException(FailedToAuthenticateUserWithCustomToken::class);
        $this->handler->handle($this->action);
    }

    /** @test */
    public function exceptions_contain_the_action_and_a_response()
    {
        $this->client->send(Argument::cetera())->willReturn($response = new Response(400));

        try {
            $this->handler->handle($this->action);
            $this->fail('An exception should have been thrown');
        } catch (FailedToAuthenticateUserWithCustomToken $e) {
            $this->assertSame($this->action, $e->action());
            $this->assertSame($response, $e->response());
        }
    }
}
