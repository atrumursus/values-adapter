<?php

declare(strict_types=1);

namespace AtrumUrsus\ValuesAdapter\Test\UnitTest;

use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;
use AtrumUrsus\ValuesAdapter\VExistString;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AtrumUrsus\ValueAdapter\VInt
 */
class VExistStringTest extends TestCase
{

	/**
	 * Test VInt success.
	 *
	 * @test
	 */
	public function testSuccess()
	{

		$result = (new VExistString())->convert(10);
		$this->assertSame('10', $result);

		$result = (new VExistString())->convert('value');
		$this->assertSame('value', $result);

		$result = (new VExistString())->convert('v');
		$this->assertSame('v', $result);

		$result = (new VExistString())->convert(' v ');
		$this->assertSame('v', $result);
	}

	/**
	 * @test
	 */
	public function testExceptionMin(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('String length is greater than min value');
		$result = (new VExistString())->convert('');
	}

	/**
	 * @test
	 */
	public function testExceptionMin1(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('String length is greater than min value');
		$result = (new VExistString())->convert("  \n\t  ");
	}
}
