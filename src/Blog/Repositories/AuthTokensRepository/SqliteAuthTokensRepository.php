<?php

namespace LksKndb\Php2\Blog\Repositories\AuthTokensRepository;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\http\Auth\AuthToken;
use PDO;
use PDOException;
use Psr\Log\LoggerInterface;

class SqliteAuthTokensRepository implements AuthTokensRepositoryInterface
{

    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ) {
    }

    public function save(AuthToken $authToken): void
    {
        // Если придет токен с таким же id, то перезапишется только expires_on
        $query = <<<'SQL'
            INSERT INTO tokens (
                token,user_uuid,expires_on
            ) VALUES (
                :token,:user_uuid,:expires_on
            )
            ON CONFLICT (token) DO UPDATE SET 
                expires_on = :expires_on
            SQL;

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute([
                ':token' => $authToken->token(),
                ':user_uuid' => (string)$authToken->userUuid(),
                ':expires_on' => $authToken->expiresOn()
                    ->format(DateTimeInterface::ATOM),
            ]);
        } catch (PDOException $e) {
            $this->logger->error($e->getMessage());
            // throw new AuthTokensRepositoryException($e->getMessage(), (int)$e->getCode(), $e);
            return;
        }
    }

    public function get(string $token): AuthToken
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM tokens WHERE token = ?'
            );
            $statement->execute([$token]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->logger->error($e->getMessage());
            // throw new AuthTokensRepositoryException($e->getMessage(), (int)$e->getCode(), $e);
            exit;
        }

        if ($result === false) {
            $this->logger->warning("Cannot find token: $token");
            // throw new AuthTokenNotFoundException("Cannot find token: $token");
            exit;
        }
        try {
        return new AuthToken(
            $result['token'],
            new UUID($result['user_uuid']),
            new DateTimeImmutable($result['expires_on'])
        );
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            // throw new AuthTokensRepositoryException($e->getMessage(), $e->getCode(), $e);
            exit;
        }
    }
}