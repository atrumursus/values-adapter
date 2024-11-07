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

/**
 * @brief An interface for reading and writing to the adapter parameter
 *
 */
interface ParamInterface
{
	/**
	 * @brief writing data to the adapter parameter
	 *
	 * @param mixed $var
	 * @return void
	 */
	public function set(mixed $var): void;

	/**
	 * @brief reading data into the adapter parameter
	 *
	 * @return mixed
	 */
	public function get(): mixed;

	/**
	 * @brief Determine if a variable is declared
	 *
	 * @return boolean
	 */
	public function isset(): bool;
}
