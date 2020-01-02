<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth\AuthenticateUser;

use Kreait\Firebase\Auth\AuthenticateUser;
use Kreait\Firebase\Auth\AuthenticationResult;

interface Handler
{
    /**
     * @throws FailedToAuthenticateUser
     */
    public function handle(AuthenticateUser $action): AuthenticationResult;
}
