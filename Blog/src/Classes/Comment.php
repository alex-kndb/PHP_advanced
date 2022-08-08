<?php

namespace LksKndb\Php2\Classes;
use LksKndb\Php2\Interfaces\IWriteComment;

class Comment extends User implements IWriteComment
{
    private int $commentId;
    private int $articleId;
    private string $text;

    public function __construct(int $commentId, int $userId, string $firstName, string $lastName, int $articleId, string $text){
        parent::__construct($userId, $firstName, $lastName);
        $this->commentId = $commentId;
        $this->articleId = $articleId;
        $this->text = $text;
    }

    /**
     * @return int
     */
    public function getCommentId(): int
    {
        return $this->commentId;
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
        return parent::__toString().implode(PHP_EOL, [$this->getCommentId(), $this->getArticleId(), $this->getText()]);
    }

    public function writeFeedback()
    {
        // TODO: Implement writeFeedback() method.
    }
}