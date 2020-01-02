<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth;

final class AuthenticateUser
{
    /** @var string */
    private $uid;

    /** @var array */
    private $claims;

    private function __construct()
    {
        $this->uid = '';
        $this->claims = [];
    }

    public static function withUid(string $uid): self
    {
        $instance = new self();
        $instance->uid = $uid;

        return $instance;
    }

    public function uid(): string
    {
        return $this->uid;
    }

    public function withClaims(array $claims): self
    {
        $instance = clone $this;
        $instance->claims = $claims;

        return $instance;
    }

    public function claims(): array
    {
        return $this->claims;
    }
}
