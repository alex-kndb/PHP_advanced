<?php

namespace LksKndb\Php2\Classes;
use LksKndb\Php2\Interfaces\IWriteArticle;

class Article extends User implements IWriteArticle
{
    private int $articleId;
    private string $title;
    private string $text;

    public function __construct(int $articleId, int $userId, string $firstName, string $lastName, string $title, string $text){
        parent::__construct($userId, $firstName, $lastName);
        $this->articleId = $articleId;
        $this->title = $title;
        $this->text = $text;
    }

    /**
     * @return int
     */
    public function getArticleId(): int
    {
        return $this->articleId;
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
        return parent::__toString().implode(PHP_EOL, [$this->getArticleId(), $this->getTitle(), $this->getText()]);
    }

    public function writeArticle()
    {
        // TODO: Implement writeArticle() method.
    }
}