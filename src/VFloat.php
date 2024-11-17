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
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleInt;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleFloat;

/**
 * @brief converts a variable to a float type
 *
 * setting addition parameters
 *  - min - min value
 *          | мінімальне значення
 *  - max - max value
 *          | Максимальне значення
 *  - toFixed - rounds the string to a specified number of decimals.
 *          | форматує число з вказаною кількість цифр після крапки.
 *
 * @method self default( mixed $var )
 * @method self valid(... mixed $var)
 * @method self eMessage( string $msg)
 * @method self next( AdapterAbstract $adapter)
 * @method self min(float $min)
 * @method self max(float $max)
 * @method self toFixed(integer $countDecimals)
 *
 */
class VFloat extends AdapterAbstract
{

	/**
	 * @brief Returns a list of parameters and their type
	 *
	 * @return array
	 */
	protected function params(): array
	{
		$param = parent::params();
		$param['min'] = new ParamSingleFloat();
		$param['max'] =	new ParamSingleFloat();
		$param['toFixed'] =	new ParamSingleInt(0);
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
		if (is_string($var)) {
			$var = str_replace(",", ".", $var);
		}
		if (!is_numeric($var)) {
			throw new ExceptionValue('Float value is corrupt');
		}
		$var = floatval($var);
		if ($this->isset('min') && $var < $this->get('min')) {
			throw new ExceptionValue('Float value is greater than min value');
		}
		if ($this->isset('max') && $var > $this->get('max')) {
			throw new ExceptionValue('Float value less than max value');
		}
		if ($this->isset('toFixed')) {
			$num = $this->get('toFixed');
			$var = number_format($var, $num, '.', '');
		}
		return $var;
	}
}
