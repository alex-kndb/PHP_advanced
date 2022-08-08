<?php

namespace LksKndb\Php2\Profiles;
use LksKndb\Php2\Common\Human;

class Admin extends Human
{
    public int $id;
    public string $access;

    public function __construct($id, $name, $age, $access)
    {
        $this->id = $id;
        $this->access = $access;
        parent::__construct($name, $age);
    }

    final function sayHello()
    {
        echo "Admin id = " . $this->id . PHP_EOL ."Access = " . $this->access . PHP_EOL . parent::sayHello();
    }
}
