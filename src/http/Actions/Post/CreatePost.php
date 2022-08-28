<?php

namespace LksKndb\Php2\http\Actions\Post;

use http\Exception\InvalidArgumentException;
use LksKndb\Php2\Blog\Exception\AuthException;
use LksKndb\Php2\Blog\Post;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\UsersRepositoriesInterface;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\UserNotFoundException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\Auth\IdentificationInterface;
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
        private IdentificationInterface $identification,
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

        $user = $this->identification->user($request);

        $uuid = UUID::createUUID();
        try {
            $post = new Post(
                $uuid,
                $user,
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