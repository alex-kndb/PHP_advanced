<?php

namespace LksKndb\Php2\Classes;

class Name
{
    public function __construct(
        private readonly string $firstName,
        private readonly string $lastName,
        private readonly string $username
    ){}

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

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function __toString(): string
    {
        return "First name: ".$this->getFirstName().PHP_EOL.
                "Last name: ".$this->getLastName().PHP_EOL.
                "Username: ".$this->getUsername().PHP_EOL;
    }
}