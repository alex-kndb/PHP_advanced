<?php

namespace LksKndb\Php2\Classes;

class Like
{
    public function __construct(
        private UUID $uuid,
        private Post $post,
        private User $author,
    ){}

    /**
     * @return UUID
     */
    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    public function __toString(): string
    {
        return 'Like UUID: '.$this->getUuid().PHP_EOL.
            'Post: '.$this->getPost()->getPost().PHP_EOL.
            'Author: '.$this->getAuthor()->getName().PHP_EOL;
    }


}