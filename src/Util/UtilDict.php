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

namespace AtrumUrsus\ValuesAdapter\Util;

use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;

/**
 *  @brief functions for working with associative arrays
 */
final class UtilDict
{

	/**
	 *  @brief returns the value of an element according to the specified keys
	 *
	 * @param mixed $src path key(s) to value
	 * @param array $var value array
	 * @return mixed
	 */
	public static function extractor(mixed $src, array $var)
	{
		if (is_array($src)) {
			$path = $src;
		} elseif (is_string($src) && $src != '') {
			$path = [$src];
		} else {
			throw new ExceptionValue('Value is corrupted for path search');
		}

		foreach ($path as $name) {
			if (array_key_exists($name, $var)) {
				$var = $var[$name];
				continue;
			}
			throw new ExceptionValue('Value not found on path');
		}
		return $var;
	}
}
