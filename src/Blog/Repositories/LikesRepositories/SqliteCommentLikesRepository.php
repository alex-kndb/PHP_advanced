<?php

namespace LksKndb\Php2\Blog\Repositories\LikesRepositories;

use DateTimeImmutable;
use LksKndb\Php2\Blog\Comment;
use LksKndb\Php2\Blog\CommentLike;
use LksKndb\Php2\Blog\Name;
use LksKndb\Php2\Blog\Post;
use LksKndb\Php2\Blog\User;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exception\Likes\LikeNotFoundException;
use LksKndb\Php2\Exceptions\Likes\CommentIsAlreadyLikedByThisUser;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use PDO;
use PDOStatement;

class SqliteCommentLikesRepository implements CommentLikesRepositoriesInterface
{
    public function __construct(
        private PDO $connection
    ){}

    public function save(CommentLike $like): void
    {
        $comment = $like->getComment()->getUuid();
        $user = $like->getUser()->getUUID();
        if($this->isCommentAlreadyLiked($comment, $user)){
            throw new CommentIsAlreadyLikedByThisUser("Comment is already liked by this user: $user");
        }
        $statement = $this->connection->prepare(
            'INSERT INTO comments_likes (uuid, comment, user) VALUES (:uuid, :comment, :user)'
        );
        $statement->execute([
            ':uuid' => $like->getUuid(),
            ':comment' => $comment,
            ':user' => $user
        ]);
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
     * @throws InvalidUuidException
     * @throws LikeNotFoundException
     */
    public function getCommentLikeByUUID(UUID $uuid): CommentLike
    {
        $statement = $this->connection->prepare(
            'SELECT comments_likes.uuid,comments_likes.user AS like_author,comments_likes.comment,users.username,users.first_name,users.last_name,users.registration,comments.comment, comments.post, comments.author AS comment_author
                    FROM comments_likes
                    LEFT JOIN comments ON comments_likes.comment=comments.id
                    LEFT JOIN users ON comments_likes.user=users.uuid
                    WHERE comments_likes.uuid=:uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
        return $this->getCommentLike($statement, $uuid);
    }

    /**
     * @throws InvalidUuidException
     * @throws LikeNotFoundException
     */
    private function getCommentLike(PDOStatement $statement, string $uuid): CommentLike
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if(!$result) {
            throw new LikeNotFoundException("DB: comment/like (UUID: $uuid) not found!");
        }

        $comment_result = $this->query('comments', $result['comment']);
        $comment_author_result = $this->query('users', $comment_result['author']);
        $post_result = $this->query('posts', $result['post']);
        $post_author_result = $this->query('users', $post_result['author']);

        $post = new Post(
            new UUID($post_result['uuid']),
            new User(
                new UUID($post_author_result['uuid']),
                new Name(
                    $post_author_result['first_name'],
                    $post_author_result['last_name'],
                    $post_author_result['username']
                ),
                DateTimeImmutable::createFromFormat('Y-m-d\ H:i:s', $post_author_result['registration'])
            ),
            $post_result['title'],
            $post_result['text']
        );

        $comment = new Comment(
            new UUID($comment_result['id']),
            $post,
            new User(
                new UUID($comment_author_result['uuid']),
                new Name(
                    $comment_author_result['first_name'],
                    $comment_author_result['last_name'],
                    $comment_author_result['username']
                ),
                DateTimeImmutable::createFromFormat('Y-m-d\ H:i:s', $comment_author_result['registration'])
            ),
            $result['text']
        );

        return new CommentLike(
            new UUID($result['uuid']),
            new User(
                new UUID($result['user']),
                new Name(
                    $result['first_name'],
                    $result['last_name'],
                    $result['username']
                ),
                DateTimeImmutable::createFromFormat('Y-m-d\ H:i:s', $result['registration'])
            ),
            $comment);
    }

    public function deleteCommentLike(UUID $id): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM comments_likes WHERE uuid = :id'
        );
        $statement->execute([
            ':id' => (string)$id,
        ]);
    }

    public function query(string $table, UUID $uuid) : ?array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM :table WHERE uuid = :uuid'
        );

        $statement->execute([
                ':table' => $table,
                ':uuid' => (string)$uuid
            ]);

        return $statement->fetch(PDO::FETCH_ASSOC) ?? null;
    }

    /**
     * @throws InvalidUuidException
     * @throws LikeNotFoundException
     */
    public function getLikesByCommentUUID(UUID $comment) : ?array
    {
        $statement = $this->connection->prepare(
            'SELECT uuid FROM comments_likes WHERE comment = :comment'
        );

        $statement->execute([
            ':comment' => (string)$comment
        ]);

        $comments_result = $statement->fetch(PDO::FETCH_ASSOC);
        $comments = [];
        foreach ($comments_result as $comment){
            $comments[] = $this->getCommentLikeByUUID(new UUID($comment['uuid']));
        }
        return $comments;
    }

}