<?php

require_once __DIR__.'/vendor/autoload.php';

use LksKndb\Php2\Classes\UUID;

$faker = Faker\Factory::create();
$date = new DateTimeImmutable();

echo "first_name=".$faker->firstName()." last_name=".$faker->lastName()." username=".$faker->userName().PHP_EOL;
