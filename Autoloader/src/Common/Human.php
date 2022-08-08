<?php

namespace Common;

class Human
{
    protected string $name;
    protected int $age;

    public function __construct($name, $age)
    {
        $this->name = $name;
        $this->age = $age;
    }

    public function getName(): string
    {
        return $this->name;
    }

    protected function sayHello()
    {
        return "Hello from " . $this->getName() . "!" . PHP_EOL;
    }
}