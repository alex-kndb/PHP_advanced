<?php

namespace LksKndb\Php2\Blog\Repositories\PostsRepositories;

use LksKndb\Php2\Blog\Post;
use LksKndb\Php2\Blog\UUID;

interface PostsRepositoriesInterface
{
    public function savePost(Post $post): void;
    public function getPostByUUID(UUID $uuid): Post;
    public function deletePost(UUID $uuid) : void;
}