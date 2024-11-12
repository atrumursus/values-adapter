<?php

declare(strict_types=1);

namespace AtrumUrsus\ValuesAdapter\Test\UnitTest;

use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;
use AtrumUrsus\ValuesAdapter\VEmail;
use PHPUnit\Framework\TestCase;

class VEmailTest extends TestCase
{


	public function testSuccess()
	{

		$result = (new VEmail())->convert('bond@gmail.com');
		$this->assertSame('bond@gmail.com', $result);
	}

	public function testExceptionMin(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('Email address value is corrupt');
		$result = (new VEmail())->convert('bond@g_mail.com');
	}


}
