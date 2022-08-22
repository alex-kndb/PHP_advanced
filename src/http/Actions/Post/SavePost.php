<?php

namespace LksKndb\Php2\http\Actions\Post;

use LksKndb\Php2\Classes\Post;
use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Repositories\PostsRepositories\PostsRepositoriesInterface;
use LksKndb\Php2\Repositories\UsersRepositories\UsersRepositoriesInterface;

class SavePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoriesInterface $postsRepository,
        private UsersRepositoriesInterface $usersRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $author_id = $request->jsonBodyField('author');
        } catch (HttpException | \JsonException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $user = $this->usersRepository->getUserByUUID(new UUID($author_id));
        } catch (HttpException | InvalidUuidException $e) {
            return new ErrorResponse($e->getMessage());
        }

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