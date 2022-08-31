<?php

namespace LksKndb\Php2\Blog\Commands;

use LksKndb\Php2\Blog\Name;
use LksKndb\Php2\Blog\User;
use LksKndb\Php2\Exceptions\ArgumentNotExistException;
use LksKndb\Php2\Exceptions\CommandException;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\UsersRepositoriesInterface;

class CreateUserCommand
{
    public function __construct(
        private UsersRepositoriesInterface $usersRepository
    ){}

    /**
     * @throws ArgumentNotExistException
     * @throws CommandException
     */
    public function handle(Arguments $args): void
    {
        $username = $args->get('username');
        if($this->usersRepository->isUserExists($username)){
            throw new CommandException("User (username: $username) already exist!");
        }

        $this->usersRepository->saveUser(
            User::createFrom(
                new Name(
                    $args->get('first_name'),
                    $args->get('last_name'),
                    $args->get('username')
                ),
                $args->get('password')
            )
        );
    }
}