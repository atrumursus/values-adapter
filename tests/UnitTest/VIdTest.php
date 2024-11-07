<?php

declare(strict_types=1);

namespace AtrumUrsus\ValuesAdapter\Test\UnitTest;

use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;
use AtrumUrsus\ValuesAdapter\VId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AtrumUrsus\ValueAdapter\VInt
 */
class VIdTest extends TestCase
{

	/**
	 * Test VInt success.
	 *
	 * @test
	 */
	public function testSuccess()
	{

		$result = (new VId())->convert(10);
		$this->assertSame(10, $result);

		$result = (new VId())->convert('1');
		$this->assertSame(1, $result);

		$result = (new VId())->convert(' 200 ');
		$this->assertSame(200, $result);
	}

	/**
	 * @test
	 */
	public function testExceptionMin(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('Integer value is greater than min value');
		$result = (new VId())->convert(-1);
	}

	/**
	 * @test
	 */
	public function testExceptionMin1(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('Integer value is greater than min value');
		$result = (new VId())->convert('0');
	}

}
