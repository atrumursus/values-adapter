<?php

declare(strict_types=1);

namespace AtrumUrsus\ValuesAdapter\Test\UnitTest;

use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;
use AtrumUrsus\ValuesAdapter\VConst;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AtrumUrsus\ValueAdapter\VInt
 */
class VConstTest extends TestCase
{

	/**
	 * Test VInt success.
	 *
	 * @test
	 */
	public function testSuccess()
	{

		$result = (new VConst())->default('const')->convert(20);
		$this->assertSame('const', $result);

		$result = (new VConst())->value('value')->convert(null);
		$this->assertSame('value', $result);

		$result = (new VConst())->value('value')->default('const')->convert(null);
		$this->assertSame('value', $result);

	}


	/**
	 * @test
	 */
	public function testExceptionValue(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('Param "value" or "default" not set');
		$result = (new VConst())->convert('data');
	}

}
