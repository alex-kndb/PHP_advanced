<?php

namespace LksKndb\Php2\Blog\Repositories\AuthTokensRepository;

use LksKndb\Php2\http\Auth\AuthToken;

interface AuthTokensRepositoryInterface
{
    public function save(AuthToken $authToken): void;
    public function get(string $token): AuthToken;
}