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
 * @brief adapter parameter of type array with values ​​as object
 *
 */
class ParamArrayObject extends ParamArrayMixed
{
	/// instantiated object of a certain class:
	private ?string $type = null;

	/**
	 * @brief construct
	 *
	 * @param string|null $type
	 */
	public function __construct(?string $type = null)
	{
		$this->type = $type;
	}

	/**
	 * @brief writing data to the adapter parameter
	 *
	 * @param mixed $var
	 * @return void
	 */
	public function set(mixed $value): void
	{
		if (!is_object($value)) {
			throw new ExceptionParam('Object param is corrupt');
		}
		if (!is_null($this->type)	&& !($value instanceof $this->type)) {
			throw new ExceptionParam('Object param not instanceof select type');
		}
		parent::set($value);
	}
}
