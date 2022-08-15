<?php

namespace LksKndb\Php2\Repositories\PostsRepositories;

use LksKndb\Php2\Classes\Post;
use LksKndb\Php2\Classes\User;
use LksKndb\Php2\Classes\UUID;

interface PostsRepositoriesInterface
{
    public function savePost(Post $post): void;
    public function getPostByUUID(UUID $uuid): Post;
}