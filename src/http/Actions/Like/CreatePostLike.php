<?php

namespace LksKndb\Php2\http\Actions\Like;

use JsonException;
use LksKndb\Php2\Blog\Exception\AuthException;
use LksKndb\Php2\Blog\PostLike;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\Auth\TokenAuthentication;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Blog\Repositories\LikesRepositories\PostLikesRepositoriesInterface;
use LksKndb\Php2\Blog\Repositories\PostsRepositories\PostsRepositoriesInterface;
use Psr\Log\LoggerInterface;

class CreatePostLike implements ActionInterface
{
    public function __construct(
        private PostLikesRepositoriesInterface $postLikesRepository,
        private postsRepositoriesInterface     $postsRepository,
        private TokenAuthentication $authentication,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Request $request): Response
    {
        $this->logger->info("Post like create http-action started");

        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $post_id = $request->jsonBodyField('post');
        } catch (HttpException | JsonException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $post = $this->postsRepository->getPostByUUID(new UUID($post_id));
        } catch (InvalidUuidException $e) {
            return new ErrorResponse($e->getMessage());
        }

        if($this->postLikesRepository->isPostAlreadyLiked($post->getPost(), $user->getUUID())){
            $mess = "Post is already liked by this user: $user";
            $this->logger->warning($mess);
            // throw new PostIsAlreadyLikedByThisUser($mess);
            return new ErrorResponse($mess);
        }

        $uuid = UUID::createUUID();
        $postLike = new PostLike(
            $uuid,
            $user,
            $post
        );

        $this->postLikesRepository->save($postLike);

        $this->logger->info("Post like created: $uuid");

        return new SuccessfulResponse(
            ['uuid' => (string)($postLike->getUuid())]
        );
    }
}