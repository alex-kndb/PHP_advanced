<?php

namespace LksKndb\Php2\http\Actions\Like;

use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Repositories\LikesRepositories\LikeNotFoundException;
use LksKndb\Php2\Repositories\LikesRepositories\LikesRepositoriesInterface;

class FindLikeByUUID implements ActionInterface
{
    public function __construct(
        private LikesRepositoriesInterface $likesRepository
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
            $like = $this->likesRepository->getLikeByUUID(new UUID($uuid));
        } catch (LikeNotFoundException|InvalidUuidException $e){
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => (string)$like->getUuid(),
            'post' => (string)$like->getPost()->getPost(),
            'author' => $like->getAuthor()->getName()->getUsername(),
        ]);
    }
}