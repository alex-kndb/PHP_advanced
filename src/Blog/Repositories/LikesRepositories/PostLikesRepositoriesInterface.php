<?php

namespace LksKndb\Php2\Blog\Repositories\LikesRepositories;

use LksKndb\Php2\Blog\PostLike;
use LksKndb\Php2\Blog\UUID;

interface PostLikesRepositoriesInterface
{
    public function save(PostLike $like): void;
    public function getPostLikeByUUID(UUID $uuid): PostLike;
}