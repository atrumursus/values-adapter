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

use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleString;
use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;

/**
 * @brief converts a variable to a datetime type
 *
 * setting addition parameters
 *  - inFormat  - date format if the variable is of type string
 *              | формат дати, якщо змінна типу строки
 *              @see /DateTime::createFromFormat()
 *  - outFormat - if set - the conversion result will be a date in the specified format
 *              | якщо встановлено - результатом конвертації буде  строка в вказаному форматі
 *               @see /DateTime::format()
 *  - tz        - string TimeZone
 *               @see https://www.php.net/manual/ru/timezones.php
 */
class VDateTime extends AdapterAbstract
{

	/**
	 * @brief Returns a list of parameters and their type
	 *
	 * @return array
	 */
	protected function params(): array
	{
		$param = parent::params();
		$param['tz'] = new ParamSingleString();
		$param['inFormat'] = new ParamSingleString();
		$param['outFormat'] =	new ParamSingleString();
		return $param;
	}


	/**
	 * @brief Validates and converts the value of a variable
	 *
	 * @param mixed $var
	 * @return \DateTime|string
	 */
	protected function prepare($var): \DateTime|string
	{
		try {
			$var = $this->prepareDateTime($var);
		} catch (\Exception $e) {
			throw new ExceptionValue("DateTime value is corrupt", 0, $e);
		}

		if ($this->isset('outFormat')) {
			$var = $var->format($this->get('outFormat'));
			if ($var === false) {
				throw new ExceptionValue("DateTime outFormat parameter is corrupt");
			}
		}

		return $var;
	}

	/**
	 * @brief Validates and converts the value of a variable
	 *
	 * @param mixed $var
	 * @return \DateTime
	 */
	protected function prepareDateTime(mixed $var): \DateTime
	{

		if (empty($var)) {
			throw new ExceptionValue("DateTime value is empty");
		}

		$timezone = null;
		if ($this->isset('tz')) {
			$timezone	= new \DateTimeZone($this->get('tz'));
		}

		if ($var instanceof \DateTimeInterface) {
			$var=\DateTime::createFromInterface($var);
			if (!empty($timezone)) {
				($var)->setTimezone($timezone);
			}
			return $var;
		}

		if (is_numeric($var) && intval($var) > 0) {
			$date = new \DateTime('now', $timezone);
			$date->setTimestamp(intval($var));
			return $date;
		}

		if (is_scalar($var) && strlen(strval($var)) > 0) {
			if ($this->isset('inFormat')) {
				return \DateTime::createFromFormat($this->get('inFormat'), $var, $timezone);
			}
			return new \DateTime($var, $timezone);
		}
		throw new ExceptionValue("DateTime value is corrupt");
	}
}
