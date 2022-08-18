<?php

use LksKndb\Php2\http\Actions\FindUserByUsername;
use LksKndb\Php2\http\ErrorResponse;
use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\SuccessfulResponse;
use LksKndb\Php2\Repositories\UsersRepositories\SqliteUsersRepository;

require_once __DIR__.'/vendor/autoload.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input')
);

//$param = $request->query('some_param');
//$header = $request->header('Some-Header');
//$path = $request->path();

//echo 'Hello from PHP!';
// Cookie: XDEBUG_SESSION=start


//$response = new SuccessfulResponse([
//    'message' => 'Hello from PHP!'
//]);
//$response = new ErrorResponse('Error!');
//$response->send();

$usersRepo = new SqliteUsersRepository(new PDO('sqlite:' . __DIR__ . '/blog.sqlite'));
$action = new FindUserByUsername($usersRepo);


