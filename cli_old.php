<?php

require_once __DIR__.'/vendor/autoload.php';

use LksKndb\Php2\Blog\Commands\Arguments;
use LksKndb\Php2\Blog\Commands\CreateCommentCommand;
use LksKndb\Php2\Exceptions\ArgumentNotExistException;
use LksKndb\Php2\Blog\Repositories\CommentsRepositories\SqliteCommentsRepository;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use Monolog\Logger;

$connection = new PDO('sqlite:'.__DIR__.'/blog.sqlite');
$logger = new Logger('CLI_Logger');

//$faker = Faker\Factory::create('ru_RU');
$commentsRepo = new SqliteCommentsRepository($connection, $logger);
$command = new CreateCommentCommand($commentsRepo);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (ArgumentNotExistException | InvalidUuidException $e) {
}

//switch($argv[1]){
//    case 'user':
//        break;
//    case 'post':
//        break;
//    case 'comment':
//        break;
//    default:
//        echo "Wrong argument - $argv[1]!";
//        break;
//}