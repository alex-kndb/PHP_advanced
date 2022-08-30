<?php

namespace LksKndb\Php2\http\Auth;

use LksKndb\Php2\Blog\User;
use LksKndb\Php2\http\Request;

interface AuthenticationInterface
{
    public function user(Request $request): User;
}