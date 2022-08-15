<?php

namespace LksKndb\Php2\Commands;

use LksKndb\Php2\Classes\Comment;
use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Exceptions\ArgumentNotExistException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\Repositories\CommentsRepositories\CommentsRepositoriesInterface;

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
