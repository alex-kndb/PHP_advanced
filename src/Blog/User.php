<?php

namespace LksKndb\Php2\Blog;

use DateTimeImmutable;

class User
{
    public function __construct(
        private UUID $uuid,
        private Name $name,
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
}