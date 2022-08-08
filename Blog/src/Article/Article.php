<?php

namespace LksKndb\Php2\Article;

class Article
{
    private int $id;
    private int $authorId;
    private string $title;
    private string $text;

    public function __construct($id, $authorId, $title, $text){
        $this->id = $id;
        $this->authorId = $authorId;
        $this->title = $title;
        $this->text = $text;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return $this->authorId;
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

    public function __toString()
    {
        return implode(PHP_EOL, [$this->getId(), $this->getAuthorId(), $this->getTitle(), $this->getText()]);
    }
}