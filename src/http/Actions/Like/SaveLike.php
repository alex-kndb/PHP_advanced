<?php

namespace LksKndb\Php2\http\Actions\Like;

use LksKndb\Php2\Classes\Like;
use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Repositories\LikesRepositories\LikesRepositoriesInterface;
use LksKndb\Php2\Repositories\PostsRepositories\PostsRepositoriesInterface;
use LksKndb\Php2\Repositories\UsersRepositories\UsersRepositoriesInterface;

class SaveLike implements ActionInterface
{
    public function __construct(
        private likesRepositoriesInterface $likesRepository,
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
            $like = new Like(
                UUID::createUUID(),
                $post,
                $user,
            );
        } catch (HttpException | \JsonException$e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->likesRepository->saveLike($like);

        return new SuccessfulResponse(
            ['uuid' => (string)($like->getUuid())]
        );
    }
}