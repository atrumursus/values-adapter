<?php

declare(strict_types=1);

namespace AtrumUrsus\ValuesAdapter\Test\UnitTest;

use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;
use AtrumUrsus\ValuesAdapter\VDateTime;
use PHPUnit\Framework\TestCase;


class VDateTimeTest extends TestCase
{
  protected $current = null;


	protected function setUp(): void
	{
		$this->current = new \DateTimeImmutable("now");
	}

	public function testSuccess()
	{

		$result = (new VDateTime())->tz("Europe/Kyiv")->convert($this->current);
		$current= $this->current->setTimezone(new \DateTimeZone("Europe/Kyiv"));
		$this->assertTrue($current == $result);

		$result = (new VDateTime())->outFormat(\DateTime::ATOM)->inFormat(\DateTime::RFC850)->convert($this->current->format(\DateTime::RFC850));
		$this->assertTrue($this->current->format(\DateTime::ATOM) === $result);
	}

}
