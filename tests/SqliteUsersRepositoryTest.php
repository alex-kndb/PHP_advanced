<?php

use LksKndb\Php2\Classes\User;
use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Classes\Name;
use LksKndb\Php2\Exceptions\User\UserNotFoundException;
use LksKndb\Php2\Repositories\UsersRepositories\SqliteUsersRepository;
use PHPUnit\Framework\TestCase;

class SqliteUsersRepositoryTest extends TestCase
{
    // Тест, проверяющий, что SQLite-репозиторий бросает исключение,
    // когда запрашиваемый пользователь не найден
    public function testItThrowAnExceptionWhenUserNotFound() : void
    {
        // 1. Мок подключения:
        $connectionMock = $this->createStub(PDO::class);
        // 2. Стаб запроса:
        $statementStub = $this->createStub(PDOStatement::class);
        // 3. Мок подключения будет возращать стаб запроса при вызове метода prepare:
        $connectionMock->method('prepare')->willReturn($statementStub);
        // 4. Стаб запроса будет возвращать false при вызове метода fetch:
        // т.е. getUser() -> false, т.е. result пустой и юзер не найден
        $statementStub->method('fetch')->willReturn(false);

        $repo = new SqliteUsersRepository($connectionMock);
        // Ожидаем исключение
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("User (Username: Alex) not found into db");

        $repo->getUserByUsername('Alex');
    }

    // Тест, проверяющий, что репо сохраняет данные в БД
    public function testItSaveUserToDB()
    {
        // 1. Мок подключения:
        $connectionStub = $this->createStub(PDO::class);
        // 2. Стаб запроса:
        $statementMock = $this->createStub(PDOStatement::class);
        // 3. Мок подключения будет возращать стаб запроса при вызове метода prepare:
        $connectionStub->method('prepare')->willReturn($statementMock);
        // ожидаем, что вызываться будет один раз, метод execute и даем массив данных
        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':first_name' => 'Alex',
                ':last_name' => 'Alex',
                ':username' => 'Alex',
                ':registration' => '2022-08-13 17:21:36'
            ]);

        $repo = new SqliteUsersRepository($connectionStub);
        $repo->saveUser(
            new User(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                new Name(
                    'Alex',
                    'Alex',
                    'Alex'
                ),
                DateTimeImmutable::createFromFormat('Y-m-d\ H:i:s', '2022-08-13 17:21:36')
            )
        );
    }
}