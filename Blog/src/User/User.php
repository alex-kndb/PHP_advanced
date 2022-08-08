<?php

namespace LksKndb\Php2\User;

use LksKndb\Php2\User\Interfaces\{IWriteArticle, IWriteComment};

class User implements IWriteComment, IWriteArticle
{
    private int $id;
    private string $firstName;
    private string $lastName;

    public function __construct(int $id, string $firstName, string $lastName)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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

    public function writeArticle()
    {
        // TODO: Implement writeArticle() method.
    }

    public function writeFeedback()
    {
        // TODO: Implement writeFeedback() method.
    }

    public function __toString(): string
    {
        return implode(PHP_EOL, [$this->getId(), $this->getFirstName(), $this->getLastName()]);
    }
}