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

/**
 * @brief converts a variable to a email
 *
 */
class VEmail extends VExistString
{

	/**
	 * @brief Validates and converts the value of a variable
	 *
	 * @param mixed $var
	 * @return mixed
	 */
	protected function prepare(mixed $var): string
	{
		try {
			$var = parent::prepare($var);
		} catch (ExceptionValue $e) {
			throw new ExceptionValue('Email address value is corrupt', 0, $e);
		}
		if (!filter_var($var, FILTER_VALIDATE_EMAIL)) {
			throw new ExceptionValue('Email address value is corrupt');
		}
		return $var;
	}
}
