<?php

namespace LksKndb\Php2\Classes;

class Post
{
    public function __construct(
        private readonly UUID   $post,
        private readonly UUID   $author,
        private readonly string $title,
        private readonly string $text
    ){}

    /**
     * @return string
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
    public function getTitle(): string
    {
        return $this->title;
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
        return 'Post UUID: '.$this->getPost().PHP_EOL.
            'Author UUID: '.$this->getAuthor().PHP_EOL.
            'Title: '.$this->getTitle().PHP_EOL.
            'Text: '.$this->getText().PHP_EOL;
    }
}