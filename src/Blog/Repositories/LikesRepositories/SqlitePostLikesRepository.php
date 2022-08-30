<?php

namespace LksKndb\Php2\Blog\Repositories\LikesRepositories;

use DateTimeImmutable;
use LksKndb\Php2\Blog\Name;
use LksKndb\Php2\Blog\Post;
use LksKndb\Php2\Blog\PostLike;
use LksKndb\Php2\Blog\User;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exception\Likes\LikeNotFoundException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\Exceptions\Likes\PostIsAlreadyLikedByThisUser;
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
            'SELECT posts_likes.uuid,posts_likes.author AS like_author,posts_likes.post,users.username,users.first_name,users.last_name,users.password,users.registration,posts.author AS post_author ,posts.title,posts.text
                    FROM posts_likes
                    LEFT JOIN posts ON posts_likes.post=posts.uuid
                    LEFT JOIN  users ON posts_likes.author=users.uuid
                    WHERE posts_likes.uuid=:uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        return $this->getPostLike($statement, $uuid);
    }

    /**
     * @throws InvalidUuidException
     * @throws LikeNotFoundException
     */
    private function getPostLike(PDOStatement $statement, string $uuid): PostLike
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if(!$result) {
            $this->logger->warning("DB: post/like (UUID: $uuid) not found!");
            // throw new LikeNotFoundException("DB: post/like (UUID: $uuid) not found!");
            exit;
        }

        $like_author = new User(
            new UUID($result['like_author']),
            new Name(
                $result['first_name'],
                $result['last_name'],
                $result['username']
            ),
            $result['password'],
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
            $result['password'],
            DateTimeImmutable::createFromFormat('Y-m-d\ H:i:s', $post_author_result['registration'])
        );

        $post = new Post(
            new UUID($result['post']),
            $post_author,
            $result['title'],
            $result['text']
        );

        return new PostLike(
            new UUID($result['uuid']),
            $like_author,
            $post);
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