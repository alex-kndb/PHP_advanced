<?php

namespace LksKndb\Php2\Repositories\CommentsRepositories;

use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Classes\Comment;
use LksKndb\Php2\Exceptions\Comment\CommentNotFoundException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use PDO;
use PDOStatement;

class SqliteCommentsRepository implements CommentsRepositoriesInterface
{
    public function __construct(
        private PDO $connection
    ){}

    public function saveComment(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post, author, text) VALUES (:uuid, :post, :author, :text)'
        );
        $statement->execute([
            ':uuid' => $comment->getUuid(),
            ':post' => $comment->getPost(),
            ':author' => $comment->getAuthor(),
            ':text' => $comment->getText(),
        ]);
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidUuidException
     */
    public function getCommentByUUID(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        return $this->getComment($statement, $uuid);
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidUuidException
     */
    private function getComment(PDOStatement $statement, string $uuid): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if(!$result) {
            throw new CommentNotFoundException("DB: comment (UUID: $uuid) not found!");
        }

        return new Comment(
            new UUID($result['uuid']),
            new UUID($result['post']),
            new UUID($result['author']),
            $result['text']);
    }

    public function deleteComment(UUID $uuid): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM comments WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
    }
}