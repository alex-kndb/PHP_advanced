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

class DeleteCommentLike implements ActionInterface
{
    public function __construct(
        private CommentLikesRepositoriesInterface $CommentLikesRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
        } catch (HttpException | InvalidUuidException $e) {
            (new ErrorResponse($e->getMessage()))->send();
        }

        $this->CommentLikesRepository->deleteCommentLike($uuid);

        return new SuccessfulResponse(
            ['uuid' => (string)$uuid]
        );
    }
}