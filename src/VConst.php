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

namespace AtrumUrsus\ValuesAdapter;

use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleMixed;

/**
 * @brief Returns the value set in the default value or parameter (regardless of the input value)
 *
 * setting addition parameters
 *   - value - value of the result
 *             | значення результату
 *
 */
class VConst extends AdapterAbstract
{

	/**
	 * @brief Returns a list of parameters and their type
	 *
	 * @return array
	 */
	protected function params(): array
	{
		$param = parent::params();
		$param['value'] = new ParamSingleMixed();
		return $param;
	}

	/**
	 * @brief Validates and converts the value of a variable
	 *
	 * @param mixed $var
	 * @return mixed
	 */
	protected function prepare(mixed $var): mixed
	{
		if ($this->isset('value')) {
			return $this->get('value');
		}
		if ($this->isset('default')) {
			return $this->get('default');
		}
		throw new ExceptionValue('Param "value" or "default" not set');
	}
}
