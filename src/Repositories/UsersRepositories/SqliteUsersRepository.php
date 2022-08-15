<?php

namespace LksKndb\Php2\Repositories\UsersRepositories;

use DateTimeImmutable;
use LksKndb\Php2\Classes\Name;
use LksKndb\Php2\Classes\User;
use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Exceptions\User\InvalidUsernameException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\Exceptions\User\UserNotFoundException;
use PDO;
use PDOStatement;

class SqliteUsersRepository implements UsersRepositoriesInterface
{
    public function __construct(
        private PDO $connection
    ){}

    public function saveUser(User $user): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid, username, first_name,last_name, registration) VALUES (:uuid, :username, :first_name, :last_name, :registration)'
        );
        $statement->execute([
            ':uuid' => $user->getUUID(),
            ':username' => $user->getName()->getUsername(),
            ':first_name' => $user->getName()->getFirstName(),
            ':last_name' => $user->getName()->getLastName(),
            ':registration' => $user->getRegisteredOn()->format('Y-m-d\ H:i:s'),
        ]);
    }

    /**
     * @throws InvalidUuidException
     * @throws UserNotFoundException
     */
    public function getUserByUUID(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        return $this->getUser($statement, $uuid);


    }

    /**
     * @throws InvalidUsernameException
     * @throws InvalidUuidException
     * @throws UserNotFoundException
     */
    public function getUserByUsername(string $username): User
    {
        if(empty($username)){
            throw new InvalidUsernameException("Username is empty!");
        }
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );
        $statement->execute([
            ':username' => $username,
        ]);

        return $this->getUser($statement, $username);

    }

    /**
     * @throws InvalidUuidException
     * @throws UserNotFoundException
     */
    private function getUser(PDOStatement $statement, $searchBy): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if(!$result){
            if(is_object($searchBy)){
                $arr = explode('\\', get_class($searchBy));
                if(array_pop($arr) === 'UUID'){
                    throw new UserNotFoundException("User (UUID: $searchBy) not found into db");
                }
            }elseif(is_string($searchBy)){
                throw new UserNotFoundException("User (Username: $searchBy) not found into db");
            }else{
                throw new UserNotFoundException("DB: user searching error!");
            }
        }

        return new User(
            new UUID($result['uuid']),
            new Name(
                $result['first_name'],
                $result['last_name'],
                $result['username']
            ),
            DateTimeImmutable::createFromFormat('Y-m-d\ H:i:s', $result['registration'])
        );
    }
}