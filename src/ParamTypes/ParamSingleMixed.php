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
 * @brief single type adapter parameter with arbitrary values
 *
 */
class ParamSingleMixed implements ParamInterface
{
	/// parameter value
	private $value;

	/// parameter initialization indicator
	private $isUndef = true;

	/**
	 * @brief writing data to the adapter parameter
	 *
	 * @param mixed $var
	 * @return void
	 */
	public function set(mixed $value): void
	{
		$this->value = $value;
		$this->isUndef = false;
	}

	/**
	 * @brief reading data into the adapter parameter
	 *
	 * @return mixed
	 */
	public function get(): mixed
	{
		if ($this->isUndef) {
			throw new ExceptionParam("Param not set");
		}
		return $this->value;
	}

	/**
	 * @brief Determine if a variable is declared
	 *
	 * @return boolean
	 */
	public function isset(): bool
	{
		return !$this->isUndef;
	}
}
