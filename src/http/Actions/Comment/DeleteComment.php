<?php

namespace LksKndb\Php2\http\Actions;

use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Repositories\CommentsRepositories\CommentsRepositoriesInterface;

class DeleteComment implements ActionInterface
{
    public function __construct(
        private CommentsRepositoriesInterface $commentsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
        } catch (HttpException | InvalidUuidException $e) {
            (new ErrorResponse($e->getMessage()))->send();
        }

        $this->commentsRepository->deleteComment($uuid);

        return new SuccessfulResponse(
            ['uuid' => (string)$uuid]
        );
    }
}