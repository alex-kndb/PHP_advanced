<?php

require_once __DIR__.'/vendor/autoload.php';

use LksKndb\Php2\Commands\Arguments;
use LksKndb\Php2\Commands\CreateUserCommand;
use LksKndb\Php2\Repositories\UsersRepositories\SqliteUsersRepository;

//$faker = Faker\Factory::create();
$connection = new PDO('sqlite:'.__DIR__.'/db.sqlite');

//$userRepo = new InMemoryUsersRepository();
//$userRepo->saveUser($user);
//print_r($userRepo->getUsers());

$userRepo = new SqliteUsersRepository($connection);

$command = new CreateUserCommand($userRepo);

try{
    $command->handle(Arguments::fromArgv($argv));
}catch(Exception $e){
    echo $e->getMessage();
}

/*
$user = new User(
    UUID::createUUID(),
    new Name(
        $faker->firstName(),
        $faker->lastName(),
        $faker->userName()
    ),
    new DateTimeImmutable()
);
*/
//$userRepo->saveUser($user);

//$user = $userRepo->getUserByUUID(new UUID('c968d9da-970e-4103-83fd-a63e8643bfe3'));
//$user = $userRepo->getUserByUsername('gaetano09');
//print_r($user);

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