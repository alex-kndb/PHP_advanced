<?php

namespace LksKndb\Php2\Classes;

class Comment
{
    public function __construct(
        private UUID $uuid,
        private Post $post,
        private User $author,
        private string $text
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

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    public function __toString(): string
    {
        return 'Comment UUID: '.$this->getUuid().PHP_EOL.
            'Post: '.$this->getPost()->getUuid().PHP_EOL.
            'Author: '.$this->getAuthor()->getName().PHP_EOL.
            'Text: '.$this->getText().PHP_EOL;
    }


}