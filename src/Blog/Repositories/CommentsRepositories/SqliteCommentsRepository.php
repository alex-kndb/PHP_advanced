<?php

namespace LksKndb\Php2\Blog\Repositories\CommentsRepositories;

use LksKndb\Php2\Blog\Comment;
use LksKndb\Php2\Blog\Repositories\PostsRepositories\SqlitePostsRepository;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\Comment\CommentNotFoundException;
use LksKndb\Php2\Exceptions\Posts\PostNotFoundException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\Exceptions\User\UserNotFoundException;
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

//        $this->logger->info("SqliteCommentRepo -> comment created: {$comment->getUuid()}");
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidUuidException
     */
    public function getCommentByUUID(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid=:uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        return $this->getComment($statement, $uuid);
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidUuidException
     * @throws UserNotFoundException
     * @throws PostNotFoundException
     */
    private function getComment(PDOStatement $statement, string $uuid): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if(!$result) {
            $this->logger->warning("DB: comment (UUID: $uuid) not found!");
            // throw new CommentNotFoundException("DB: comment (UUID: $uuid) not found!");
            exit;
        }

        $userRepo = new SqliteUsersRepository($this->connection, $this->logger);
        $postRepo = new SqlitePostsRepository($this->connection, $this->logger);

        return new Comment(
            new UUID($result['uuid']),
            $postRepo->getPostByUUID(new UUID($result['post'])),
            $userRepo->getUserByUUID(new UUID($result['author'])),
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