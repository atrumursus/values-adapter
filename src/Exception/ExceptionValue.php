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

namespace AtrumUrsus\ValuesAdapter\Exception;

/**
 * @brief Exception for activity with values
 *
 */
class ExceptionValue extends Exception
{
	/**
	 * @brief Return first Exception from previous data
	 *
	 * @return \Throwable
	 */
	public function getFirstException()
	{
		return static::getPreviousException($this);
	}

	/**
	 * @brief Return first Exception from previous data
	 *
	 * @param \Throwable $exception
	 * @return \Throwable
	 */
	protected static function getPreviousException(\Throwable $exception)
	{
		$previousException = $exception->getPrevious();
		if ($previousException instanceof \Throwable) {
			return static::getPreviousException($previousException);
		}
		return $exception;
	}
}
