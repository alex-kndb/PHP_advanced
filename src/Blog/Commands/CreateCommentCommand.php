<?php

namespace LksKndb\Php2\Blog\Commands;

use LksKndb\Php2\Blog\Comment;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\ArgumentNotExistException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\Blog\Repositories\CommentsRepositories\CommentsRepositoriesInterface;

class CreateCommentCommand
{
    public function __construct(
        private CommentsRepositoriesInterface $commentsRepository
    ){}

    /**
     * @throws ArgumentNotExistException
     * @throws InvalidUuidException
     */
    public function handle(Arguments $args): void
    {
        $this->commentsRepository->saveComment(
            new Comment(
                UUID::createUUID(),
                new UUID($args->get('post')),
                new UUID($args->get('author')),
                $args->get('text')
            )
        );
    }

    /**
     * @throws InvalidUuidException
     */
    public function get(string $uuid): Comment
    {
        return $this->commentsRepository->getCommentByUUID(new UUID($uuid));
    }
}
