<?php

use LksKndb\Php2\http\Actions\DeleteComment;
use LksKndb\Php2\http\Actions\DeletePost;
use LksKndb\Php2\http\Actions\FindCommentByUUID;
use LksKndb\Php2\http\Actions\FindPostByUUID;
use LksKndb\Php2\http\Actions\FindUserByUsername;
use LksKndb\Php2\http\Actions\SaveComment;
use LksKndb\Php2\http\Actions\SavePost;
use LksKndb\Php2\http\Actions\SaveUser;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\Repositories\CommentsRepositories\SqliteCommentsRepository;
use LksKndb\Php2\Repositories\PostsRepositories\SqlitePostsRepository;
use LksKndb\Php2\Repositories\UsersRepositories\SqliteUsersRepository;
use LksKndb\Php2\Exceptions\HttpException;

require_once __DIR__.'/vendor/autoload.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input')
);

$connection = new PDO('sqlite:'.__DIR__.'/db.sqlite');
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

// Роутер
$routes = [
    'POST' => [
        '/user/save' => new SaveUser(new SqliteUsersRepository($connection)),
        '/post/save' => new SavePost(new SqlitePostsRepository($connection)),
        '/comment/save' => new SaveComment(new SqliteCommentsRepository($connection))
    ],
    'GET' => [
        '/user/find' => new FindUserByUsername(new SqliteUsersRepository($connection)),
        '/post/find' => new FindPostByUUID(new SqlitePostsRepository($connection)),
        '/comment/find' => new FindCommentByUUID(new SqliteCommentsRepository($connection))
    ],
    'DELETE' => [
        '/post' => new DeletePost(new SqlitePostsRepository($connection)),
        '/comment' => new DeleteComment(new SqliteCommentsRepository($connection))
    ]
];

if(!array_key_exists($path, $routes[$method])){
    (new ErrorResponse("Not found"))->send();
}

$action = $routes[$method][$path];

try {
    $action->handle($request)->send();
} catch (Exception $e){
    (new ErrorResponse($e->getMessage()))->send();
}

