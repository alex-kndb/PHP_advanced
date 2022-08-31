<?php

namespace LksKndb\Php2\Blog\Repositories\UsersRepositories;

use DateTimeImmutable;
use LksKndb\Php2\Blog\Name;
use LksKndb\Php2\Blog\User;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\User\InvalidUsernameException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\Exceptions\User\UserNotFoundException;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqliteUsersRepository implements UsersRepositoriesInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ){}

    public function saveUser(User $user): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO users 
                        (uuid, username, first_name,last_name, password, registration) 
                    VALUES 
                        (:uuid, :username, :first_name, :last_name, :password, :registration)
                    ON CONFLICT (username) 
                        DO UPDATE SET
                            first_name = :first_name,
                            last_name = :last_name'
        );

        $statement->execute([
            ':uuid' => $user->getUUID(),
            ':username' => $user->getName()->getUsername(),
            ':first_name' => $user->getName()->getFirstName(),
            ':last_name' => $user->getName()->getLastName(),
            ':password' => $user->hashedPassword(),
            ':registration' => $user->getRegisteredOn()->format('Y-m-d\ H:i:s'),
        ]);

//        $this->logger->info("User save in db: ".$user->getUUID());
    }

    public function isUserExists(string $username) : bool
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );
        $statement->execute([
            ':username' => $username,
        ]);
        return (bool)$statement->fetch();
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
     * @throws InvalidUuidException
     * @throws InvalidUsernameException|UserNotFoundException
     */
    public function getUserByUsername(string $username): User
    {
        if(empty($username)){
           $this->logger->warning("Username is empty!");
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
                    $this->logger->warning("User (UUID: $searchBy) not found into db");
                    throw new UserNotFoundException("User (UUID: $searchBy) not found into db");
                }else{
                    $this->logger->error("DB: user searching error (not UUID)!");
                    throw new UserNotFoundException("DB: user searching error!");
                }
            }elseif(is_string($searchBy)){
                $this->logger->warning("User (Username: $searchBy) not found into db");
                throw new UserNotFoundException("User (Username: $searchBy) not found into db");
            }else{
                $this->logger->error("DB: user searching error!");
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
            $result['password'],
            DateTimeImmutable::createFromFormat('Y-m-d\ H:i:s', $result['registration'])
        );
    }
}