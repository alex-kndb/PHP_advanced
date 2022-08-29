<?php

namespace LksKndb\Php2\http\Actions\Post;

use LksKndb\Php2\Blog\Exception\AuthException;
use LksKndb\Php2\Blog\Post;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\Auth\AuthenticationInterface;
use LksKndb\Php2\http\Auth\TokenAuthentication;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Blog\Repositories\PostsRepositories\PostsRepositoriesInterface;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{
//        private UsersRepositoriesInterface $usersRepository

    public function __construct(
        private PostsRepositoriesInterface $postsRepository,
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
        $this->logger->info("Post create http-action started");

        try {
            $author = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $uuid = UUID::createUUID();
        try {
            $post = new Post(
                $uuid,
                $author,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException | \JsonException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->postsRepository->savePost($post);

        $this->logger->info("Post created: $uuid");

        return new SuccessfulResponse(
            ['uuid' => (string)($post->getPost())]
        );
    }
}