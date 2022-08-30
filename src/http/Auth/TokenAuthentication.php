<?php

namespace LksKndb\Php2\http\Auth;

use DateTimeImmutable;
use LksKndb\Php2\Blog\Exception\AuthException;
use LksKndb\Php2\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\UsersRepositoriesInterface;
use LksKndb\Php2\Blog\User;
use LksKndb\Php2\Exception\AuthTokensRepository\AuthTokenNotFoundException;
use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\http\Request;
use Psr\Log\LoggerInterface;

class TokenAuthentication implements TokenAuthenticationInterface
{
    private const HEADER_PREFIX = 'Crab ';
    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository,
        private UsersRepositoriesInterface    $usersRepository,
        private LoggerInterface               $logger
    ) {
    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        $token = $this->getTokenFromHeader($request, self::HEADER_PREFIX);

        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            $this->logger->warning("Bad token: [$token]");
            // throw new AuthException("Bad token: [$token]");
            exit;
        }

        if ($authToken->expiresOn() <= new DateTimeImmutable()) {
            $this->logger->warning("Token expired: [$token]");
            // throw new AuthException("Token expired: [$token]");
            exit;
        }

        $userUuid = $authToken->userUuid();
        return $this->usersRepository->getUserByUUID($userUuid);

    }

    /**
     * @throws AuthException
     */
    public function getTokenFromHeader(Request $request, $headerPrefix) : string
    {
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!str_starts_with($header, $headerPrefix)) {
            $this->logger->warning("Malformed token: [$header]");
            // throw new AuthException("Malformed token: [$header]");
            exit;
        }

        return mb_substr($header, strlen($headerPrefix));
    }
}