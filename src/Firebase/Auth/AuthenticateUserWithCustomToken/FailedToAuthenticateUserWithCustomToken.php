<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth\AuthenticateUserWithCustomToken;

use InvalidArgumentException;
use Kreait\Firebase\Auth\AuthenticateUserWithCustomToken;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Util\JSON;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

final class FailedToAuthenticateUserWithCustomToken extends RuntimeException implements FirebaseException
{
    /** @var AuthenticateUserWithCustomToken|null */
    private $action;

    /** @var ResponseInterface|null */
    private $response;

    public static function withActionAndResponse(AuthenticateUserWithCustomToken $action, ResponseInterface $response): self
    {
        $fallbackMessage = 'Failed to authenticate user with custom token';

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
     * @return AuthenticateUserWithCustomToken|null
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
