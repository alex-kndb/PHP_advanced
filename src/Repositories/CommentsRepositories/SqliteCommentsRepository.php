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
            'INSERT INTO comments (id, post, author, comment) VALUES (:uuid, :post, :author, :text)'
        );
        $statement->execute([
            ':uuid' => $comment->getUuid(),
            ':post' => $comment->getPost()->getPost(),
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
                    LEFT JOIN posts ON comments.post=posts.uuid
                    LEFT JOIN  users ON comments.author=users.uuid
                    WHERE comments.id=:uuid'
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
            new UUID($result['uuid']),
            new Name(
                $result['first_name'],
                $result['last_name'],
                $result['username']
            ),
            DateTimeImmutable::createFromFormat('Y-m-d\ H:i:s', $result['registration'])
        );

        $post = new Post(
            new UUID($result['post']),
            $user,
            $result['title'],
            $result['text']
        );

        return new Comment(
            new UUID($result['id']),
            $post,
            $user,
            $result['comment']);
    }

    public function deleteComment(UUID $id): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM comments WHERE id = :id'
        );
        $statement->execute([
            ':id' => (string)$id,
        ]);
    }
}