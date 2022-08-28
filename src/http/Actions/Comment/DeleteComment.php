<?php

namespace LksKndb\Php2\http\Actions\Comment;

use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Blog\Repositories\CommentsRepositories\CommentsRepositoriesInterface;
use Psr\Log\LoggerInterface;

class DeleteComment implements ActionInterface
{
    public function __construct(
        private CommentsRepositoriesInterface $commentsRepository,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Request $request): Response
    {
        $this->logger->info("Comment delete http-action started");

        try {
            $uuid = new UUID($request->query('uuid'));
        } catch (HttpException | InvalidUuidException $e) {
            (new ErrorResponse($e->getMessage()))->send();
        }

        $this->commentsRepository->deleteComment($uuid);

        $this->logger->info("Comment deleted: $uuid");

        return new SuccessfulResponse(
            ['uuid' => (string)$uuid]
        );
    }
}