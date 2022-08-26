<?php

namespace LksKndb\Php2\Blog\Repositories\CommentsRepositories;

use LksKndb\Php2\Blog\Comment;
use LksKndb\Php2\Blog\UUID;

interface CommentsRepositoriesInterface
{
    public function saveComment(Comment $comment): void;
    public function getCommentByUUID(UUID $uuid): Comment;
}