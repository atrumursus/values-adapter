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
 * @brief adapter parameter of type single with values ​​as boolean
 *
 */
class ParamSingleBool extends ParamSingleMixed
{

	/**
	 * @brief writing data to the adapter parameter
	 *
	 * @param mixed $var
	 * @return void
	 */
	public function set(mixed $value): void
	{
		if (!is_bool($value)) {
			throw new ExceptionParam('Boolean param is corrupt');
		}
		$value = boolval($value);
		parent::set($value);
	}
}
