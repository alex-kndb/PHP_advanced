<?php

namespace LksKndb\Php2\Blog;

class PostLike extends Like
{
    private Post $post;

    public function __construct(UUID $uuid, User $user, Post $post)
    {
        parent::__construct($uuid,$user);
        $this->post = $post;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    public function __toString(): string
    {
        return parent::__toString() .
                "Post: ".$this->getPost()->getPost();
    }
}