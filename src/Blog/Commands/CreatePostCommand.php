<?php

namespace LksKndb\Php2\Blog\Commands;

use LksKndb\Php2\Blog\Post;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\ArgumentNotExistException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use LksKndb\Php2\Blog\Repositories\PostsRepositories\PostsRepositoriesInterface;

class CreatePostCommand
{
    public function __construct(
        private PostsRepositoriesInterface $postsRepository
    )
    {
    }

    /**
     * @throws ArgumentNotExistException
     * @throws InvalidUuidException
     */
    public function handle(Arguments $args): void
    {
        $this->postsRepository->savePost(
            new Post(
                UUID::createUUID(),
                new UUID($args->get('author')),
                $args->get('title'),
                $args->get('text')
            )
        );
    }

    public function get(string $uuid): Post
    {
        return $this->postsRepository->getPostByUUID(new UUID($uuid));
    }

}
//    private function isPostExist(UUID $uuid): bool
//    {
//        try{
//            $this->postsRepository->getPostByUUID($uuid);
//        } catch (PostNotFoundException $e){
//            echo $e->getMessage();
//            return false;
//        }
//        return true;
//    }