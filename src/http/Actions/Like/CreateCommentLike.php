<?php

namespace LksKndb\Php2\http\Actions\Like;

use LksKndb\Php2\Blog\CommentLike;
use LksKndb\Php2\Blog\Repositories\CommentsRepositories\CommentsRepositoriesInterface;
use LksKndb\Php2\Blog\Repositories\LikesRepositories\CommentLikesRepositoriesInterface;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\UsersRepositoriesInterface;

class CreateCommentLike implements ActionInterface
{
    public function __construct(
        private CommentLikesRepositoriesInterface $commentLikesRepository,
        private CommentsRepositoriesInterface     $commentsRepository,
        private UsersRepositoriesInterface     $usersRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $user_id = $request->jsonBodyField('author');
            $comment_id = $request->jsonBodyField('comment');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $user = $this->usersRepository->getUserByUUID(new UUID($user_id));
            $comment = $this->commentsRepository->getCommentByUUID(new UUID($comment_id));
        } catch (HttpException | InvalidUuidException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $commentLike = new CommentLike(
                UUID::createUUID(),
                $user,
                $comment
            );
        } catch (HttpException | \JsonException$e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->commentLikesRepository->save($commentLike);

        return new SuccessfulResponse(
            ['uuid' => (string)($commentLike->getUuid())]
        );
    }
}