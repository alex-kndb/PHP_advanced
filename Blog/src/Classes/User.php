<?php

namespace LksKndb\Php2\Classes;

class User
{
    private int $userId;
    private string $firstName;
    private string $lastName;

    public function __construct(int $userId, string $firstName, string $lastName)
    {
        $this->userId  = $userId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function __toString(): string
    {
        return implode('|', [$this->getUserId(), $this->getFirstName(), $this->getLastName()]).PHP_EOL;
    }
}