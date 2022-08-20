<?php

namespace LksKndb\Php2\http\Actions;

use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\Exceptions\User\UserNotFoundException;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Repositories\CommentsRepositories\CommentsRepositoriesInterface;

class FindCommentByUUID implements ActionInterface
{
    public function __construct(
        private CommentsRepositoriesInterface $commentsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = $request->query('uuid');
        } catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        try {
            $comment = $this->commentsRepository->getCommentByUUID(new UUID($uuid));
        } catch (UserNotFoundException|InvalidUuidException $e){
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => (string)$comment->getUuid(),
            'post' => (string)$comment->getPost(),
            'author' => (string)$comment->getAuthor(),
            'text' => $comment->getText(),
        ]);
    }
}