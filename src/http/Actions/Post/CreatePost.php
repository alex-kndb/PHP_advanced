<?php

namespace LksKndb\Php2\http\Actions\Post;

use LksKndb\Php2\Blog\http\Auth\IdentificationInterface;
use LksKndb\Php2\Blog\Post;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Blog\Repositories\PostsRepositories\PostsRepositoriesInterface;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\UsersRepositoriesInterface;

class CreatePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoriesInterface $postsRepository,
//        private UsersRepositoriesInterface $usersRepository
        private IdentificationInterface $identification
    ) {
    }

    public function handle(Request $request): Response
    {

        $user = $this->identification->user($request);

        try {
            $post = new Post(
                UUID::createUUID(),
                $user,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException | \JsonException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->postsRepository->savePost($post);

        return new SuccessfulResponse(
            ['uuid' => (string)($post->getPost())]
        );
    }
}