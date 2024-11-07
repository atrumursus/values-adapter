<?php
/*
 * This file is part of AtrumUrsus\ValuesAdapter.
 *
 * (c) Vasyl Tochylin <tochylin.vasyl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AtrumUrsus\ValuesAdapter;

use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleInt;

/**
 * @brief converts a variable to a integer type
 *
 * setting addition parameters
 *  - min - min value
 *          | мінімальне значення
 *  - max - max value
 *          | Максимальне значення
 *
 */
class VInt extends AdapterAbstract
{

	/**
	 * @brief Returns a list of parameters and their type
	 *
	 * @return array
	 */
	protected function params(): array
	{
		$param = parent::params();
		$param['min'] = new ParamSingleInt();
		$param['max'] =	new ParamSingleInt();
		return $param;
	}

	/**
	 * @brief Validates and converts the value of a variable
	 *
	 * @param mixed $var
	 * @return mixed
	 */
	protected function prepare(mixed $var): int
	{
		if (!is_numeric($var)) {
			throw new ExceptionValue('Integer value is corrupt');
		}
		$var = intval($var);
		if ($this->isset('min') && $var < $this->get('min')) {
			throw new ExceptionValue('Integer value is greater than min value');
		}
		if ($this->isset('max') && $var > $this->get('max')) {
			throw new ExceptionValue('Integer value less than max value');
		}
		return $var;
	}
}
