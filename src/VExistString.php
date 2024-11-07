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

/**
 * @brief converts a variable to a exist string type
 *
 */
class VExistString extends VString
{

	/**
	 * @brief construct
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->trim(true);
		$this->min(1);
	}
}
