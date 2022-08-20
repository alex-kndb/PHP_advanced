<?php

namespace LksKndb\Php2\Repositories\PostsRepositories;

use LksKndb\Php2\Classes\Post;
use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Exceptions\Posts\PostNotFoundException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use PDO;
use PDOStatement;

class SqlitePostsRepository implements PostsRepositoriesInterface
{
    public function __construct(
        private PDO $connection
    ){}

    public function savePost(Post $post): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author, title, text) VALUES (:uuid, :author, :title, :text)'
        );
        $statement->execute([
            ':uuid' => $post->getPost(),
            ':author' => $post->getAuthor(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText(),
        ]);
    }

    /**
     * @throws InvalidUuidException
     * @throws PostNotFoundException
     */
    public function getPostByUUID(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
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
            throw new PostNotFoundException("DB: post (UUID: $uuid) not found!");
        }

        return new Post(
            new UUID($result['uuid']),
            new UUID($result['author']),
            $result['title'],
            $result['text']);
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