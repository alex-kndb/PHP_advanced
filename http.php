<?php

use LksKndb\Php2\http\Actions\Comment\DeleteComment;
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

require_once __DIR__.'/vendor/autoload.php';

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

// Роутер
$routes = [
    'POST' => [
        '/user/save' => new SaveUser(new SqliteUsersRepository($connection)),
        '/post/save' => new SavePost(
            new SqlitePostsRepository($connection),
            new SqliteUsersRepository($connection)
        ),
        '/comment/save' => new SaveComment(
            new SqliteCommentsRepository($connection),
            new SqlitePostsRepository($connection),
            new SqliteUsersRepository($connection)
        )
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

