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
 * @brief adapter parameter of type single with values ​​as int
 *
 */
class ParamSingleInt extends ParamSingleMixed
{
	/// min value
	private $min = null;

	/// max value
	private $max = null;

	/**
	 * @brief construct
	 *
	 * @param integer|null $min min value
	 * @param integer|null $max max value
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
		if (!is_numeric($value)) {
			throw new ExceptionParam('Integer param is corrupt');
		}
		$value = intval($value);
		if (!is_null($this->min) && $this->min > $value) {
			throw new ExceptionParam('Integer param is corrupt');
		}
		if (!is_null($this->max) && $this->max < $value) {
			throw new ExceptionParam('Integer param is corrupt');
		}
		parent::set($value);
	}
}
