<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth\AuthenticateUserWithCustomToken;

use Kreait\Firebase\Auth\AuthenticateUserWithCustomToken;
use Kreait\Firebase\Auth\AuthenticationResult;

interface Handler
{
    /**
     * @throws FailedToAuthenticateUserWithCustomToken
     */
    public function handle(AuthenticateUserWithCustomToken $action): AuthenticationResult;
}
