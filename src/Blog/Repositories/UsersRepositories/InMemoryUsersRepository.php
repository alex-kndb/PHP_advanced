<?php

namespace LksKndb\Php2\Blog\Repositories\UsersRepositories;

use LksKndb\Php2\Blog\User;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\User\UserNotFoundException;

class InMemoryUsersRepository implements UsersRepositoriesInterface
{
    private array $users = [];

    /**
     * @return array
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    public function saveUser(User $user): void
    {
        $this->users[] = $user;
    }

    public function getUserByUUID(UUID $uuid): User
    {
        foreach($this->users as $user){
            if((string)$user->getUUID() === (string)$uuid){
                return $user;
            }
        }
        throw new UserNotFoundException("User (UUID: $uuid) not found");
    }

    public function getUserByUsername(string $username): User
    {
        foreach($this->users as $user){
            if($user->getUsername() === $username){
                return $user;
            }
        }
        throw new UserNotFoundException("User (username: $username) not found");
    }
}