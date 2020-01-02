<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth\AuthenticateUserWithRefreshToken;

use function GuzzleHttp\Psr7\build_query;
use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\Psr7\uri_for;
use Kreait\Firebase\Auth\AuthenticateUserWithRefreshToken;
use Kreait\Firebase\Http\WrappedPsr7Request;
use Psr\Http\Message\RequestInterface;

final class ApiRequest implements RequestInterface
{
    use WrappedPsr7Request;

    public function __construct(AuthenticateUserWithRefreshToken $action)
    {
        $body = build_query([
            'grant_type' => 'refresh_token',
            'refresh_token' => $action->refreshToken(),
        ]);

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/json',
        ];

        $uri = uri_for('https://securetoken.googleapis.com/v1/token');

        $this->wrappedRequest = new Request('POST', $uri, $headers, $body);
    }
}
