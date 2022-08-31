<?php

use Dotenv\Dotenv;
use LksKndb\Php2\Blog\Container\DIContainer;
use LksKndb\Php2\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use LksKndb\Php2\Blog\Repositories\AuthTokensRepository\SqliteAuthTokensRepository;
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
use LksKndb\Php2\http\Auth\PasswordAuthentication;
use LksKndb\Php2\http\Auth\PasswordAuthenticationInterface;
use LksKndb\Php2\http\Auth\TokenAuthentication;
use LksKndb\Php2\http\Auth\TokenAuthenticationInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Faker\Generator;
use Faker\Provider\Lorem;
use Faker\Provider\ru_RU\Internet;
use Faker\Provider\ru_RU\Person;
use Faker\Provider\ru_RU\Text;

require_once __DIR__.'/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->safeLoad();

$container = new DIContainer();

$logger = new Logger('blog');

$faker = new Generator();
$faker->addProvider(new Person($faker));
$faker->addProvider(new Text($faker));
$faker->addProvider(new Internet($faker));
$faker->addProvider(new Lorem($faker));

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
    Generator::class,
    $faker
);

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

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    TokenAuthenticationInterface::class,
    TokenAuthentication::class
);

$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);
return $container;