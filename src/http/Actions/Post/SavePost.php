<?php

namespace LksKndb\Php2\http\Actions;

use LksKndb\Php2\Classes\Post;
use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Repositories\PostsRepositories\PostsRepositoriesInterface;

class SavePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoriesInterface $postsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $post = new Post(
                UUID::createUUID(),
                new UUID($request->jsonBodyField('author')),
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->postsRepository->savePost($post);

        return new SuccessfulResponse(
            ['uuid' => (string)($post->getPost())]
        );
    }
}