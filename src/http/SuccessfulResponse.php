<?php declare(strict_types=1);
// режим строгой типизации
// В этом режиме в функцию можно передавать значения только тех типов,
// которые объявлены для аргументов. В противном случае будет выбрасываться исключение TypeError.
// Есть лишь одно исключение — целое число (int) можно передать в функцию, которая ожидает значение типа float.

namespace LksKndb\Php2\http;

class SuccessfulResponse extends Response
{
    protected const SUCCESS = true;

    public function __construct(
        public array $data = []
    ) { }


    protected function payload(): array
    {
        return ['data' => $this->data];
    }
}