<?php

namespace LksKndb\Php2\http\Actions\Like;

use LksKndb\Php2\Blog\Repositories\LikesRepositories\CommentLikesRepositoriesInterface;
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

class GetLikesByCommentUUID implements ActionInterface
{
    public function __construct(
        private CommentLikesRepositoriesInterface $commentLikesRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $comment = $request->query('uuid');
        } catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        try {
            $comments = $this->commentLikesRepository->getLikesByCommentUUID(new UUID($comment));
        } catch (LikeNotFoundException|InvalidUuidException $e){
            return new ErrorResponse($e->getMessage());
        }

        $commentsArray = [];
        foreach ($comments as $comment){
            $commentsArray[] = [
                'uuid' => (string)$comment->getUuid(),
                'comment' => (string)$comment->getPost()->getPost(),
                'author' => $comment->getUser()->getName()->getUsername(),
            ];
        }
        return new SuccessfulResponse($commentsArray);
    }
}