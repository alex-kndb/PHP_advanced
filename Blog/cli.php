<?php

require_once __DIR__.'/vendor/autoload.php';
use LksKndb\Php2\User\User;
use LksKndb\Php2\Article\Article;
use LksKndb\Php2\Comment\Comment;

$faker = Faker\Factory::create();
switch($argv[1]){
    case 'user':
        $user = new User($faker->randomDigitNotZero(), $faker->firstName(), $faker->lastName());
        echo $user;
        break;
    case 'post':
        $user = new Article($faker->randomDigitNotZero(), $faker->randomDigitNotZero(), $faker->text(20), $faker->text(200));
        echo $user;
        break;
    case 'comment':
        $user = new Comment($faker->randomDigitNotZero(), $faker->randomDigitNotZero(), 3, $faker->realTextBetween(50, 100));
        echo $user;
        break;
    default:
        echo "Wrong argument - $argv[1]!";
        break;
}