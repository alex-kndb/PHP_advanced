<?php

namespace LksKndb\Php2\Blog\Repositories\LikesRepositories;

use LksKndb\Php2\Blog\CommentLike;
use LksKndb\Php2\Blog\Repositories\CommentsRepositories\SqliteCommentsRepository;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\Comment\CommentNotFoundException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\Exceptions\User\UserNotFoundException;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqliteCommentLikesRepository implements CommentLikesRepositoriesInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ){}

    public function save(CommentLike $like): void
    {
        $comment = $like->getComment()->getUuid();
        $user = $like->getUser()->getUUID();
        if($this->isCommentAlreadyLiked($comment, $user)){
            $this->logger->warning("Comment is already liked by this user: $user");
            // throw new CommentIsAlreadyLikedByThisUser("Comment is already liked by this user: $user");
            return;
        }
        $statement = $this->connection->prepare(
            'INSERT INTO comments_likes (uuid, comment, user) VALUES (:uuid, :comment, :user)'
        );
        $statement->execute([
            ':uuid' => $like->getUuid(),
            ':comment' => $comment,
            ':user' => $user
        ]);

        $this->logger->info("SqliteCommentLikeRepo -> comment like created: {$like->getUuid()}");
    }

    public function isCommentAlreadyLiked(UUID $comment, UUID $user) : bool
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments_likes WHERE comment=:comment_id AND user=:user_id'
        );
        $statement->execute([
            ':comment_id' => (string)$comment,
            ':user_id' => (string)$user
        ]);
        return (bool)$statement->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @throws CommentNotFoundException
     * @throws InvalidUuidException
     * @throws UserNotFoundException
     */
    public function getCommentLikeByUUID(UUID $uuid): CommentLike
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments_likes WHERE uuid=:uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        return $this->getCommentLike($statement, $uuid);
    }

    /**
     * @throws InvalidUuidException
     * @throws CommentNotFoundException
     * @throws UserNotFoundException
     */
    private function getCommentLike(PDOStatement $statement, string $uuid): CommentLike
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if(!$result) {
            $this->logger->warning("DB: comment/like (UUID: $uuid) not found!");
            // throw new LikeNotFoundException("DB: comment/like (UUID: $uuid) not found!");
            exit;
        }

        $usersRepo = new SqliteUsersRepository($this->connection, $this->logger);
        $commentsRepo = new SqliteCommentsRepository($this->connection, $this->logger);

        return new CommentLike(
            new UUID($result['uuid']),
            $usersRepo->getUserByUUID(new UUID($result['author'])),
            $commentsRepo->getCommentByUUID(new UUID($result['comment']))
        );
    }

    public function deleteCommentLike(UUID $uuid): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM comments_likes WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidUuidException
     * @throws UserNotFoundException
     */
    public function getLikesByCommentUUID(UUID $comment) : ?array
    {
        $statement = $this->connection->prepare(
            'SELECT uuid FROM comments_likes WHERE comment = :comment'
        );

        $statement->execute([
            ':comment' => (string)$comment
        ]);

        $comments_result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $comments = [];
        foreach ($comments_result as $comment){
            $comments[] = $this->getCommentLikeByUUID(new UUID($comment['uuid']));
        }
        return $comments;
    }

}