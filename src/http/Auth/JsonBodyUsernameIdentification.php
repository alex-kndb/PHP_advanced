<?php

namespace LksKndb\Php2\http\Auth;

use InvalidArgumentException;
use JsonException;
use LksKndb\Php2\Blog\Exception\AuthException;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\UsersRepositoriesInterface;
use LksKndb\Php2\Blog\User;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\http\Request;

class JsonBodyUsernameIdentification implements IdentificationInterface
{
    public function __construct(
        private UsersRepositoriesInterface $usersRepository
    ) {
    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {

        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException | InvalidArgumentException | JsonException $e) {
            throw new AuthException($e->getMessage());
        }

        return $this->usersRepository->getUserByUsername($username);
    }
}