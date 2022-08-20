<?php

namespace LksKndb\Php2\http\Actions;

use LksKndb\Php2\Classes\Comment;
use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Repositories\CommentsRepositories\CommentsRepositoriesInterface;

class SaveComment implements ActionInterface
{
    public function __construct(
        private commentsRepositoriesInterface $commentsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $comment = new Comment(
                UUID::createUUID(),
                new UUID($request->jsonBodyField('post')),
                new UUID($request->jsonBodyField('author')),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->commentsRepository->saveComment($comment);

        return new SuccessfulResponse(
            ['uuid' => (string)($comment->getUuid())]
        );
    }
}