<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth\AuthenticateUserWithRefreshToken;

use Kreait\Firebase\Auth\AuthenticateUserWithRefreshToken;
use Kreait\Firebase\Auth\AuthenticationResult;

interface Handler
{
    /**
     * @throws FailedToAuthenticateUserWithRefreshToken
     */
    public function handle(AuthenticateUserWithRefreshToken $action): AuthenticationResult;
}
