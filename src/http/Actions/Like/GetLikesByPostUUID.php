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

class GetLikesByPostUUID implements ActionInterface
{
    public function __construct(
        private PostLikesRepositoriesInterface $postLikesRepository
    ) {
    }

    public function handle(Request $request): Response
    {
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
        foreach ($likes as $like){
            $likesArray[] = [
                'uuid' => (string)$like->getUuid(),
                'post' => (string)$like->getPost()->getPost(),
                'author' => $like->getUser()->getName()->getUsername(),
            ];
        }
        return new SuccessfulResponse($likesArray);
    }
}