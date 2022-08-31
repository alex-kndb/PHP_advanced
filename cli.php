<?php

require_once __DIR__.'/vendor/autoload.php';

use LksKndb\Php2\Blog\Commands\FakeData\PopulateDB;
use LksKndb\Php2\Blog\Commands\Posts\DeletePost;
use LksKndb\Php2\Blog\Commands\Users\CreateUser;
use LksKndb\Php2\Blog\Commands\Users\FindUser;
use LksKndb\Php2\Blog\Commands\Users\UpdateUser;
use Monolog\Logger;
use Symfony\Component\Console\Application;

$container = require __DIR__ . '/bootstrap.php';
$connection = new PDO('sqlite:'.__DIR__.'/blog.sqlite');
$logger = new Logger('CLI_Logger');
$app = new Application();

$commandsClasses = [
    CreateUser::class,
    FindUser::class,
    UpdateUser::class,
    DeletePost::class,
    PopulateDB::class
];

foreach($commandsClasses as $commandsClass) {
    $command = $container->get($commandsClass);
    $app->add($command);
}

$app->run();
