<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth\AuthenticateUserWithEmailAndClearTextPassword;

use Kreait\Firebase\Auth\AuthenticateUserWithEmailAndClearTextPassword;
use Kreait\Firebase\Auth\AuthenticationResult;

interface Handler
{
    /**
     * @throws FailedToAuthenticateUserWithEmailAndClearTextPassword
     */
    public function handle(AuthenticateUserWithEmailAndClearTextPassword $action): AuthenticationResult;
}
