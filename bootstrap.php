<?php

use LksKndb\Php2\Blog\Container\DIContainer;
use LksKndb\Php2\Blog\http\Auth\IdentificationInterface;
use LksKndb\Php2\Blog\http\Auth\JsonBodyUsernameAuthentification;
use LksKndb\Php2\Blog\Repositories\CommentsRepositories\CommentsRepositoriesInterface;
use LksKndb\Php2\Blog\Repositories\CommentsRepositories\SqliteCommentsRepository;
use LksKndb\Php2\Blog\Repositories\LikesRepositories\PostLikesRepositoriesInterface;
use LksKndb\Php2\Blog\Repositories\LikesRepositories\SqlitePostLikesRepository;
use LksKndb\Php2\Blog\Repositories\PostsRepositories\PostsRepositoriesInterface;
use LksKndb\Php2\Blog\Repositories\PostsRepositories\SqlitePostsRepository;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\UsersRepositoriesInterface;

require_once __DIR__.'/vendor/autoload.php';

$container = new DIContainer();

$container->bind(
    PDO::class,
    new PDO('sqlite:'.__DIR__.'/blog.sqlite'));

$container->bind(
    IdentificationInterface::class,
    JsonBodyUsernameAuthentification::class);

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
    PostLikesRepositoriesInterface::class,
SqlitePostLikesRepository::class);

return $container;