<?php

use Dotenv\Dotenv;
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
use LksKndb\Php2\http\Auth\AuthenticationInterface;
use LksKndb\Php2\http\Auth\IdentificationInterface;
use LksKndb\Php2\http\Auth\JsonBodyUsernameIdentification;
use LksKndb\Php2\http\Auth\PasswordAuthentication;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

require_once __DIR__.'/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->safeLoad();

$container = new DIContainer();

$logger = new Logger('blog');

if ($_SERVER['LOG_TO_FILES'] === 'yes') {
    $logger
        ->pushHandler(new StreamHandler(
        __DIR__ . '/logs/blog.log'
    ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,
            bubble: false,
        ));
}

if ($_SERVER['LOG_TO_CONSOLE'] === 'yes') {
    $logger
        ->pushHandler(
            new StreamHandler("php://stdout")
        );
}

$container->bind(
    LoggerInterface::class,
    $logger
);

$container->bind(
    PDO::class,
//    new PDO('sqlite:'.__DIR__.'/blog.sqlite')
    new PDO('sqlite:' . __DIR__ . '/' . $_SERVER['SQLITE_DB_PATH'])
);


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

//$container->bind(
//    IdentificationInterface::class,
//    JsonBodyUsernameIdentification::class
//);

$container->bind(
    AuthenticationInterface::class,
    PasswordAuthentication::class
);

return $container;