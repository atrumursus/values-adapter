<?php

declare(strict_types=1);

namespace AtrumUrsus\ValuesAdapter\Test\UnitTest;

use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;
use AtrumUrsus\ValuesAdapter\VInt;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AtrumUrsus\ValueAdapter\VInt
 */
class VIntTest extends TestCase
{

	/**
	 * Test VInt success.
	 *
	 * @test
	 */
	public function testSuccess() {

		$result = (new VInt())->convert(10);
		$this->assertSame(10, $result);

		$result = (new VInt())->convert('10');
		$this->assertSame(10, $result);

		$result = (new VInt())->convert(' 10 ');
		$this->assertSame(10, $result);

		$result = (new VInt())->min(1)->max(11)->convert('10');
		$this->assertSame(10, $result);

		$result = (new VInt())->min(1)->max(11)->default(21)->convert(10);
		$this->assertSame(10, $result);

		$result = (new VInt())->min(1)->max(11)->default(10)->convert(21);
		$this->assertSame(10, $result);

		$result = (new VInt())->min(1)->max(11)->default(10)->convert('noInt');
		$this->assertSame(10, $result);

		$result = (new VInt())->valid('valid')->min(1)->max(11)->default(10)->convert('valid');
		$this->assertSame('valid', $result);

		$result = (new VInt())->valid('valid_1')->valid('valid_2')->min(1)->max(11)->default(10)->convert('valid_2');
		$this->assertSame('valid_2', $result);
	}


	/**
	 * @test
	 */
	public function testExceptionValue(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('Integer value is corrupt');
		$result = (new VInt())->convert('noValid');
	}

	/**
	 * @test
	 */
	public function testExceptionValue1(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('Integer value is corrupt');
		$result = (new VInt())->convert('+12noValid45');
	}

	/**
	 * @test
	 */
	public function testExceptionMin(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('Integer value is greater than min value');
		$result = (new VInt())->min(1)->max(11)->convert('-3');
	}

	/**
	 * @test
	 */
	public function testExceptionMax(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('Integer value less than max value');
		$result = (new VInt())->min(1)->max(11)->convert(12);
	}
}
