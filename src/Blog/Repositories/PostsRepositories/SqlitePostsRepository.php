<?php

namespace LksKndb\Php2\Blog\Repositories\PostsRepositories;

use DateTimeImmutable;
use LksKndb\Php2\Blog\Name;
use LksKndb\Php2\Blog\Post;
use LksKndb\Php2\Blog\User;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\Posts\PostNotFoundException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
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
            'SELECT posts.uuid,posts.author,posts.title,posts.text,users.first_name,users.last_name,users.username,users.password,users.registration FROM posts INNER JOIN users ON posts.author=users.uuid WHERE posts.uuid=:uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        return $this->getPost($statement, $uuid);
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidUuidException
     */
    private function getPost(PDOStatement $statement, string $uuid): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if(!$result) {
            $this->logger->warning("DB: post (UUID: $uuid) not found!");
            // throw new PostNotFoundException("DB: post (UUID: $uuid) not found!");
            exit;
        }

        return new Post(
            new UUID($result['uuid']),
            new User(
                new UUID($result['author']),
                new Name(
                    $result['first_name'],
                    $result['last_name'],
                    $result['username']
                ),
                $result['password'],
                DateTimeImmutable::createFromFormat('Y-m-d\ H:i:s', $result['registration'])
            ),
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