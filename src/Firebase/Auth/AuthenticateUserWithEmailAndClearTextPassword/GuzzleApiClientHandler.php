<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth\AuthenticateUserWithEmailAndClearTextPassword;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Kreait\Clock;
use Kreait\Firebase\Auth\AuthenticateUserWithEmailAndClearTextPassword;
use Kreait\Firebase\Auth\AuthenticationResult;
use Kreait\Firebase\Util\JSON;

final class GuzzleApiClientHandler implements Handler
{
    /** @var ClientInterface */
    private $client;

    /** @var Clock */
    private $clock;

    public function __construct(ClientInterface $client, Clock $clock = null)
    {
        $this->client = $client;
        $this->clock = $clock ?: new Clock\SystemClock();
    }

    public function handle(AuthenticateUserWithEmailAndClearTextPassword $action): AuthenticationResult
    {
        $request = new ApiRequest($action);

        try {
            $response = $this->client->send($request, ['http_errors' => false]);
        } catch (GuzzleException $e) {
            throw new FailedToAuthenticateUserWithEmailAndClearTextPassword('Failed to authenticate user with email and clear-text password: '.$e->getMessage(), $e->getCode(), $e);
        }

        if ($response->getStatusCode() !== 200) {
            throw FailedToAuthenticateUserWithEmailAndClearTextPassword::withActionAndResponse($action, $response);
        }

        try {
            $data = JSON::decode((string) $response->getBody(), true);
        } catch (InvalidArgumentException $e) {
            throw new FailedToAuthenticateUserWithEmailAndClearTextPassword('Unable to parse the response data: '.$e->getMessage(), $e->getCode(), $e);
        }

        try {
            return AuthenticationResult::fromData($data, $this->clock);
        } catch (InvalidArgumentException $e) {
            throw new FailedToAuthenticateUserWithEmailAndClearTextPassword('Unexpected response data: '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}
