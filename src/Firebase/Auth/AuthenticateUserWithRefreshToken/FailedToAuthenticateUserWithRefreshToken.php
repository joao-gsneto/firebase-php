<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth\AuthenticateUserWithRefreshToken;

use InvalidArgumentException;
use Kreait\Firebase\Auth\AuthenticateUserWithRefreshToken;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Util\JSON;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

final class FailedToAuthenticateUserWithRefreshToken extends RuntimeException implements FirebaseException
{
    /** @var AuthenticateUserWithRefreshToken|null */
    private $action;

    /** @var ResponseInterface|null */
    private $response;

    public static function withActionAndResponse(AuthenticateUserWithRefreshToken $action, ResponseInterface $response): self
    {
        $fallbackMessage = 'Failed to authenticate user with refresh token';

        try {
            $message = JSON::decode((string) $response->getBody(), true)['error']['message'] ?? $fallbackMessage;
        } catch (InvalidArgumentException $e) {
            $message = $fallbackMessage;
        }

        $error = new self($message);
        $error->action = $action;
        $error->response = $response;

        return $error;
    }

    /**
     * @return AuthenticateUserWithRefreshToken|null
     */
    public function action()
    {
        return $this->action;
    }

    /**
     * @return ResponseInterface|null
     */
    public function response()
    {
        return $this->response;
    }
}
