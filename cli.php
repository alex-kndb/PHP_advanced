<?php

require_once __DIR__.'/vendor/autoload.php';

use LksKndb\Php2\Blog\Comment;
use LksKndb\Php2\Blog\Post;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Blog\Commands\Arguments;
use LksKndb\Php2\Blog\Commands\CreateCommentCommand;
use LksKndb\Php2\Blog\Commands\CreateUserCommand;
use LksKndb\Php2\Exceptions\ArgumentNotExistException;
use LksKndb\Php2\Blog\Repositories\CommentsRepositories\SqliteCommentsRepository;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\SqliteUsersRepository;

$faker = Faker\Factory::create('ru_RU');
$connection = new PDO('sqlite:'.__DIR__.'/blog.sqlite');

$commentsRepo = new SqliteCommentsRepository($connection);
$command = new CreateCommentCommand($commentsRepo);
$str = '4fdae89a-e3eb-4d8e-ad83-15771dcb73ff';

$command->handle(Arguments::fromArgv($argv));


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