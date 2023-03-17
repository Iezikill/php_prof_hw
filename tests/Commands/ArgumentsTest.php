<?php

namespace Viktoriya\PHP2\Blog\UnitTests\Commands;

use Viktoriya\PHP2\Blog\Commands\Arguments;
use Viktoriya\PHP2\Blog\Exceptions\ArgumentsException;
use PHPUnit\Framework\TestCase;

class ArgumentsTest extends TestCase
{
  public function testItReturnsArgumentsValueByName(): void
  {
    $arguments = new Arguments(['some_key' => 123]);

    $value = $arguments->get('some_key');

    $this->assertSame('123', $value);
    $this->assertIsString($value);
  }

  public function testItThrowsAnExceptionWhenArgumentIsAbsent(): void
  {
    $arguments = new Arguments([]);
    $this->expectException(ArgumentsException::class);

    $this->expectExceptionMessage("No such argument: some_key");

    $arguments->get('some_key');
  }

  /**
   * @dataProvider argumentsProvider
   * @throws ArgumentsException
   */
  public function testItConvertsArgumentsToStrings($inputValue, $expectedValue): void
  {
    $arguments = new Arguments(['some_key' => $inputValue]);
    $value = $arguments->get('some_key');
    $this->assertEquals($expectedValue, $value);
  }

  public function argumentsProvider(): iterable
  {
    return [
      ['some_string', 'some_string'],
      [' some_string', 'some_string'],
      [' some_string ', 'some_string'],
      [123, '123'],
      [12.3, '12.3'],
    ];
  }
}
