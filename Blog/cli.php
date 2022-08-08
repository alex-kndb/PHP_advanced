<?php

require_once __DIR__.'/vendor/autoload.php';

use LksKndb\Php2\Classes\Article;
use LksKndb\Php2\Classes\Comment;
use LksKndb\Php2\Classes\User;

$faker = Faker\Factory::create();
switch($argv[1]){
    case 'user':
        $user = new User(
            $faker->randomDigitNotZero(),
            $faker->firstName(),
            $faker->lastName()
        );
        echo $user;
        break;
    case 'post':
        $user = new Article(
            $faker->randomDigitNotZero(),
            $faker->randomDigitNotZero(),
            $faker->firstName(),
            $faker->lastName(),
            $faker->text(20),
            $faker->text(200)
        );
        echo $user;
        break;
    case 'comment':
        $user = new Comment(
            $faker->randomDigitNotZero(),
            $faker->randomDigitNotZero(),
            $faker->firstName(),
            $faker->lastName(),
            $faker->randomDigitNotZero(),
            $faker->realTextBetween(50, 100)
        );
        echo $user;
        break;
    default:
        echo "Wrong argument - $argv[1]!";
        break;
}