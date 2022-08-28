<?php

use LksKndb\Php2\Blog\Container\DIContainer;
use LksKndb\Php2\Blog\Repositories\CommentsRepositories\CommentsRepositoriesInterface;
use LksKndb\Php2\Blog\Repositories\CommentsRepositories\SqliteCommentsRepository;
use LksKndb\Php2\Blog\Repositories\LikesRepositories\CommentLikesRepositoriesInterface;
use LksKndb\Php2\Blog\Repositories\LikesRepositories\PostLikesRepositoriesInterface;
use LksKndb\Php2\Blog\Repositories\LikesRepositories\SqliteCommentLikesRepository;
use LksKndb\Php2\Blog\Repositories\LikesRepositories\SqlitePostLikesRepository;
use LksKndb\Php2\Blog\Repositories\PostsRepositories\PostsRepositoriesInterface;
use LksKndb\Php2\Blog\Repositories\PostsRepositories\SqlitePostsRepository;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\UsersRepositoriesInterface;
use LksKndb\Php2\http\Auth\IdentificationInterface;
use LksKndb\Php2\http\Auth\JsonBodyUsernameIdentification;

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
    PostLikesRepositoriesInterface::class,
SqlitePostLikesRepository::class
);

$container->bind(
    CommentLikesRepositoriesInterface::class,
    SqliteCommentLikesRepository::class
);

$container->bind(
    IdentificationInterface::class,
    JsonBodyUsernameIdentification::class
);

return $container;