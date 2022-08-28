<?php

namespace LksKndb\Php2\http\Actions\Like;

use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Blog\Repositories\LikesRepositories\PostLikesRepositoriesInterface;
use Psr\Log\LoggerInterface;

class DeletePostLike implements ActionInterface
{
    public function __construct(
        private PostLikesRepositoriesInterface $postLikesRepository,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Request $request): Response
    {
        $this->logger->info("Post like delete http-action started");

        try {
            $uuid = new UUID($request->query('uuid'));
        } catch (HttpException | InvalidUuidException $e) {
            (new ErrorResponse($e->getMessage()))->send();
        }

        $this->postLikesRepository->deletePostLike($uuid);

        $this->logger->info("Post like deleted: $uuid");

        return new SuccessfulResponse(
            ['uuid' => (string)$uuid]
        );
    }
}