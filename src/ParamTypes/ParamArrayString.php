<?php
/*
 * This file is part of AtrumUrsus\ValuesAdapter.
 * https://github.com/atrumursus/values-adapter
 *
 * (c) Vasyl Tochylin <tochylin.vasyl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AtrumUrsus\ValuesAdapter\ParamTypes;

use AtrumUrsus\ValuesAdapter\Exception\ExceptionParam;

/**
 * @brief adapter parameter of type array with values ​​as string
 *
 */
class ParamArrayString extends ParamArrayMixed
{
	/// min string length
	private ?int $min = null;

	/// max string length
	private ?int $max = null;

	/**
	 * @brief construct
	 *
	 * @param integer|null $min min string length
	 * @param integer|null $max max string length
	 */
	public function __construct(?int $min = null, ?int $max = null)
	{
		$this->min = $min;
		$this->max = $max;
	}

	/**
	 * @brief writing data to the adapter parameter
	 *
	 * @param mixed $var
	 * @return void
	 */
	public function set(mixed $value): void
	{
		if (!is_string($value)) {
			throw new ExceptionParam('String param is corrupt');
		}
		$value = strval($value);

		$len = mb_strlen($value);
		if (!is_null($this->min) && $this->min > $len) {
			throw new ExceptionParam('Param string length is greater than min value');
		}
		if (!is_null($this->max) && $this->max < $len) {
			throw new ExceptionParam('Param string length less than max value');
		}

		parent::set($value);
	}
}
