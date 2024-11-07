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

use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleCallable;

/**
 * @brief Creates an associative array from the input value according to the transformation map
 *
 * setting addition parameters
 *  - preConvert -  function of preliminary conversion of the original value (before the start of the conversion according to the map)
 *               | функція попереднього перетворення вихідного значення (до початку перетворення згідно мапі)
 *  - postConvert -  function convert output value after conversion according to map
 *                | функція  перетворення вихідного значення після  перетворення згідно мапі
 *
 */
class VDict extends VDictAbstract
{

	/**
	 * @brief returns conversion maps (stub)
	 *
	 * @return array
	 */
	protected function getMap(): array
	{
		return [];
	}

	/**
	 * @brief Returns a list of parameters and their type
	 *
	 * @return array
	 */
	protected function params(): array
	{
		$param = parent::params();
		$param['preConvert'] = new ParamSingleCallable();
		$param['postConvert'] = new ParamSingleCallable();
		return $param;
	}

	/**
	 * @brief function of preliminary conversion of the original value (before the start of the conversion according to the map)
	 *
	 * @param mixed $var
	 * @return mixed
	 */
	protected function runPreConvert(mixed $var): mixed
	{
		if ($this->isset('preConvert')) {
			return call_user_func($this->get('preConvert'), $var);
		}
		return $var;
	}

	/**
	 * @brief function convert output value after conversion according to map
	 *
	 * @param mixed $var
	 * @return mixed
	 */
	protected function runPostConvert(mixed $var): mixed
	{
		if ($this->isset('postConvert')) {
			return call_user_func($this->get('postConvert'), $var);
		}
		return $var;
	}

	/**
	 * @brief function of preparing and saving the transformation map
	 *
	 * @param array $map
	 * @return self
	 */
	public function map(array $map): self
	{
		$this->prepareMap($map);
		return $this;
	}
}
