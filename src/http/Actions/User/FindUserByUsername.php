<?php

namespace LksKndb\Php2\http\Actions\User;

use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\User\UserNotFoundException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Repositories\UsersRepositories\UsersRepositoriesInterface;

class FindUserByUsername implements ActionInterface
{
    public function __construct(
        private UsersRepositoriesInterface $usersRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $username = $request->query('username');
        } catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        try {
            $user = $this->usersRepository->getUserByUsername($username);
        } catch (UserNotFoundException $e){
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => (string)$user->getUUID(),
            'username' => $user->getName()->getUsername(),
            'first_name' => $user->getName()->getFirstName(),
            'last_name' => $user->getName()->getLastName(),
            'registeredOn' => $user->getRegisteredOn()->format('Y-m-d\ H:i:s')
        ]);
    }
}