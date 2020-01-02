<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth\AuthenticateUserWithCustomToken;

use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\Psr7\stream_for;
use function GuzzleHttp\Psr7\uri_for;
use Kreait\Firebase\Auth\AuthenticateUserWithCustomToken;
use Kreait\Firebase\Http\WrappedPsr7Request;
use Psr\Http\Message\RequestInterface;

final class ApiRequest implements RequestInterface
{
    use WrappedPsr7Request;

    public function __construct(AuthenticateUserWithCustomToken $action)
    {
        $uri = uri_for('https://identitytoolkit.googleapis.com/v1/accounts:signInWithCustomToken');

        $body = stream_for(\json_encode([
            'token' => $action->customToken(),
            'returnSecureToken' => true,
        ]));

        $headers = \array_filter([
            'Content-Type' => 'application/json; charset=UTF-8',
            'Content-Length' => $body->getSize(),
        ]);

        $this->wrappedRequest = new Request('POST', $uri, $headers, $body);
    }
}
