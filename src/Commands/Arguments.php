<?php

namespace LksKndb\Php2\Commands;

use LksKndb\Php2\Exceptions\ArgumentNotExistException;

class Arguments
{
    private array $args = [];

    public function __construct(iterable $args)
    {
        foreach ($args as $key => $value){
            $argValue = trim((string)$value);
            if(!$argValue){
                continue;
            }
            $this->args[(string)$key] = $argValue;
        }
    }

    public static function fromArgv(array $argv): self
    {
        $args = [];
        foreach ($argv as $arg){
            $parts = explode('=', $arg);
            if(count($parts) !== 2){
                continue;
            }
            $args[$parts[0]] = $parts[1];
        }
        return new self($args);
    }

    /**
     * @throws ArgumentNotExistException
     */
    public function get(string $arg): string
    {
        if(!array_key_exists($arg, $this->getArgs())){
            throw new ArgumentNotExistException(
                "No such argument: $arg"
            );
        }
        return $this->getArgs()[$arg];
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }
}