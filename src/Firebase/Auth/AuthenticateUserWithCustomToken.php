<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth;

final class AuthenticateUserWithCustomToken
{
    /** @var string */
    private $customToken;

    private function __construct()
    {
    }

    public static function fromValue(string $customToken): self
    {
        $instance = new self();
        $instance->customToken = $customToken;

        return $instance;
    }

    public function customToken(): string
    {
        return $this->customToken;
    }
}
