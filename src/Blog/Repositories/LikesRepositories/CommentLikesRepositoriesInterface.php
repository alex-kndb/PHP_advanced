<?php

namespace LksKndb\Php2\Blog\Repositories\LikesRepositories;

use LksKndb\Php2\Blog\CommentLike;
use LksKndb\Php2\Blog\UUID;

interface CommentLikesRepositoriesInterface
{
    public function save(CommentLike $like): void;
    public function getCommentLikeByUUID(UUID $uuid): CommentLike;
}