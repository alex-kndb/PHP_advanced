<?php

namespace LksKndb\Php2\http\Actions\Comment;

use LksKndb\Php2\Classes\Comment;
use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Repositories\CommentsRepositories\CommentsRepositoriesInterface;
use LksKndb\Php2\Repositories\PostsRepositories\PostsRepositoriesInterface;
use LksKndb\Php2\Repositories\UsersRepositories\UsersRepositoriesInterface;

class SaveComment implements ActionInterface
{
    public function __construct(
        private commentsRepositoriesInterface $commentsRepository,
        private postsRepositoriesInterface $postsRepository,
        private usersRepositoriesInterface $usersRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $author_id = $request->jsonBodyField('author');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $user = $this->usersRepository->getUserByUUID(new UUID($author_id));
        } catch (HttpException | InvalidUuidException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $post_id = $request->jsonBodyField('post');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $post = $this->postsRepository->getPostByUUID(new UUID($post_id));
        } catch (HttpException | InvalidUuidException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $comment = new Comment(
                UUID::createUUID(),
                $post,
                $user,
                $request->jsonBodyField('text'),
            );
        } catch (HttpException | \JsonException$e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->commentsRepository->saveComment($comment);

        return new SuccessfulResponse(
            ['uuid' => (string)($comment->getUuid())]
        );
    }
}