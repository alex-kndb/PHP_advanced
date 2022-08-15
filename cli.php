<?php

require_once __DIR__.'/vendor/autoload.php';

use LksKndb\Php2\Classes\Comment;
use LksKndb\Php2\Classes\Post;
use LksKndb\Php2\Classes\UUID;
use LksKndb\Php2\Commands\Arguments;
use LksKndb\Php2\Commands\CreateUserCommand;
use LksKndb\Php2\Repositories\CommentsRepositories\SqliteCommentsRepository;
use LksKndb\Php2\Repositories\UsersRepositories\SqliteUsersRepository;

$faker = Faker\Factory::create('ru_RU');
$connection = new PDO('sqlite:'.__DIR__.'/db.sqlite');

$commentsRepo = new SqliteCommentsRepository($connection);
$command = new \LksKndb\Php2\Commands\CreateCommentCommand($commentsRepo);
$str = '4fdae89a-e3eb-4d8e-ad83-15771dcb73ff';
try {
//    $command->handle(Arguments::fromArgv($argv));
    echo $command->get(new UUID($str));
} catch (Exception $e) {
    echo $e->getMessage();
}


//switch($argv[1]){
//    case 'user':
//        $user = new User(
//            UUID::createUUID(),
//            new Name(
//                $faker->firstName(),
//                $faker->lastName(),
//                $faker->userName()
//            ),
//            new DateTimeImmutable()
//        );
//        echo $user;
//        break;
//    case 'post':
//        $article = new Article(
//            $faker->randomDigitNotZero(),
//            $faker->randomDigitNotZero(),
//            $faker->firstName(),
//            $faker->lastName(),
//            $faker->text(20),
//            $faker->text(190)
//        );
//        echo $article;
//        break;
//    case 'comment':
//        $comment = new Comment(
//            $faker->randomDigitNotZero(),
//            $faker->randomDigitNotZero(),
//            $faker->firstName(),
//            $faker->lastName(),
//            $faker->randomDigitNotZero(),
//            $faker->realTextBetween(50, 100)
//        );
//        echo $comment;
//        break;
//    default:
//        echo "Wrong argument - $argv[1]!";
//        break;
//}