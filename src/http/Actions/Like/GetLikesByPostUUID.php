<?php

namespace LksKndb\Php2\http\Actions\Like;

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
use Psr\Log\LoggerInterface;

class GetLikesByPostUUID implements ActionInterface
{
    public function __construct(
        private PostLikesRepositoriesInterface $postLikesRepository,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Request $request): Response
    {
        $this->logger->info("All post likes search http-action started");

        try {
            $post = $request->query('uuid');
        } catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        try {
            $likes = $this->postLikesRepository->getLikesByPostUUID(new UUID($post));
        } catch (LikeNotFoundException|InvalidUuidException $e){
            return new ErrorResponse($e->getMessage());
        }

        $likesArray = [];
        $uuidsArr = [];
        foreach ($likes as $like){
            $uuid = (string)$like->getUuid();
            $uuidsArr[] = $uuid;
            $likesArray[] = [
                'uuid' => $uuid,
                'post' => (string)$like->getPost()->getPost(),
                'author' => $like->getUser()->getName()->getUsername(),
            ];
        }

        $this->logger->info("All post likes found: ". implode(', ', $uuidsArr));

        return new SuccessfulResponse($likesArray);
    }
}