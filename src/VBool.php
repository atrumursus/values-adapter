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
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamArrayString;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleBool;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleMixed;

/**
 * @brief converts a variable to a boolean type
 *
 * setting addition parameters
 *   - asTrue - an array of additional (case-insensitive) string values ​​that are associated with true
 *              | масив додаткових строкових значень (без урахування регістру) які асоціюються з true
 *   - asFalse - an array of additional string values ​​(case-insensitive) that are associated with false
 *               | масив додаткових строкових значень (без урахування регістру) які асоціюються з false
 *   - true - the value that is returned if the variable is true
 *            | значення яке вертається, якщо змінна дорівнює true
 *   - false - the value that is returned if the variable is false
 *             | значення яке вертається, якщо змінна дорівнює false
 *   - boolval - apply the boolval() function to the input value
 *               | застосувати функцію boolval() до вхідного значення
 *
 */
class VBool extends AdapterAbstract
{

	/**
	 *  @brief construct
	 */
	public function __construct()
	{
		parent::__construct();
		$this->asTrue('true', 'yes', 'on');
		$this->asFalse('', 'false', 'no', 'not', 'off');
		$this->true(true);
		$this->false(false);
	}

	/**
	 * @brief Returns a list of parameters and their type
	 *
	 * @return array
	 */
	protected function params(): array
	{
		$param = parent::params();
		$param['asTrue'] = new ParamArrayString();
		$param['asFalse'] = new ParamArrayString();
		$param['true'] = new ParamSingleMixed();
		$param['false'] = new ParamSingleMixed();
		$param['boolval'] = new ParamSingleBool();
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
		$result = [
			'false' => $this->get('false'),
			'true' => $this->get('true')
		];

		if ($this->isset('boolval') && $this->get('boolval')) {
			$var = boolval($var);
		};

		if ($var === false) return $result['false'];
		if ($var === true)  return $result['true'];

		//Якщо змінна число та дорівнює 0 - false, інше - true
		if (is_numeric($var)) {
			return ((int)($var) == 0) ? $result['false'] : $result['true'];
		}

		//Якщо строка - дивимось по константам
		if(is_scalar($var)){
			$var = mb_strtolower((string)$var);
			if ($this->isset('asTrue')) {
				$paramAsTrue = array_fill_keys($this->get('asTrue'), true);
				$paramAsTrue = array_change_key_case($paramAsTrue, CASE_LOWER);
				if (array_key_exists($var, $paramAsTrue)) {
					return $result['true'];
				}
			}
			if ($this->isset('asFalse')) {
				$paramAsFalse = array_fill_keys($this->get('asFalse'), true);
				$paramAsFalse = array_change_key_case($paramAsFalse, CASE_LOWER);
				if (array_key_exists($var, $paramAsFalse)) {
					return $result['false'];
				}
			}
		}
		throw new ExceptionValue('Bool value is corrupt');
	}
}
