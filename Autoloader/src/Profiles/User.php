<?php

namespace LksKndb\Php2\Profiles;
use LksKndb\Php2\Common\Human;

class User extends Human
{
    public int $id;

    public function __construct($id, $name, $age)
    {
        $this->id = $id;
        parent::__construct($name, $age);
    }

    final function sayHello()
    {
        echo "User id = " . $this->id . PHP_EOL . parent::sayHello();
    }
}