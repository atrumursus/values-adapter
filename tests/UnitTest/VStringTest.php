<?php

declare(strict_types=1);

namespace AtrumUrsus\ValuesAdapter\Test\UnitTest;

use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;
use AtrumUrsus\ValuesAdapter\VString;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AtrumUrsus\ValueAdapter\VString
 */
class VStringTest extends TestCase
{

	public function testSuccess()
	{
		$result = (new VString())->convert('value');
		$this->assertSame('value', $result);

		$result = (new VString())->convert(10);
		$this->assertSame('10', $result);

		$result = (new VString())->convert(null);
		$this->assertSame('', $result);
	}

	public function testSuccessTrim()
	{
		$result = (new VString())->trim(false)->convert("   value \t\n    ");
		$this->assertSame("   value \t\n    ", $result);

		$result = (new VString())->trim(true)->convert("   value \t\n    ");
		$this->assertSame('value', $result);
	}

	public function testSuccessCase()
	{
		$result = (new VString())->case(null)->convert('VaLuE');
		$this->assertSame('VaLuE', $result);

		$result = (new VString())->case('upper')->convert('VaLuE');
		$this->assertSame('VALUE', $result);

		$result = (new VString())->case('lower')->convert('VaLuE');
		$this->assertSame('value', $result);

		$result = (new VString())->case('title')->convert('VaLuE');
		$this->assertSame('Value', $result);

	}

	public function testSuccessWithPrefix()
	{
		$result = (new VString())->withPrefix('')->convert('value');
		$this->assertSame('value', $result);

		$result = (new VString())->withPrefix('PREFIX_')->convert('value');
		$this->assertSame('PREFIX_value', $result);

		$result = (new VString())->withPrefix('PREFIX_')->convert('PREFIX_value');
		$this->assertSame('PREFIX_value', $result);
	}

	public function testSuccessWithoutPrefix()
	{
		$result = (new VString())->withoutPrefix('')->convert('value');
		$this->assertSame('value', $result);

		$result = (new VString())->withoutPrefix('PREFIX_')->convert('value');
		$this->assertSame('value', $result);

		$result = (new VString())->withoutPrefix('PREFIX_', 'PREFIX2_')->convert('PREFIX_value');
		$this->assertSame('value', $result);
	}

	public function testSuccessWithSuffix()
	{
		$result = (new VString())->withSuffix('')->convert('value');
		$this->assertSame('value', $result);

		$result = (new VString())->withSuffix('_SUFFIX')->convert('value');
		$this->assertSame('value_SUFFIX', $result);

		$result = (new VString())->withSuffix('_SUFFIX')->convert('value_SUFFIX');
		$this->assertSame('value_SUFFIX', $result);
	}

	public function testSuccessWithoutSuffix()
	{
		$result = (new VString())->withoutSuffix('')->convert('value');
		$this->assertSame('value', $result);

		$result = (new VString())->withoutSuffix('_SUFFIX')->convert('value');
		$this->assertSame('value', $result);

		$result = (new VString())->withoutSuffix('_SUFFIX', '_SUFFIX2')->convert('value_SUFFIX');
		$this->assertSame('value', $result);
	}

	public function testSuccessMinMax()
	{
		$result = (new VString())->min(1)->max(1)->convert('v');
		$this->assertSame('v', $result);

		$result = (new VString())->min(1)->max(10)->convert('value');
		$this->assertSame('value', $result);
	}

	public function testExceptionMin(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('String length is greater than min value');
		$result = (new VString())->min(3)->max(11)->convert('ab');
	}

	public function testExceptionMax(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('String length less than max value');
		$result = (new VString())->min(1)->max(3)->convert('abcd');
	}

}
