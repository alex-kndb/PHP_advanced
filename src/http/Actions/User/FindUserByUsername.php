<?php

namespace LksKndb\Php2\http\Actions\User;

use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\UsersRepositoriesInterface;
use Psr\Log\LoggerInterface;

class FindUserByUsername implements ActionInterface
{
    public function __construct(
        private UsersRepositoriesInterface $usersRepository,
        private LoggerInterface $logger
    ) {
    }

    public function handle(Request $request): Response
    {
        $this->logger->info("User search http-action started");

        try {
            $username = $request->query('username');
        } catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        $user = $this->usersRepository->getUserByUsername($username);

        $this->logger->info("User found: $username");

        return new SuccessfulResponse([
            'uuid' => (string)$user->getUUID(),
            'username' => $user->getName()->getUsername(),
            'first_name' => $user->getName()->getFirstName(),
            'last_name' => $user->getName()->getLastName(),
            'password' => $user->hashedPassword(),
            'registeredOn' => $user->getRegisteredOn()->format('Y-m-d\ H:i:s')
        ]);
    }
}