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
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamArrayObject;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamArrayString;
use AtrumUrsus\ValuesAdapter\Util\UtilDict;

/**
 * @brief Creates an associative array from the input value according to the transformation map (abstract class)
 *
 * setting addition parameters
 *  - src - an array of input array key names to get the value to convert
 *       | масив назв ключів вхідного масиву для отримання значення, яке буде перетворюватися згідно мапі перетворень
 *  - item -  an array of transformation map elements
 *        | масив елементів мапи перетворення
 *
 * @method self default( mixed $var )
 * @method self valid(... mixed $var)
 * @method self eMessage( string $msg)
 * @method self next( AdapterAbstract $adapter)
 * @method self src(... string $itemPath)
 * @method self item(... VDictItem $item)
 *
 */
abstract class VDictAbstract extends AdapterAbstract
{

	/**
	 * @brief construct
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->prepareMap($this->getMap());
	}

	/**
	 * @brief function of preliminary conversion of the original value (before the start of the conversion according to the map)
	 *
	 * @param mixed $var
	 * @return mixed
	 */
	abstract protected function runPreConvert(mixed $var): mixed;

	/**
	 * @brief function convert output value after conversion according to map
	 *
	 * @param mixed $var
	 * @return mixed
	 */
	abstract protected function runPostConvert(mixed $var): mixed;

	/**
	 * @brief returns conversion maps (stub)
	 *
	 * @return array
	 */
	abstract protected function getMap(): array;


	/**
	 * @brief Returns a list of parameters and their type
	 *
	 * @return array
	 */
	protected function params(): array
	{
		$param = parent::params();
		$param['src'] = new ParamArrayString();
		$param['item'] = new ParamArrayObject(VDictItem::class);
		return $param;
	}

	/**
	 * @brief function of preparing and saving the transformation map
	 *
	 * map element format:
	 *
	 *
	 * key => [
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
	 *   - eMessage - ExceptionValue message - if conversion of this element is not possible
	 *                | сповіщення ExceptionValue - якщо перетворення даного елементу неможливо
	 *  ]
	 *
	 *
	 * or
	 *
	 *  key => src
	 *
	 *
	 * @param array $map
	 * @return void
	 */
	protected function prepareMap(array $map): void
	{
		foreach ($map as $key => $data) {
			$item = new VDictItem($key);
			if(!is_array($data) && !is_string($data)){
				throw new ExceptionParam('Param dict map is corrupt');
			}
			if (!is_array($data)) {
				$data = [$data];
			}
			$isFullFormat = false;
			// Full format
			if (array_key_exists('src', $data)) {
				if(!is_array($data['src'])){
					$data['src'] = [$data['src']];
				}
				$item->src(...$data['src']);
				$isFullFormat = true;
			}
			if (array_key_exists('required', $data)) {
				$item->required($data['required']);
				$isFullFormat = true;
			}
			if (array_key_exists('extractor', $data)) {
				$item->extractor($data['extractor']);
				$isFullFormat = true;
			}
			if (array_key_exists('adapter', $data)) {
				$item->adapter($data['adapter']);
				$isFullFormat = true;
			}
			if (array_key_exists('option', $data)) {
				$item->option($data['option']);
				$isFullFormat = true;
			}
			if (array_key_exists('eMessage', $data)) {
				$item->eMessage($data['eMessage']);
				$isFullFormat = true;
			}
			// Simple format
			if (!$isFullFormat && !empty($data)) {
				$item->src(...$data);
			}
			$this->item($item);
		}
	}

	/**
	 * @brief Validates and converts the value of a variable
	 *
	 * @param mixed $var
	 * @return mixed
	 */
	protected function prepare(mixed $var): mixed
	{

		$var = $this->runPreConvert($var);

		if ($this->isset('src')) {
			$var = UtilDict::extractor($this->get('src'), $var);
		}
		$map = [];
		if ($this->isset('item')) {
			$map = $this->get('item');
		}
		$result = [];
		foreach ($map as $item) {
			$result = array_merge($result, $item->convert($var));
		}
		return $result;
	}

	/**
	 * @brief  conversion of an array of associative arrays
	 *
	 * @param array $list
	 * @return array
	 */
	public function convertList(array $list)
	{
		$result = [];
		foreach ($list as $data) {
			$result[] =	$this->convert($data);
		}
		return $result;
	}
}
