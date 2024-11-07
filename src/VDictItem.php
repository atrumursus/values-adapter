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

use AtrumUrsus\ValuesAdapter\AdapterAbstract;
use AtrumUrsus\ValuesAdapter\Exception\ExceptionParam;
use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamArrayMixed;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamArrayString;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleBool;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleCallable;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleObject;
use AtrumUrsus\ValuesAdapter\Util\UtilDict;


/**
 * @brief Creates an element associative array from the input value according to the transformation map (abstract class)
 *
 * setting addition parameters
 *   - src - an array of input array key names to get the value to element convert
 *         | масив назв ключів вхідного масиву
 *   - required - if true - this element of the array is necessary (by default - false)
 *              | якщо true - цей елемент масиву є необхідним (за умовчанням - false)
 *   - extractor - pre-conversion function
 *                convert(mixed: $data, array: $options):mixed
 *                where:
 *                  $data - the original value,
 *                  $options - this item array (by default - null)
 *               | функція попереднього перетворення
 *                 convert(mixed: $data, array: $options):mixed
 *                 де:
 *                    $data - вихідне значення,
 *                    $options - цей масив item (за умовчанням - null)
 *   - option -  custom options that can be used in the extractor function
 *               | користувацькі опції які можна задіяти в функції extractor
 *   - adapter - adapter object to convert the value to (defaults to null)
 *               | об'єкт адаптеру для перетворення значення (за умовчанням - null)
 *
 */
class VDictItem extends AdapterAbstract
{
	/// associative array key
	protected $key = null;

	public function __construct(string $key)
	{
		parent::__construct();
		$this->required(false);
		if (!is_string($key) || $key == '') {
			throw new ExceptionParam('key is corrupt');
		}
		$this->key = strval($key);
	}

	/**
	 * @brief Returns a list of parameters and their type
	 *
	 * @return array
	 */
	protected function params(): array
	{
		$param = parent::params();
		$param['src'] = new ParamArrayString();
		$param['required'] = new ParamSingleBool();
		$param['option'] = new ParamArrayMixed();
		$param['extractor'] = new ParamSingleCallable();
		$param['adapter'] = new ParamSingleObject(AdapterAbstract::class);
		unset($param['default']);
		unset($param['valid']);

		return $param;
	}

	/**
	 * @brief Validates and converts the value of a variable
	 *
	 * @param mixed $var
	 * @return mixed
	 */
	protected function prepare(mixed $var): mixed
	{
		$result = [];
		try {
			if ($this->isset('extractor')) {
				$var = call_user_func($this->get('extractor'), $var, ($this->isset('option')) ? $this->get('option') : null);
			} elseif ($this->isset('src')) {
				try {
					$var = UtilDict::extractor($this->get('src'), $var);
				} catch (ExceptionValue) {
					if (!$this->get('required')) {
						return $result;
					}
					if (!$this->isset('adapter')) {
						throw new ExceptionValue('Required Value not found');
					}
					$var = null;
				}
			}

			if ($this->isset('adapter')) {
				$var = ($this->get('adapter'))->convert($var);
			}
			$result[$this->key] = $var;
		} catch (ExceptionValue $e) {
			if (!$this->get('required')) {
				return $result;
			}
			throw new ExceptionValue($this->key . ': ' . $e->getMessage(), 0, $e);
		}
		return $result;
	}
}
