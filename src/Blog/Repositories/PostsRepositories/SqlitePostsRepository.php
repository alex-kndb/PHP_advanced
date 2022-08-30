<?php

namespace LksKndb\Php2\Blog\Repositories\PostsRepositories;

use LksKndb\Php2\Blog\Post;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\Posts\PostNotFoundException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\Exceptions\User\UserNotFoundException;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqlitePostsRepository implements PostsRepositoriesInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ){}

    public function savePost(Post $post): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author, title, text) VALUES (:uuid, :author, :title, :text)'
        );
        $statement->execute([
            ':uuid' => $post->getPost(),
            ':author' => $post->getAuthor()->getUUID(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText(),
        ]);

        $this->logger->info("SqlitePostRepo -> post created: {$post->getPost()}");
    }

    /**
     * @throws InvalidUuidException
     * @throws PostNotFoundException
     */
    public function getPostByUUID(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid=:uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        return $this->getPost($statement, $uuid);
    }

    /**
     * @throws InvalidUuidException
     * @throws UserNotFoundException
     */
    private function getPost(PDOStatement $statement, string $uuid): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if(!$result) {
            $this->logger->warning("DB: post (UUID: $uuid) not found!");
            // throw new PostNotFoundException("DB: post (UUID: $uuid) not found!");
            exit;
        }

        $userRepo = new SqliteUsersRepository($this->connection, $this->logger);

        return new Post(
            new UUID($result['uuid']),
            $userRepo->getUserByUUID(new UUID($result['author'])),
            $result['title'],
            $result['text']
        );
    }

    public function deletePost(UUID $uuid): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM posts WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
    }


}