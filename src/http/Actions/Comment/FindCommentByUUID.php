<?php

namespace LksKndb\Php2\http\Actions\Comment;

use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\Exceptions\User\UserNotFoundException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Blog\Repositories\CommentsRepositories\CommentsRepositoriesInterface;
use Psr\Log\LoggerInterface;

class FindCommentByUUID implements ActionInterface
{
    public function __construct(
        private CommentsRepositoriesInterface $commentsRepository,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Request $request): Response
    {
        $this->logger->info("Comment search http-action started");

        try {
            $uuid = $request->query('uuid');
        } catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        try {
            $comment = $this->commentsRepository->getCommentByUUID(new UUID($uuid));
        } catch (InvalidUuidException $e){
            return new ErrorResponse($e->getMessage());
        }

        $this->logger->info("Comment found: $uuid");

        return new SuccessfulResponse([
            'uuid' => (string)$comment->getUuid(),
            'post' => (string)$comment->getPost()->getPost(),
            'author' => $comment->getAuthor()->getName()->getUsername(),
            'text' => $comment->getText(),
        ]);
    }
}