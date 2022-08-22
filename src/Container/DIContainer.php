<?php

namespace LksKndb\Php2\Container;
use LksKndb\Php2\Exceptions\NotFoundException;
use ReflectionClass;

class DIContainer
{
    private array $resolvers = [];

    public function bind(string $type, $resolver): void
    {
        $this->resolvers[$type] = $resolver;
    }

    /**
     * @throws NotFoundException
     */
    public function get(string $type) : object
    {
        if(array_key_exists($type, $this->resolvers)){
            $typeToCreate = $this->resolvers[$type];

            if(is_object($typeToCreate)){
                return $typeToCreate;
            }

            return $this->get($typeToCreate);
        }

        if(!class_exists($type)){
            throw new NotFoundException("Cannot resolve type: $type");
        }

        $reflectionClass = new ReflectionClass($type);
        $constructor = $reflectionClass->getConstructor();

        if($constructor === null){
            return new $type;
        }

        $parameters = [];

        foreach($constructor->getParameters() as $parameter){
            $parametersType = $parameter->getType()->getName();
            $parameters[] = $this->get($parametersType);
        }

        return new $type(...$parameters);
    }
}