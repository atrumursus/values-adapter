<?php

declare(strict_types=1);

namespace AtrumUrsus\ValuesAdapter\Test\UnitTest;

use AtrumUrsus\ValuesAdapter\VDictAbstract;
use AtrumUrsus\ValuesAdapter\VExistString;
use AtrumUrsus\ValuesAdapter\VInt;
use AtrumUrsus\ValuesAdapter\VString;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AtrumUrsus\ValueAdapter\VInt
 */
class VDictClassTest extends TestCase
{
	protected $adapter;
	protected $inputValue1;


	protected function setUp(): void
	{
		$this->inputValue1	= [
			'data' => [
				'employee' => [
					'name' => '  JAMES',
					'lastname' => '   BOnd   ',
					'additional' => [
						'age' => '27',
						'gender' => 'man'
					]
				]
			]
		];

		$this->adapter = new class() extends VDictAbstract {
			protected function runPreConvert(mixed $var): mixed
			{
				return $var;
			}

			protected function runPostConvert(mixed $var): mixed
			{
				return $var;
			}

			protected function getMap(): array
			{
				return [
					'NAME' => [
						'src' => 'name',
						'adapter' => (new VString())->case('title')->trim(true)
					],
					'LAST_NAME' => [
						'src' => 'lastname',
						'adapter' => (new VString())->case('title')->trim(true)
					],
					'FULL_NAME' => [
						'extractor' => function ($data, $options) {
							$adapter = (new VExistString)->default('')->trim(true);
							return $adapter->convert($data['name']) . ' ' . $adapter->default('Anonymous')->convert($data['lastname']);
						},
						'adapter' => (new VString())->case('title')->trim(true)
					],
					'AGE' => [
						'src' => ['additional', 'age'],
						'adapter' => (new VInt())->min(16)
					]
				];
			}
		};
	}

	public function testSuccess(): void
	{
		//RESULT:
		$sample = [
			'NAME' => 'James',
			'LAST_NAME' => 'Bond',
			'FULL_NAME' => 'James Bond',
			'AGE' => 27
		];
		//Process:
		$result = $this->adapter->src('data', 'employee')->convert($this->inputValue1);
		$this->assertSame($sample, $result);
	}
}
