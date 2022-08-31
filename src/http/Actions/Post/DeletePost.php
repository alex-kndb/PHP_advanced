<?php

namespace LksKndb\Php2\http\Actions\Post;

use LksKndb\Php2\blog\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Blog\Repositories\PostsRepositories\PostsRepositoriesInterface;
use Psr\Log\LoggerInterface;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoriesInterface $postsRepository,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Request $request): Response
    {
        $this->logger->info("Post delete http-action started");

        try {
            $uuid = new UUID($request->query('uuid'));
        } catch (HttpException | InvalidUuidException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->postsRepository->deletePost($uuid);

        $this->logger->info("Post deleted: $uuid");

        return new SuccessfulResponse(
            ['uuid' => (string)$uuid]
        );
    }
}