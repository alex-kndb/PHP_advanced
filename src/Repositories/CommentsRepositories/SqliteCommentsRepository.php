<?php

namespace LksKndb\Php2\Repositories\CommentsRepositories;

use DateTimeImmutable;
use LksKndb\Php2\Classes\Name;
use LksKndb\Php2\Classes\Post;
use LksKndb\Php2\Classes\User;
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
            ':post' => $comment->getPost()->getUuid(),
            ':author' => $comment->getAuthor()->getUuid(),
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
            'SELECT * 
                   FROM comments 
                   INNER JOIN posts ON comments.post = posts.uuid
                   INNER JOIN users ON comments.author = users.uuid
                   WHERE comments.uuid=:uuid'

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

        $user = new User(
            new UUID($result['users.uuid']),
            new Name(
                $result['users.first_name'],
                $result['users.last_name'],
                $result['users.username']
            ),
            DateTimeImmutable::createFromFormat('Y-m-d\ H:i:s', $result['registration'])
        );

        $post = new Post(
            new UUID($result['posts.uuid']),
            $user,
            $result['posts.title'],
            $result['posts.text']
        );

        return new Comment(
            new UUID($result['comments.uuid']),
            $post,
            $user,
            $result['comments.text']);
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