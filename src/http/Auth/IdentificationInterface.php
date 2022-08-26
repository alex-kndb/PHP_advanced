<?php

namespace LksKndb\Php2\Blog\http\Auth;

use LksKndb\Php2\Blog\User;
use LksKndb\Php2\http\Request;

interface IdentificationInterface
{
    public function user(Request $request) : User;
}