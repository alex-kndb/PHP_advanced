<?php

namespace LksKndb\Php2\Repositories\LikesRepositories;

use DateTimeImmutable;
use LksKndb\Php2\Classes\Like;
use LksKndb\Php2\Classes\Name;
use LksKndb\Php2\Classes\Post;
use LksKndb\Php2\Classes\User;
use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\Exceptions\Likes\PostIsAlreadyLikedByThisUser;
use PDO;
use PDOStatement;

class SqliteLikesRepository implements LikesRepositoriesInterface
{
    public function __construct(
        private PDO $connection
    ){}

    /**
     * @throws PostIsAlreadyLikedByThisUser
     */
    public function saveLike(Like $like): void
    {
        $post = $like->getPost()->getPost();
        $author = $like->getAuthor()->getUUID();
        if($this->isAlreadyLiked($post, $author)){
            throw new PostIsAlreadyLikedByThisUser("Post is already liked by this user: $author");
        }
        $statement = $this->connection->prepare(
            'INSERT INTO likes (uuid, post, author) VALUES (:uuid, :post, :author)'
        );
        $statement->execute([
            ':uuid' => $like->getUuid(),
            ':post' => $like->getPost()->getPost(),
            ':author' => $like->getAuthor()->getUUID()
        ]);
    }

    public function isAlreadyLiked(UUID $post, UUID $user) : bool
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE post=:post_id AND author=:user_id'
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
    public function getLikeByUUID(UUID $uuid): Like
    {
        $statement = $this->connection->prepare(
            'SELECT *
                    FROM likes
                    LEFT JOIN posts ON likes.post=posts.uuid
                    LEFT JOIN  users ON likes.author=users.uuid
                    WHERE likes.uuid=:uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        return $this->getLike($statement, $uuid);
    }

    /**
     * @throws InvalidUuidException
     * @throws LikeNotFoundException
     */
    private function getLike(PDOStatement $statement, string $uuid): Like
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if(!$result) {
            throw new LikeNotFoundException("DB: like (UUID: $uuid) not found!");
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

        return new Like(
            new UUID($result['uuid']),
            $post,
            $user);
    }

    public function deleteLike(UUID $id): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM likes WHERE uuid = :id'
        );
        $statement->execute([
            ':id' => (string)$id,
        ]);
    }
}