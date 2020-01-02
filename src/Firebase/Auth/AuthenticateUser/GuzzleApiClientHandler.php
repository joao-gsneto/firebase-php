<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth\AuthenticateUser;

use Firebase\Auth\Token\Domain\Generator;
use GuzzleHttp\ClientInterface;
use Kreait\Clock;
use Kreait\Firebase\Auth\AuthenticateUser;
use Kreait\Firebase\Auth\AuthenticateUserWithCustomToken;
use Kreait\Firebase\Auth\AuthenticateUserWithCustomToken\FailedToAuthenticateUserWithCustomToken;
use Kreait\Firebase\Auth\AuthenticationResult;
use Kreait\Firebase\JWT\CustomTokenGenerator;
use Throwable;

final class GuzzleApiClientHandler implements Handler
{
    /** @var ClientInterface */
    private $client;

    /** @var Clock */
    private $clock;

    /** @var Generator|CustomTokenGenerator */
    private $tokenGenerator;

    /**
     * @param Generator|CustomTokenGenerator $tokenGenerator
     */
    public function __construct(ClientInterface $client, $tokenGenerator, Clock $clock = null)
    {
        $this->client = $client;
        $this->tokenGenerator = $tokenGenerator;
        $this->clock = $clock ?: new Clock\SystemClock();
    }

    public function handle(AuthenticateUser $action): AuthenticationResult
    {
        try {
            $customToken = $this->generateCustomToken($action->uid(), $action->claims());
        } catch (Throwable $e) {
            throw new FailedToAuthenticateUser('Failed to authenticate user: '.$e->getMessage(), $e->getCode(), $e);
        }

        try {
            return $this->signInWithCustomToken($customToken);
        } catch (FailedToAuthenticateUserWithCustomToken $e) {
            if ($response = $e->response()) {
                throw FailedToAuthenticateUser::withActionAndResponse($action, $response);
            }

            throw new FailedToAuthenticateUser('Failed to authenticate user: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    private function generateCustomToken(string $uid, array $claims): string
    {
        if ($this->tokenGenerator instanceof Generator) {
            return (string) $this->tokenGenerator->createCustomToken($uid, $claims);
        }

        return $this->tokenGenerator->createCustomToken($uid, $claims)->toString();
    }

    private function signInWithCustomToken(string $customToken): AuthenticationResult
    {
        $handler = new AuthenticateUserWithCustomToken\GuzzleApiClientHandler($this->client, $this->clock);
        $action = AuthenticateUserWithCustomToken::fromValue($customToken);

        return $handler->handle($action);
    }
}
