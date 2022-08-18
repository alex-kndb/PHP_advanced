<?php

namespace LksKndb\Php2\UnitTests;

use LksKndb\Php2\Classes\Name;
use LksKndb\Php2\Classes\User;
use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Commands\Arguments;
use LksKndb\Php2\Commands\CreateUserCommand;
use LksKndb\Php2\Exceptions\ArgumentNotExistException;
use LksKndb\Php2\Exceptions\CommandException;
use LksKndb\Php2\Exceptions\User\InvalidUsernameException;
use LksKndb\Php2\Exceptions\User\UserNotFoundException;
use LksKndb\Php2\Repositories\UsersRepositories\DummyUsersRepository;
use LksKndb\Php2\Repositories\UsersRepositories\UsersRepositoriesInterface;
use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{
    // 1.
    // используем стаб - DummyUsersRepository (двойник класса с интерфейсом UsersRepositoriesInterface)
    public function testItThrowAnExceptionWhenUserAlreadyExist()
    {
        $command = new CreateUserCommand(new DummyUsersRepository());
        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("User (username: Alex) already exist!");

        $command->handle(new Arguments(['username' => 'Alex']));
    }


    // 2.
    // Не следует создавать двойник класса, как в прошлом примере.
    // Пишем метод, возвращающий анонимный класс (используется только для созданияодного единственного
    // экземпляра класса и ббольше нигде не фигурирует)
    private function makeUsersRepository(): UsersRepositoriesInterface
    {
        return new class implements UsersRepositoriesInterface {

            public function saveUser(User $user): void
            {
                // TODO ...
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getUserByUUID(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getUserByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
        };
    }

    // используем анонимный класс в следующем тесте
    public function testItRequiresFirstName() : void
    {
        $command = new CreateUserCommand($this->makeUsersRepository());
        $this->expectException(ArgumentNotExistException::class);
        $this->expectExceptionMessage("No such argument: first_name");
        $command->handle(new Arguments([
//            'first_name' => 'Alex',
            'last_name' => 'Alex',
            'username' => 'Alex'
        ]));
    }


    // 3.
    // Обычный стаб не сможет сообщить нам, что юзер сохранился, т.е. что медот saveUser отработал.
    // Поэтому пишем мок - тестовый двойник, имеющий метод для проверки assert-утверждений
    public function testItSavesUserToRepo() : void
    {
        $userRepo = new class implements UsersRepositoriesInterface {

            public bool $called = false;

            // Вместо сохранения юзера, просто изменяем на true значение called.
            // Таким образом мы узнаем, взывался ли метод saveUser().
            public function saveUser(User $user): void
            {
                $this->called = true;
            }

            public function get(UUID $uuid): User
            {
                 throw new UserNotFoundException("Not found");
            }

            public function getUserByUUID(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            // Всегда возвращает юзера (username - Al)
            public function getUserByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");

//                 return new User(
//                     UUID::createUUID(),
//                     new Name(
//                         'al',
//                         'al',
//                         'al'
//                     ),
//                     new \DateTimeImmutable()
//                 );
            }

            public function wasCalled(): bool
            {
                return $this->called;
            }

        };
        $command = new CreateUserCommand($userRepo);

        $command->handle(new Arguments([
            'first_name' => 'Alex',
            'last_name' => 'Alex',
            'username' => 'Alex'
        ]));

        $this->assertTrue($userRepo->wasCalled());
    }
}
