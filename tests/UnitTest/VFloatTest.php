<?php

declare(strict_types=1);

namespace AtrumUrsus\ValuesAdapter\Test\UnitTest;

use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;
use AtrumUrsus\ValuesAdapter\VFloat;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AtrumUrsus\ValueAdapter\VFloat
 */
class VFloatTest extends TestCase
{

	/**
	 * Test VFloat success.
	 *
	 * @test
	 */
	public function testSuccess()
	{

		$result = (new VFloat())->convert(10);
		$this->assertSame(10.0, $result);

		$result = (new VFloat())->convert('10,2');
		$this->assertSame(10.2, $result);

		$result = (new VFloat())->convert(' 10.3 ');
		$this->assertSame(10.3, $result);

		$result = (new VFloat())->min(0.0)->max(11)->convert('10,9');
		$this->assertSame(10.9, $result);

		$result = (new VFloat())->min(1.0)->max(11)->default(21.2)->convert(10);
		$this->assertSame(10.0, $result);

		$result = (new VFloat())->min(1)->max(11.0)->default(10.0)->convert(21.45);
		$this->assertSame(10.0, $result);

		$result = (new VFloat())->min(1)->max(11)->default(10.3)->convert('noNum');
		$this->assertSame(10.3, $result);

		$result = (new VFloat())->toFixed(2)->convert(' 10.3 ');
		$this->assertSame("10.30", $result);
	}


	/**
	 * @test
	 */
	public function testExceptionValue(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('Float value is corrupt');
		$result = (new VFloat())->convert('noValid');
	}

	/**
	 * @test
	 */
	public function testExceptionValue1(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('Float value is corrupt');
		$result = (new VFloat())->convert('+12noValid45');
	}

	/**
	 * @test
	 */
	public function testExceptionMin(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('Float value is greater than min value');
		$result = (new VFloat())->min(1.0)->max(11.0)->convert('-3,14');
	}

	/**
	 * @test
	 */
	public function testExceptionMax(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('Float value less than max value');
		$result = (new VFloat())->min(1.0)->max(11.0)->convert(12.4);
	}
}
