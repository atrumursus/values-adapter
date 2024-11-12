<?php

declare(strict_types=1);

namespace AtrumUrsus\ValuesAdapter\Test\UnitTest;

use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;
use AtrumUrsus\ValuesAdapter\VPhone;
use PHPUnit\Framework\TestCase;

class VPhoneTest extends TestCase
{


	public function testSuccess()
	{

		$result = (new VPhone())->country('UA')->convert('0441234567');
		$this->assertSame("+380 44 123 4567", $result);

		$result = (new VPhone())->country('UA')->convert('(044) 123 - 45 - 67');
		$this->assertSame("+380 44 123 4567", $result);

		$result = (new VPhone())->output(VPhone::OUTPUT_E164)->convert('+380441234567');
		$this->assertSame("+380441234567", $result);

		$result = (new VPhone())->output(VPhone::OUTPUT_NATIONAL)->convert('380441234567');
		$this->assertSame("044 123 4567", $result);

		$result = (new VPhone())->output(VPhone::OUTPUT_RFC3966)->convert('+380441234567');
		$this->assertSame("tel:+380-44-123-4567", $result);

		$result = (new VPhone())->output(VPhone::OUTPUT_COUNTRY)->convert('+380441234567');
		$this->assertSame("UA", $result);

		$result = (new VPhone())->output(VPhone::OUTPUT_DESCRIPTION)->locale('us')->convert('+380441234567');
		$this->assertSame("Kyiv city", $result);

		$result = (new VPhone())->output(VPhone::OUTPUT_TYPE)->convert('+380441234567');
		$this->assertSame("FIXED_LINE", $result);

	}

	public function testExceptionMin(): void
	{
		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('Phone value is corrupt');
		$result = (new VPhone())->convert('not valid data');
	}
}
