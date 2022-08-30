<?php

namespace LksKndb\Php2\http\Auth;

use DateTimeImmutable;
use LksKndb\Php2\Blog\Exception\AuthException;
use LksKndb\Php2\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class LogIn implements ActionInterface
{
    public function __construct(
        private PasswordAuthenticationInterface $passwordAuthentication,
        private AuthTokensRepositoryInterface $authTokensRepository,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @throws \Exception
     */
    public function handle(Request $request): Response
    {
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $authToken = new AuthToken(
            bin2hex(random_bytes(40)),
            $user->getUUID(),
            (new DateTimeImmutable())->modify('+1 day')
        );

        $this->authTokensRepository->save($authToken);

        $this->logger->info("Token saved: ".$authToken->token());

        return new SuccessfulResponse([
            'token' => $authToken->token(),
        ]);
    }
}