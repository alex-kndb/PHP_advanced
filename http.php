<?php

use LksKndb\Php2\http\Actions\Comment\DeleteComment;
use LksKndb\Php2\http\Actions\Like\DeleteLike;
use LksKndb\Php2\http\Actions\Like\FindLikeByUUID;
use LksKndb\Php2\http\Actions\Like\SaveLike;
use LksKndb\Php2\http\Actions\Post\DeletePost;
use LksKndb\Php2\http\Actions\Comment\FindCommentByUUID;
use LksKndb\Php2\http\Actions\Post\FindPostByUUID;
use LksKndb\Php2\http\Actions\User\FindUserByUsername;
use LksKndb\Php2\http\Actions\Comment\SaveComment;
use LksKndb\Php2\http\Actions\Post\SavePost;
use LksKndb\Php2\http\Actions\User\SaveUser;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\Repositories\CommentsRepositories\SqliteCommentsRepository;
use LksKndb\Php2\Repositories\PostsRepositories\SqlitePostsRepository;
use LksKndb\Php2\Repositories\UsersRepositories\SqliteUsersRepository;
use LksKndb\Php2\Exceptions\HttpException;

$container = require_once __DIR__.'/bootstrap.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input')
);

$connection = new PDO('sqlite:'.__DIR__.'/blog.sqlite');
$commentsRepo = new SqliteCommentsRepository($connection);

// Получаем действие
try {
    $path = $request->path();
} catch (HttpException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}

// Получаем метод
try {
    $method = $request->method();
} catch (HttpException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}

$routes = [
    'POST' => [
        '/user/create' => SaveUser::class,
        '/post/create' => SavePost::class,
        '/comment/create' => SaveComment::class,
        '/like/create' => SaveLike::class
    ],
    'GET' => [
        '/user/find' => FindUserByUsername::class,
        '/post/find' => FindPostByUUID::class,
        '/comment/find' => FindCommentByUUID::class,
        '/like/find' => FindLikeByUUID::class
    ],
    'DELETE' => [
        '/post' => DeletePost::class,
        '/comment' => DeleteComment::class,
        '/like' => DeleteLike::class
    ]
];

if(!array_key_exists($path, $routes[$method])){
    (new ErrorResponse("Not found"))->send();
}

$actionClassName = $routes[$method][$path];
$action = $container->get($actionClassName);
try {
    $action->handle($request)->send();
} catch (Exception $e){
    (new ErrorResponse($e->getMessage()))->send();
}

