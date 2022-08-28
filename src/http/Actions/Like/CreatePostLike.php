<?php

namespace LksKndb\Php2\http\Actions\Like;

use LksKndb\Php2\Blog\PostLike;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Blog\Repositories\LikesRepositories\PostLikesRepositoriesInterface;
use LksKndb\Php2\Blog\Repositories\PostsRepositories\PostsRepositoriesInterface;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\UsersRepositoriesInterface;

class CreatePostLike implements ActionInterface
{
    public function __construct(
        private PostLikesRepositoriesInterface $postLikesRepository,
        private postsRepositoriesInterface     $postsRepository,
        private usersRepositoriesInterface     $usersRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $user_id = $request->jsonBodyField('author');
            $post_id = $request->jsonBodyField('post');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $user = $this->usersRepository->getUserByUUID(new UUID($user_id));
            $post = $this->postsRepository->getPostByUUID(new UUID($post_id));
        } catch (HttpException | InvalidUuidException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $postLike = new PostLike(
                UUID::createUUID(),
                $user,
                $post
            );
        } catch (HttpException | \JsonException$e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->postLikesRepository->save($postLike);

        return new SuccessfulResponse(
            ['uuid' => (string)($postLike->getUuid())]
        );
    }
}