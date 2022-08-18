<?php

require_once __DIR__.'/vendor/autoload.php';

use LksKndb\Php2\Classes\UUID;

$faker = Faker\Factory::create('ru_RU');
//$date = new DateTimeImmutable();

//echo "first_name=".$faker->firstName()." last_name=".$faker->lastName()." username=".$faker->userName().PHP_EOL;
//echo "author=".$faker->uuid()." title=".str_replace(" ", '_', $faker->realText(20))."v text=".str_replace(" ", '_', $faker->realText(190));
//echo "post=".$faker->uuid()." author=".$faker->uuid()." text=".str_replace(" ", '_', $faker->realText(150));

//$date = '2022-08-13 17:21:36';
//print_r(DateTimeImmutable::createFromFormat('Y-m-d\ H:i:s', $date));

$d_1 = new DateTimeImmutable();
$str_1 = $d_1->format('Y-m-d\ H:i:s');
$d_2 = DateTimeImmutable::createFromFormat('Y-m-d\ H:i:s', $str_1);
$str_2 = $d_2->format('Y-m-d\ H:i:s');
echo $str_1 == $str_2;

