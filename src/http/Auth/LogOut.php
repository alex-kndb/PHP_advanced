<?php

namespace LksKndb\Php2\http\Auth;

use DateTimeImmutable;
use LksKndb\Php2\Blog\Exception\AuthException;
use LksKndb\Php2\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use LksKndb\Php2\Blog\User;
use LksKndb\Php2\http\Actions\ActionInterface;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;
use LksKndb\Php2\http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class LogOut implements ActionInterface
{
    private const HEADER_PREFIX = 'Crab ';
    
    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository,
        private TokenAuthentication $tokenAuthentication,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @throws AuthException
     */
    public function handle(Request $request): Response
    {

        $validTokenUuid = $this->tokenAuthentication->getTokenFromHeader($request, self::HEADER_PREFIX);
        $validToken = $this->authTokensRepository->get($validTokenUuid);
        
        $expiredToken = new AuthToken(
            $validToken->token(),
            $validToken->userUuid(),
            new DateTimeImmutable()
        );
        
        $this->authTokensRepository->save($expiredToken);

        $this->logger->info("Token expired: ".$expiredToken->token());

        return new SuccessfulResponse([
            'token' => $expiredToken->token(),
        ]);
    }


}