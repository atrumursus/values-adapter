<?php

declare(strict_types=1);

namespace AtrumUrsus\ValuesAdapter\Test\UnitTest;

use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;
use AtrumUrsus\ValuesAdapter\VDict;
use AtrumUrsus\ValuesAdapter\VExistString;
use AtrumUrsus\ValuesAdapter\VInt;
use AtrumUrsus\ValuesAdapter\VString;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AtrumUrsus\ValueAdapter\VInt
 */
class VDictTest extends TestCase
{
	protected $inputValue1;

	protected function setUp(): void
	{
		$this->inputValue1	= [
			'employee' => [
				'name' => '  JAMES',
				'lastname' => '   BOnd   ',
				'additional' => [
					'age' => '27',
					'gender' => 'man'
				]
			]
		];
	}

	public function testSuccess0(): void
	{
		//RESULT:
		$sample = [
			'NAME' => 'James',
			'LAST_NAME' => 'Bond',
			'FULL_NAME' => 'James Bond',
			'AGE' => 27
		];
		//Process:
		$adapter = (new VDict())
			->src('employee')
			->map([
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
			]);
		$result = $adapter->convert($this->inputValue1);
		$this->assertSame($sample, $result);
	}

	public function testSuccess1(): void
	{
		//RESULT:
		$sample = [
			'NAME' => 'James',
			'LAST_NAME' => 'Bond',
			'FULL_NAME' => 'James Bond',
			'AGE' => 27
		];
		//Process:
		$adapter = (new VDict())
			->preConvert(
				function ($var) {
					if (!is_array($var) || !array_key_exists('employee', $var)) {
						throw new ExceptionValue('Input Data is corrupt');
					}
					return $var['employee'];
				}
			)
			->map([
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
			]);
		$result = $adapter->convert($this->inputValue1);
		$this->assertSame($sample, $result);
	}

	public function testException(): void
	{
		$adapter = (new VDict())
			->src('employee')
			->map([
				'NAME' => [
					'src' => 'name',
					'adapter' => (new VString())->case('title')->trim(true)
				],
				'LAST_NAME' => [
					'src' => 'lastname',
					'adapter' => (new VString())->case('title')->trim(true)
				],
				'AGE' => [
					'src' => ['additional', 'age'],
					'required' => true,
					'adapter' => (new VInt())->min(30),
					'eMessage' => 'too young to be an employee'
				]
			]);

		$this->expectException(ExceptionValue::class);
		$this->expectExceptionMessage('too young to be an employee');
		$result = $adapter->convert($this->inputValue1);
	}
}
