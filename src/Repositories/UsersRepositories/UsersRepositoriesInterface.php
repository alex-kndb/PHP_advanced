<?php

namespace LksKndb\Php2\Repositories\UsersRepositories;

use LksKndb\Php2\Classes\User;
use LksKndb\Php2\Classes\UUID;

interface UsersRepositoriesInterface
{
    public function saveUser(User $user): void;
    public function getUserByUUID(UUID $uuid): User;
    public function getUserByUsername(string $username): User;
}