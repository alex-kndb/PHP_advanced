<?php

namespace LksKndb\Php2\Comment;

class Comment
{
    private int $id;
    private int $userId;
    private int $articleId;
    private string $text;

    public function __construct(int $id, int $userId, int $articleId, string $text){
        $this->id = $id;
        $this->userId = $userId;
        $this->articleId = $articleId;
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
    public function getUserId(): int
    {
        return $this->userId;
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
    public function getText(): string
    {
        return $this->text;
    }

    public function __toString(): string
    {
        return implode(PHP_EOL, [$this->getId(), $this->getUserId(), $this->getArticleId(), $this->getText()]);
    }
}