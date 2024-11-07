<?php

declare(strict_types=1);

namespace AtrumUrsus\ValuesAdapter\Test\UnitTest;

use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;
use AtrumUrsus\ValuesAdapter\AdapterAbstract;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AtrumUrsus\ValueAdapter\VInt
 */
class GeneralTest extends TestCase
{

	protected $adapter = null;


	protected function setUp(): void
	{
		$this->adapter = new class() extends AdapterAbstract {
			protected function prepare(mixed $var): mixed
			{
				$invalid = 'inValid';
				if ($var === $invalid) {
					throw new ExceptionValue('just not: ' . $invalid, 1);
				}
				return $var;
			}
		};
	}

	/**
	 * @test
	 */
	public function testSuccess0(): void
	{
		$result = ($this->adapter)->convert('value');
		$this->assertSame('value', $result);
	}

	/**
	 * @test
	 */
	public function testSuccess1(): void
	{
		$result = ($this->adapter)->default('default')->convert('valid');
		$this->assertSame('valid', $result);
	}

	/**
	 * @test
	 */
	public function testSuccess2(): void
	{
		$result = ($this->adapter)->default('default')->convert('inValid');
		$this->assertSame('default', $result);
	}

	/**
	 * @test
	 */
	public function testSuccess3(): void
	{
		$result = ($this->adapter)->valid('inValid')->default('default')->convert('inValid');
		$this->assertSame('inValid', $result);
	}

	/**
	 * @test
	 */
	public function testSuccess4(): void
	{
		$result = ($this->adapter)->valid('valid_1')->valid('inValid', 'valid_2')->default('default')->convert('inValid');
		$this->assertSame('inValid', $result);
	}


	/**
	 * @test
	 */
	public function testException(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionCode(1);

		$result = ($this->adapter)->convert('inValid');
	}
}
