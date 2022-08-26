<?php

namespace LksKndb\Php2\Blog;

class Like
{
    public function __construct(
        private UUID $uuid,
        private User $user,
    ){}

    /**
     * @return UUID
     */
    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function __toString(): string
    {
        return 'Like UUID: '.$this->getUuid().PHP_EOL.
                'Author: '.$this->getUser()->getName()->getUsername().PHP_EOL;
    }


}