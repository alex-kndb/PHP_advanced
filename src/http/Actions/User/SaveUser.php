<?php

namespace LksKndb\Php2\http\Actions;

use DateTimeImmutable;
use LksKndb\Php2\Classes\Name;
use LksKndb\Php2\Classes\User;
use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Repositories\UsersRepositories\UsersRepositoriesInterface;

class SaveUser implements ActionInterface
{
    public function __construct(
        private UsersRepositoriesInterface $usersRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $user = new User(
                UUID::createUUID(),
                new Name(
                    $request->jsonBodyField('first_name'),
                    $request->jsonBodyField('last_name'),
                    $request->jsonBodyField('username')
                ),
                new DateTimeImmutable()
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->usersRepository->saveUser($user);

        return new SuccessfulResponse(
            ['uuid' => (string)($user->getUUID())]
        );
    }
}