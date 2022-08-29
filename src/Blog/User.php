<?php

namespace LksKndb\Php2\Blog;

use DateTimeImmutable;

class User
{
    public function __construct(
        private UUID $uuid,
        private Name $name,
        private string $hashedPassword,
        private DateTimeImmutable $registeredOn
    ){}

    /**
     * @return UUID
     */
    public function getUUID(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function hashedPassword(): string
    {
        return $this->hashedPassword;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getRegisteredOn(): DateTimeImmutable
    {
        return $this->registeredOn;
    }

    public function __toString(): string
    {
        return "User id: {$this->getUUID()}".PHP_EOL.
            "Registration: ".$this->getRegisteredOn()->format('Y-m-d\ H:i:s').PHP_EOL.
            $this->getName()->__toString();
    }

    private static function hash(string $password, UUID $uuid): string
    {
        return hash('sha256', $uuid.$password);
    }

    public function checkPassword(string $password): bool
    {
        return $this->hashedPassword === self::hash($password, $this->getUUID());
    }

    public static function createFrom(Name $name, string $password): self
    {
        $uuid = UUID::createUUID();
        return new self(
            $uuid,
            $name,
            self::hash($password, $uuid),
            new DateTimeImmutable()
        );
    }
}