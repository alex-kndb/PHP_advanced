<?php

namespace LksKndb\Php2\Blog\Repositories\UsersRepositories;

use LksKndb\Php2\Blog\User;
use LksKndb\Php2\Blog\UUID;

interface UsersRepositoriesInterface
{
    public function saveUser(User $user): void;
    public function getUserByUUID(UUID $uuid): User;
    public function getUserByUsername(string $username): User;
}