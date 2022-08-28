<?php

namespace LksKndb\Php2\http\Actions\Like;

use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exception\Likes\LikeNotFoundException;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Blog\Repositories\LikesRepositories\PostLikesRepositoriesInterface;
use Psr\Log\LoggerInterface;

class FindPostLikeByUUID implements ActionInterface
{
    public function __construct(
        private PostLikesRepositoriesInterface $postLikesRepository,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Request $request): Response
    {
        $this->logger->info("Post like search http-action started");

        try {
            $uuid = $request->query('uuid');
        } catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        try {
            $like = $this->postLikesRepository->getPostLikeByUUID(new UUID($uuid));
        } catch (LikeNotFoundException|InvalidUuidException $e){
            return new ErrorResponse($e->getMessage());
        }

        $this->logger->info("Post like found: $uuid");

        return new SuccessfulResponse([
            'uuid' => (string)$like->getUuid(),
            'post' => (string)$like->getPost()->getPost(),
            'author' => $like->getUser()->getName()->getUsername(),
        ]);
    }
}