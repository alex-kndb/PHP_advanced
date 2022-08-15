<?php

namespace LksKndb\Php2\Repositories\CommentsRepositories;

use LksKndb\Php2\Classes\Comment;
use LksKndb\Php2\Classes\UUID;

interface CommentsRepositoriesInterface
{
    public function saveComment(Comment $comment): void;
    public function getCommentByUUID(UUID $uuid): Comment;
}