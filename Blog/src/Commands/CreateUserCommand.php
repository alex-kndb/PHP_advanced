<?php

namespace LksKndb\Php2\Commands;

use DateTimeImmutable;
use LksKndb\Php2\Classes\Name;
use LksKndb\Php2\Classes\User;
use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Exceptions\ArgumentNotExistException;
use LksKndb\Php2\Exceptions\CommandException;
use LksKndb\Php2\Exceptions\User\UserNotFoundException;
use LksKndb\Php2\Repositories\UsersRepositories\UsersRepositoriesInterface;

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
        // Проверяем на существование такого юзера в базе
        $username = $args->get('username');
        if($this->isUserExist($username)){
            throw new CommandException("User (username: $username) already exist!");
        }

        // Создаем нового юзера и сохраняем в базу
        $this->usersRepository->saveUser(
            New User(
                UUID::createUUID(),
                new Name(
                    $args->get('first_name'),
                    $args->get('last_name'),
                    $args->get('username'),
                ),
                new DateTimeImmutable()
            )
        );
    }

    private function isUserExist(string $username): bool
    {
        try{
            $this->usersRepository->getUserByUsername($username);
        }catch(UserNotFoundException $e){
            echo $e->getMessage();
            return false;
        }
        return true;
    }
}