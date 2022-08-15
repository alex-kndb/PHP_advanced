<?php

namespace LksKndb\Php2\Classes;

use LksKndb\Php2\Exceptions\User\InvalidUuidException;

class UUID
{
    public function __construct(
        private string $uuid
    ){
        /**
         * @throws InvalidUuidException
         */
        if(!uuid_is_valid($uuid)){
            throw new InvalidUuidException("Wrong UUID: ".$this->getUuid());
        }
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    public static function createUUID(): self
    {
        return new self(uuid_create(UUID_TYPE_RANDOM));
    }

    public function __toString(): string
    {
        return $this->getUuid();
    }
}