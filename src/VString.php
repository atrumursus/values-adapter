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
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleBool;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleEnum;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleInt;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleString;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamArrayString;

/**
 * @brief converts a variable to a string type
 *
 * setting addition parameters
 *  - min - min value
 *          | мінімальне значення
 *  - max - max value
 *          | Максимальне значення
 *  - trim - discards leading and trailing spaces
 *          | відкидає пробіли з початку та кінця
 *  - case - converts letters: 'upper', 'lower', 'header'
 *           | перетворює літери: 'upper', 'lower', 'title'
 *  - withPrefix - if there is none, it adds the specified prefix
 *                 | якщо нема то додає вказаний префікс
 *  - withoutPrefix - if present, it removes the specified prefix
 *                    | якщо є - то видаляє вказаний префікс
 *  - withSuffix - if there is none, it adds the specified suffix
 *                 | якщо нема то додає вказаний суфікс
 *  - withoutSuffix - if there is, it removes the specified suffix
 *                    | якщо є - то видаляє вказаний суфікс
 *
 */
class VString extends AdapterAbstract
{

	const CASE_UPPER = 'upper';
	const CASE_LOWER = 'lower';
	const CASE_TITLE = 'title';
	const CASE_NULL = null;

	/**
	 *  @brief construct
	 */
	public function __construct()
	{
		parent::__construct();
	}

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
		$param['trim'] = new ParamSingleBool();
		$param['case'] = new ParamSingleEnum([static::CASE_UPPER, static::CASE_LOWER, static::CASE_TITLE, static::CASE_NULL]);
		$param['withPrefix'] = new ParamSingleString();
		$param['withoutPrefix'] = new ParamArrayString();
		$param['withSuffix'] = new ParamSingleString();
		$param['withoutSuffix'] = new ParamArrayString();
		return $param;
	}

	/**
	 * @brief Validates and converts the value of a variable
	 *
	 * @param mixed $var
	 * @return mixed
	 */
	protected function prepare(mixed $var): string
	{

		if (!is_null($var) && !is_scalar($var)) {
			throw new ExceptionValue('String value is corrupt');
		}
		$var = (string)$var;

		if ($this->isset('trim')) {
			if ($this->get('trim')) $var = trim($var);
		}

		if ($this->isset('case')) {
			$var =	match ($this->get('case')) {
				static::CASE_UPPER =>  mb_convert_case($var, MB_CASE_UPPER_SIMPLE),
				static::CASE_LOWER  => mb_convert_case($var, MB_CASE_LOWER_SIMPLE),
				static::CASE_TITLE  =>	mb_convert_case($var,	MB_CASE_TITLE_SIMPLE),
				default   => $var,
			};
		}

		if ($this->isset('withoutPrefix')) {
			$prefixList = $this->get('withoutPrefix');
			foreach ($prefixList as $prefix) {
				$lenPrefix = mb_strlen($prefix);
				if ($lenPrefix > 0) {
					$subVal = mb_substr($var, 0, $lenPrefix);
					if ($prefix == $subVal) {
						$var = mb_substr($var, $lenPrefix);
						continue;
					}
				}
			}
		}

		if ($this->isset('withPrefix')) {
			$prefix = $this->get('withPrefix');
			$lenPrefix = mb_strlen($prefix);
			if ($lenPrefix > 0) {
				$subVal = mb_substr($var, 0, $lenPrefix);
				if ($prefix != $subVal) {
					$var = $prefix . $var;
				}
			}
		}

		if ($this->isset('withoutSuffix')) {
			$suffixList = $this->get('withoutSuffix');
			foreach ($suffixList as $suffix) {
				$lenSuffix = mb_strlen($suffix);
				if ($lenSuffix > 0) {
					$subVal = mb_substr($var, (-1 * $lenSuffix), $lenSuffix);
					if ($suffix == $subVal) {
						$var = mb_substr($var, 0, (-1 * $lenSuffix));
						continue;
					}
				}
			}
		}

		if ($this->isset('withSuffix')) {
			$suffix = $this->get('withSuffix');
			$lenSuffix = mb_strlen($suffix);
			if ($lenSuffix > 0) {
				$subVal =	mb_substr($var, (-1 * $lenSuffix), $lenSuffix);
				if ($suffix != $subVal) {
					$var =  $var . $suffix;
				}
			}
		}

		$len = mb_strlen($var);

		if ($this->isset('min') && $len < $this->get('min')) {
			throw new ExceptionValue('String length is greater than min value');
		}

		if ($this->isset('max') && $len > $this->get('max')) {
			throw new ExceptionValue('String length less than max value');
		}
		return $var;
	}
}
