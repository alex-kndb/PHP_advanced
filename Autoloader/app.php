<?php

require 'loader.php';

use src\Profiles\{User, Admin};

$user = new User(1, 'Alex', 30);
$admin = new Admin(2, 'John', 35, full);
//print_r($user);

//$user->sayHello();
//$admin->sayHello();