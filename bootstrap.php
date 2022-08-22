<?php

use LksKndb\Php2\Container\DIContainer;
use LksKndb\Php2\Repositories\CommentsRepositories\CommentsRepositoriesInterface;
use LksKndb\Php2\Repositories\CommentsRepositories\SqliteCommentsRepository;
use LksKndb\Php2\Repositories\LikesRepositories\LikesRepositoriesInterface;
use LksKndb\Php2\Repositories\LikesRepositories\SqliteLikesRepository;
use LksKndb\Php2\Repositories\PostsRepositories\PostsRepositoriesInterface;
use LksKndb\Php2\Repositories\PostsRepositories\SqlitePostsRepository;
use LksKndb\Php2\Repositories\UsersRepositories\SqliteUsersRepository;
use LksKndb\Php2\Repositories\UsersRepositories\UsersRepositoriesInterface;

require_once __DIR__.'/vendor/autoload.php';

$container = new DIContainer();

$container->bind(
    PDO::class,
    new PDO('sqlite:'.__DIR__.'/blog.sqlite'));

$container->bind(
    UsersRepositoriesInterface::class,
    SqliteUsersRepository::class
);

$container->bind(
    PostsRepositoriesInterface::class,
    SqlitePostsRepository::class
);

$container->bind(
    CommentsRepositoriesInterface::class,
    SqliteCommentsRepository::class
);

$container->bind(
    LikesRepositoriesInterface::class,
SqliteLikesRepository::class);

return $container;