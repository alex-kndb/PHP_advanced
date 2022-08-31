<?php

namespace LksKndb\Php2\Blog\Repositories\LikesRepositories;

use DateTimeImmutable;
use LksKndb\Php2\Blog\Name;
use LksKndb\Php2\Blog\Post;
use LksKndb\Php2\Blog\PostLike;
use LksKndb\Php2\Blog\Repositories\PostsRepositories\SqlitePostsRepository;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use LksKndb\Php2\Blog\User;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exception\Likes\LikeNotFoundException;
use LksKndb\Php2\Exceptions\Posts\PostNotFoundException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\Exceptions\User\UserNotFoundException;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqlitePostLikesRepository implements PostLikesRepositoriesInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ){}

    public function save(PostLike $like): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts_likes (uuid, post, author) VALUES (:uuid, :post, :author)'
        );
        $statement->execute([
            ':uuid' => $like->getUuid(),
            ':post' => $like->getPost()->getPost(),
            ':author' => $like->getUser()->getUUID()
        ]);

        $this->logger->info("SqlitePostLikeRepo -> post like created: {$like->getUuid()}");
    }

    public function isPostAlreadyLiked(UUID $post, UUID $user) : bool
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts_likes WHERE post=:post_id AND author=:user_id'
        );
        $statement->execute([
            ':post_id' => (string)$post,
            ':user_id' => (string)$user
        ]);
        return (bool)$statement->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @throws InvalidUuidException
     * @throws LikeNotFoundException
     */
    public function getPostLikeByUUID(UUID $uuid): PostLike
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts_likes WHERE uuid=:uuid'
        );

        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        return $this->getPostLike($statement, $uuid);
    }

    /**
     * @throws InvalidUuidException
     * @throws UserNotFoundException
     * @throws PostNotFoundException
     */
    private function getPostLike(PDOStatement $statement, string $uuid): PostLike
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if(!$result) {
            $this->logger->warning("DB: post/like (UUID: $uuid) not found!");
            // throw new LikeNotFoundException("DB: post/like (UUID: $uuid) not found!");
            exit;
        }

        $usersRepo = new SqliteUsersRepository($this->connection,$this->logger);
        $postsRepo = new SqlitePostsRepository($this->connection,$this->logger);

        return new PostLike(
            new UUID($result['uuid']),
            $usersRepo->getUserByUUID(new UUID($result['author'])),
            $postsRepo->getPostByUUID(new UUID($result['post']))
        );
    }

    public function deletePostLike(UUID $id): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM posts_likes WHERE uuid = :id'
        );
        $statement->execute([
            ':id' => (string)$id,
        ]);
    }

    /**
     * @throws InvalidUuidException
     * @throws LikeNotFoundException
     */
    public function getLikesByPostUUID(UUID $post) : ?array
    {
        $statement = $this->connection->prepare(
            'SELECT uuid FROM posts_likes WHERE post = :post'
        );

        $statement->execute([
            ':post' => (string)$post
        ]);

        $likes_result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $likes = [];
        foreach ($likes_result as $like){
            $likes[] = $this->getPostLikeByUUID(new UUID($like['uuid']));
        }
        return $likes;
    }

}