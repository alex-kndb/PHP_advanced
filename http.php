<?php

use LksKndb\Php2\http\Actions\Comment\DeleteComment;
use LksKndb\Php2\http\Actions\Like\CreateCommentLike;
use LksKndb\Php2\http\Actions\Like\CreatePostLike;
use LksKndb\Php2\http\Actions\Like\DeleteCommentLike;
use LksKndb\Php2\http\Actions\Like\DeletePostLike;
use LksKndb\Php2\http\Actions\Like\FindCommentLikeByUUID;
use LksKndb\Php2\http\Actions\Like\FindPostLikeByUUID;
use LksKndb\Php2\http\Actions\Like\GetLikesByCommentUUID;
use LksKndb\Php2\http\Actions\Like\GetLikesByPostUUID;
use LksKndb\Php2\http\Actions\Post\DeletePost;
use LksKndb\Php2\http\Actions\Comment\FindCommentByUUID;
use LksKndb\Php2\http\Actions\Post\FindPostByUUID;
use LksKndb\Php2\http\Actions\User\FindUserByUsername;
use LksKndb\Php2\http\Actions\Comment\CreateComment;
use LksKndb\Php2\http\Actions\Post\CreatePost;
use LksKndb\Php2\http\Actions\User\CreateUser;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\Blog\Repositories\CommentsRepositories\SqliteCommentsRepository;
use LksKndb\Php2\Exceptions\HttpException;
use Psr\Log\LoggerInterface;

$container = require_once __DIR__.'/bootstrap.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input')
);

$connection = new PDO('sqlite:'.__DIR__.'/blog.sqlite');
//$commentsRepo = new SqliteCommentsRepository($connection);

$logger = $container->get(LoggerInterface::class);

try {
    $path = $request->path();
    $method = $request->method();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse($e->getMessage()))->send();
    return;
}

$routes = [
    'POST' => [
        '/user/create' => CreateUser::class,
        '/post/create' => CreatePost::class,
        '/comment/create' => CreateComment::class,
        '/post/like/create' => CreatePostLike::class,
        '/comment/like/create' => CreateCommentLike::class,
    ],
    'GET' => [
        '/user/find' => FindUserByUsername::class,
        '/post/find' => FindPostByUUID::class,
        '/comment/find' => FindCommentByUUID::class,
        '/post/like/find' => FindPostLikeByUUID::class,
        '/comment/like/find' => FindCommentLikeByUUID::class,
        '/post/likes' => GetLikesByPostUUID::class,
        '/comment/likes' => GetLikesByCommentUUID::class
    ],
    'DELETE' => [
        '/post' => DeletePost::class,
        '/comment' => DeleteComment::class,
        '/post/like' => DeletePostLike::class,
        '/comment/like' => DeleteCommentLike::class
    ]
];

if (!array_key_exists($method, $routes) || !array_key_exists($path, $routes[$method])){
    $mess = "Route not found: $method $path";
    $logger->notice($mess);
    (new ErrorResponse($mess))->send();
    return;
}

$actionClassName = $routes[$method][$path];
$action = $container->get($actionClassName);

try {
    $action->handle($request)->send();
} catch (Exception $e){
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse($e->getMessage()))->send();
    return;
}

