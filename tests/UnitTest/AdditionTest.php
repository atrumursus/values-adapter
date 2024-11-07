<?php

declare(strict_types=1);

namespace AtrumUrsus\ValuesAdapter\Test\UnitTest;

use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;
use AtrumUrsus\ValuesAdapter\VString;
use AtrumUrsus\ValuesAdapter\VId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AtrumUrsus\ValueAdapter\VString
 */
class AdditionTest extends TestCase
{

	public function testSuccess()
	{
		//Default
		$result = (new VString())->min(5)->default('default')->convert('no');
		$this->assertSame('default', $result);

		//Valid
		$result = (new VString())->min(10)->valid('valid')->valid([1,2])->convert([1, 2]);
		$this->assertSame([1, 2], $result);

		//Next
		$result =  (new VString())->withoutPrefix('user_')->next((new VId()))->convert('user_23');
		$this->assertSame(23, $result);
	}

	public function testExceptionMax(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('My Exeption message');
		$result = (new VString())->min(1)->max(3)->eMessage('My Exeption message')->convert('abcd');
	}
}
