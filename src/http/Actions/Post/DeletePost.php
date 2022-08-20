<?php

namespace LksKndb\Php2\http\Actions\Post;

use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Repositories\PostsRepositories\PostsRepositoriesInterface;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoriesInterface $postsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
        } catch (HttpException | InvalidUuidException $e) {
            (new ErrorResponse($e->getMessage()))->send();
        }

        $this->postsRepository->deletePost($uuid);

        return new SuccessfulResponse(
            ['uuid' => (string)$uuid]
        );
    }
}