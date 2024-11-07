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

namespace AtrumUrsus\ValuesAdapter\ParamTypes;

use AtrumUrsus\ValuesAdapter\Exception\ExceptionParam;

/**
 * @brief adapter parameter of type single with values ​​as enum
 *
 */
class ParamSingleEnum extends ParamSingleMixed
{
	///array of possible values
	private array $elements = [];

	/**
	 *  @brief construct
	 *
	 * @param array|null $elements array of possible values
	 */
	public function __construct(?array $elements = null)
	{
		if (is_array($elements)) {
			$this->elements = $elements;
		}
	}

	/**
	 * @brief writing data to the adapter parameter
	 *
	 * @param mixed $var
	 * @return void
	 */
	public function set(mixed $value): void
	{
		foreach ($this->elements as $item) {
			if ($item === $value) {
				parent::set($value);
				return;
			}
		}
		throw new ExceptionParam('Parameter does not belong to elements');
	}
}
