<?php

namespace LksKndb\Php2\http\Actions\Like;

use JsonException;
use LksKndb\Php2\Blog\CommentLike;
use LksKndb\Php2\Blog\Exception\AuthException;
use LksKndb\Php2\Blog\Repositories\CommentsRepositories\CommentsRepositoriesInterface;
use LksKndb\Php2\Blog\Repositories\LikesRepositories\CommentLikesRepositoriesInterface;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\Auth\TokenAuthentication;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreateCommentLike implements ActionInterface
{
    public function __construct(
        private CommentLikesRepositoriesInterface $commentLikesRepository,
        private CommentsRepositoriesInterface     $commentsRepository,
        private TokenAuthentication $authentication,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @throws AuthException
     */
    public function handle(Request $request): Response
    {
        $this->logger->info("Post like create http-action started");

        $user = $this->authentication->user($request);

        try {
            $comment_id = $request->jsonBodyField('comment');
        } catch (HttpException | JsonException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $comment = $this->commentsRepository->getCommentByUUID(new UUID($comment_id));
        } catch (InvalidUuidException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $uuid = UUID::createUUID();
        $commentLike = new CommentLike(
            $uuid,
            $user,
            $comment
        );

        $this->commentLikesRepository->save($commentLike);

        $this->logger->info("Post like created: $uuid");

        return new SuccessfulResponse(
            ['uuid' => (string)($commentLike->getUuid())]
        );
    }
}