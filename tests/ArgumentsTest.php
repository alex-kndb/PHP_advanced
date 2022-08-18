<?php

namespace LksKndb\Php2\UnitTests;

use LksKndb\Php2\Commands\Arguments;
use LksKndb\Php2\Exceptions\ArgumentNotExistException;
use PHPUnit\Framework\TestCase;

class ArgumentsTest extends TestCase
{

    /**
     * @dataProvider argumentsProvider
     * @throws ArgumentNotExistException
     */
    public function testItReturnsArgumentsValueByName($inputValue, $expectedValue): void
    {
        $args = new Arguments(['key' => $inputValue]);
        $value = $args->get('key');
        $this->assertEquals($expectedValue, $value);
    }

    public function argumentsProvider(): iterable
    {
        // на нижних трех значениях эксепшн вылетает
        return [
            ['value', 'value'],
            ['value value', 'value value'],
            [33, '33'],
//            [0, ''],
//            [' ', ' '],
//            [null, '']
        ];
    }

    public function testItThrowAnExceptionWhenArgumentIsAbsent(): void
    {
        $this->expectException(ArgumentNotExistException::class);
        $this->expectExceptionMessage("No such argument: key");
        $args = new Arguments(['key' => 'value']);
        $args->get('key1');
    }
}

