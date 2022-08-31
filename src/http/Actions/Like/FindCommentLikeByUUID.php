<?php

namespace LksKndb\Php2\http\Actions\Like;

use LksKndb\Php2\Blog\Repositories\LikesRepositories\CommentLikesRepositoriesInterface;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class FindCommentLikeByUUID implements ActionInterface
{
    public function __construct(
        private CommentLikesRepositoriesInterface $commentLikesRepository,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Request $request): Response
    {
        $this->logger->info("Comment like search http-action started");

        try {
            $uuid = $request->query('uuid');
        } catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        try {
            $like = $this->commentLikesRepository->getCommentLikeByUUID(new UUID($uuid));
        } catch (InvalidUuidException $e){
            return new ErrorResponse($e->getMessage());
        }

        $this->logger->info("Comment like found: $uuid");

        return new SuccessfulResponse([
            'uuid' => (string)$like->getUuid(),
            'comment' => (string)$like->getComment()->getUuid(),
            'author' => $like->getUser()->getName()->getUsername(),
        ]);
    }
}