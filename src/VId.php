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

namespace AtrumUrsus\ValuesAdapter;

/**
 * @brief converts a variable to a integer (more than 0) type
 *
 */
class VId extends VInt
{

	/**
	 * @brief construct
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->min(1);
	}
}