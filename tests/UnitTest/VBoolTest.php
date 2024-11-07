<?php

declare(strict_types=1);

namespace AtrumUrsus\ValuesAdapter\Test\UnitTest;

use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;
use AtrumUrsus\ValuesAdapter\VBool;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \AtrumUrsus\ValueAdapter\VBool
 */
class VBoolTest extends TestCase
{

	/**
	 * Test VInt success.
	 *
	 * @test
	 */
	public function testSuccess()
	{

		$result = (new VBool())->convert(true);
		$this->assertSame(true, $result);

		$result = (new VBool())->convert(false);
		$this->assertSame(false, $result);

		$result = (new VBool())->convert(0);
		$this->assertSame(false, $result);

		$result = (new VBool())->convert(1);
		$this->assertSame(true, $result);

		$result = (new VBool())->convert(5);
		$this->assertSame(true, $result);

		$result = (new VBool())->convert('true');
		$this->assertSame(true, $result);

		$result = (new VBool())->convert('Yes');
		$this->assertSame(true, $result);

		$result = (new VBool())->convert('ON');
		$this->assertSame(true, $result);

		$result = (new VBool())->convert('NO');
		$this->assertSame(false, $result);

		$result = (new VBool())->convert('False');
		$this->assertSame(false, $result);

		$result = (new VBool())->convert('off');
		$this->assertSame(false, $result);

		$result = (new VBool())->true('True')->false('False')->convert(true);
		$this->assertSame('True', $result);

		$result = (new VBool())->true('True')->false('False')->convert(false);
		$this->assertSame('False', $result);

		$result = (new VBool())->boolval(true)->convert(new stdClass());
		$this->assertSame(true, $result);
	}

	public function testExceptionMax(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('Bool value is corrupt');
		$result = (new VBool())->convert(new stdClass());
	}
}
