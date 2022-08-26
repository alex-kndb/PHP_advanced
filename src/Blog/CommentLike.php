<?php

namespace LksKndb\Php2\Blog;

class CommentLike extends Like
{
    private Comment $comment;

    public function __construct(UUID $uuid, User $user, Comment $comment)
    {
        parent::__construct($uuid,$user);
        $this->comment = $comment;
    }

    /**
     * @return Comment
     */
    public function getComment(): Comment
    {
        return $this->comment;
    }

    public function __toString(): string
    {
        return parent::__toString().
                "Comment: ".$this->getComment()->getUuid();
    }
}