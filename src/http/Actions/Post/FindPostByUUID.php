<?php

namespace LksKndb\Php2\http\Actions\Post;

use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Blog\Repositories\PostsRepositories\PostsRepositoriesInterface;
use Psr\Log\LoggerInterface;

class FindPostByUUID implements ActionInterface
{
    public function __construct(
        private PostsRepositoriesInterface $postsRepository,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Request $request): Response
    {
        $this->logger->info("Post search http-action started");

        try {
            $uuid = $request->query('uuid');
        } catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        try {
            $post = $this->postsRepository->getPostByUUID(new UUID($uuid));
        } catch (InvalidUuidException $e){
            return new ErrorResponse($e->getMessage());
        }

        $this->logger->info("Post found: $uuid");

        return new SuccessfulResponse([
            'uuid' => (string)$post->getPost(),
            'author' => $post->getAuthor()->getName()->getUsername(),
            'title' => $post->getTitle(),
            'text' => $post->getText(),
        ]);
    }
}