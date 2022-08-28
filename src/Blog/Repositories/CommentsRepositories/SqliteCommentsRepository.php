<?php

namespace LksKndb\Php2\Blog\Repositories\CommentsRepositories;

use DateTimeImmutable;
use LksKndb\Php2\Blog\Comment;
use LksKndb\Php2\Blog\Name;
use LksKndb\Php2\Blog\Post;
use LksKndb\Php2\Blog\User;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\Comment\CommentNotFoundException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqliteCommentsRepository implements CommentsRepositoriesInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ){}

    public function saveComment(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post, author, comment) VALUES (:uuid, :post, :author, :text)'
        );
        $statement->execute([
            ':uuid' => $comment->getUuid(),
            ':post' => $comment->getPost()->getPost(),
            ':author' => $comment->getAuthor()->getUuid(),
            ':text' => $comment->getText(),
        ]);

        $this->logger->info("SqliteCommentRepo -> comment created: {$comment->getUuid()}");
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidUuidException
     */
    public function getCommentByUUID(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT comments.uuid,comments.post,comments.author AS comment_author,comments.comment,posts.author AS post_author,posts.title,posts.text,users.username,users.first_name,users.last_name,users.registration
                    FROM comments
                    LEFT JOIN posts ON comments.post=posts.uuid
                    LEFT JOIN  users ON comments.author=users.uuid
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
            $this->logger->warning("DB: comment (UUID: $uuid) not found!");
            // throw new CommentNotFoundException("DB: comment (UUID: $uuid) not found!");
            exit;
        }

        $comment_author = new User(
            new UUID($result['comment_author']),
            new Name(
                $result['first_name'],
                $result['last_name'],
                $result['username']
            ),
            DateTimeImmutable::createFromFormat('Y-m-d\ H:i:s', $result['registration'])
        );

        $post_author_result = $this->query('users', new UUID($result['post_author']));

        $post_author = new User(
            new UUID($post_author_result['uuid']),
            new Name(
                $post_author_result['first_name'],
                $post_author_result['last_name'],
                $post_author_result['username']
            ),
            DateTimeImmutable::createFromFormat('Y-m-d\ H:i:s', $post_author_result['registration'])
        );

        $post = new Post(
            new UUID($result['post']),
            $post_author,
            $result['title'],
            $result['text']
        );

        return new Comment(
            new UUID($result['uuid']),
            $post,
            $comment_author,
            $result['comment']);
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

    public function query(string $table, UUID $uuid) : ?array
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM $table WHERE uuid = :uuid"
        );

        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        return $statement->fetch(PDO::FETCH_ASSOC) ?? null;
    }

}