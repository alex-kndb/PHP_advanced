<?php

namespace LksKndb\Php2\Blog\Repositories\UsersRepositories;

use DateTimeImmutable;
use LksKndb\Php2\Blog\Name;
use LksKndb\Php2\Blog\User;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\User\UserNotFoundException;

class DummyUsersRepository implements UsersRepositoriesInterface
{
    public function saveUser(User $user): void
    {
        // TODO: ...
    }

    // Кидаем подготовленное исключение
    public function getUserByUUID(UUID $uuid): User
    {
        throw new UserNotFoundException("User not found!");
    }

    // Возвращаем подготовленный экземпляр
    public function getUserByUsername(string $username): User
    {
        return new User(
            UUID::createUUID(),
            new Name(
                'Alex',
                'Alex',
                'Alex'
            ),
            new DateTimeImmutable()
        );
    }
}