<?php

namespace LksKndb\Php2\http\Actions\Like;

use LksKndb\Php2\Blog\Repositories\LikesRepositories\CommentLikesRepositoriesInterface;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class DeleteCommentLike implements ActionInterface
{
    public function __construct(
        private CommentLikesRepositoriesInterface $CommentLikesRepository,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Request $request): Response
    {
        $this->logger->info("Comment like delete http-action started");

        try {
            $uuid = new UUID($request->query('uuid'));
        } catch (HttpException | InvalidUuidException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->CommentLikesRepository->deleteCommentLike($uuid);

        $this->logger->info("Comment like deleted: $uuid");

        return new SuccessfulResponse(
            ['uuid' => (string)$uuid]
        );
    }
}