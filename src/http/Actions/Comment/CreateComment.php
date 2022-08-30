<?php

namespace LksKndb\Php2\http\Actions\Comment;

use LksKndb\Php2\Blog\Comment;
use LksKndb\Php2\Blog\Exception\AuthException;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\Auth\TokenAuthentication;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Blog\Repositories\CommentsRepositories\CommentsRepositoriesInterface;
use LksKndb\Php2\Blog\Repositories\PostsRepositories\PostsRepositoriesInterface;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\UsersRepositoriesInterface;
use Psr\Log\LoggerInterface;

class CreateComment implements ActionInterface
{
    public function __construct(
        private commentsRepositoriesInterface $commentsRepository,
        private postsRepositoriesInterface $postsRepository,
        private TokenAuthentication $authentication,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @throws AuthException
     * @throws \JsonException
     */
    public function handle(Request $request): Response
    {
        $this->logger->info("Comment create http-action started");

        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
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

        $uuid = UUID::createUUID();

        try {
            $comment = new Comment(
                $uuid,
                $post,
                $user,
                $request->jsonBodyField('text'),
            );
        } catch (HttpException | \JsonException$e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->commentsRepository->saveComment($comment);

        $this->logger->info("Comment created: $uuid");

        return new SuccessfulResponse(
            ['uuid' => (string)($comment->getUuid())]
        );
    }
}