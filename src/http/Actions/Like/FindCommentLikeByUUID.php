<?php

namespace LksKndb\Php2\http\Actions\Like;

use LksKndb\Php2\Blog\Repositories\LikesRepositories\CommentLikesRepositoriesInterface;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\Comment\CommentNotFoundException;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;

class FindCommentLikeByUUID implements ActionInterface
{
    public function __construct(
        private CommentLikesRepositoriesInterface $commentLikesRepository
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
            $like = $this->commentLikesRepository->getCommentLikeByUUID(new UUID($uuid));
        } catch (CommentNotFoundException|InvalidUuidException $e){
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => (string)$like->getUuid(),
            'comment' => (string)$like->getComment()->getUuid(),
            'author' => $like->getUser()->getName()->getUsername(),
        ]);
    }
}