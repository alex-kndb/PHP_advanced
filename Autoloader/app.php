<?php

require 'loader.php';
use LksKndb\Php2\Profiles\{User, Admin};

$user = new User(1, 'Alex', 30);
$admin = new Admin(2, 'John', 35, 'full');

$user->sayHello();
$admin->sayHello();