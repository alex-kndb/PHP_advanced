<?php

namespace LksKndb\Php2\http\Auth;

use LksKndb\Php2\Blog\Exception\AuthException;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\UsersRepositoriesInterface;
use LksKndb\Php2\Blog\User;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\UserNotFoundException;
use LksKndb\Php2\http\Request;
use Psr\Log\LoggerInterface;

class PasswordAuthentication implements PasswordAuthenticationInterface
{

    public function __construct(
        private UsersRepositoriesInterface $usersRepository,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @throws AuthException
     * @throws \JsonException
     */
    public function user(Request $request): User
    {
        try {
            $username = $request->jsonBodyField('username');
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $user = $this->usersRepository->getUserByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!$user->checkPassword($password)) {
            $this->logger->warning('Password authentication: wrong password');
            // throw new AuthException('Wrong password');
            exit;
        }

        $this->logger->info("Password authentication: success");
        return $user;
    }
}