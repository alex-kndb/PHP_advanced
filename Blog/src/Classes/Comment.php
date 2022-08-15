<?php

namespace LksKndb\Php2\Classes;

class Comment
{
    public function __construct(
        private UUID $uuid,
        private UUID $post,
        private UUID $author,
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
     * @return UUID
     */
    public function getPost(): UUID
    {
        return $this->post;
    }

    /**
     * @return UUID
     */
    public function getAuthor(): UUID
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
            'Post UUID: '.$this->getPost().PHP_EOL.
            'Author UUID: '.$this->getAuthor().PHP_EOL.
            'Text: '.$this->getText().PHP_EOL;
    }


}