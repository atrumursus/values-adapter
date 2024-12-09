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

use AtrumUrsus\ValuesAdapter\Exception\ExceptionParam;
use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamArrayMixed;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamInterface;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleMixed;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleObject;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleString;

/**
 * @brief abstract adapter class
 *
 * setting parameters
 *   - default - default value (if conversion is not possible)
 *               | значення за замовчуванням
 *   - valid - an array of valid values ​​(if the variable is equal to (===) at least one of them - the check is successful)
 *             | масив валідних значень (якщо змінна дорівнює (===) хоч одному з них - перевірка успішна)
 *   - eMessage - ExceptionValue message - if conversion of this element is not possible
 *                | сповіщення ExceptionValue - якщо перетворення даного елементу неможливо
 *   - next - the adapter object that will be run after being processed by this adapter
 *            | об'єкт адаптера який буде запущено після обробки цим адаптером
 *
 *
 */
abstract class AdapterAbstract
{

	///An array of parameters
	private array $paramList = [];

	/**
	 * @brief construct
	 *
	 */
	public function __construct()
	{
		$params = $this->params();
		foreach ($params as $name => $item) {
			if ($item instanceof ParamInterface) {
				$this->paramList[$name] = $item;
			} else {
				throw new ExceptionParam('Parameter "' . $name . '" is not initialized correctly');
			}
		}
	}

	/**
	 * @brief returns an Array of parameter names
	 *
	 * @return array
	 */
	public function getParamList(): array
	{
		return array_keys($this->paramList);
	}

	/**
	 * @brief Returns a list of parameters and their type
	 *
	 * @return array
	 */
	protected function params(): array
	{
		return [
			'default' => new ParamSingleMixed(),
			'valid' => new ParamArrayMixed(),
			'eMessage' => new ParamSingleString(),
			'next' => new ParamSingleObject(self::class)
		];
	}

	/**
	 * @brief sets the parameter value
	 *
	 * @param string $name
	 * @param array  $values
	 * @return self
	 */
	public function set(string $name, mixed ...$values): self
	{
		if (!array_key_exists($name, $this->paramList)) {
			throw new ExceptionParam('Parameter "' . $name . '" not implemented');
		}
		foreach ($values as $item) {
			try {
				$this->paramList[$name]->set($item);
			} catch (ExceptionValue $e) {
				throw new ExceptionParam('Parameter "' . $name . '": ' . $e->getMessage(), 0, $e);
			}
		}
		return $this;
	}

	/**
	 * @brief Returns the parameter value
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function get(string $name): mixed
	{
		if (!array_key_exists($name, $this->paramList)) {
			throw new ExceptionParam('Parameter "' . $name . '" not implemented');
		}
		try {
			return $this->paramList[$name]->get();
		} catch (ExceptionValue $e) {
			throw new ExceptionParam('Parameter "' . $name . '": ' . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * @brief Checks if a parameter has been set
	 *
	 * @param string $name
	 * @return bool
	 */
	public function isset(string $name): bool
	{
		if (!array_key_exists($name, $this->paramList)) {
			return false;
		}
		return ($this->paramList[$name])->isset();
	}

	/**
	 * @brief sets the parameter value (via a method)
	 *
	 * @param string $name
	 * @param array  $arguments
	 * @return self
	 */
	public function __call(string $name, array $arguments): self
	{
		return $this->set($name, ...$arguments);
	}

	/**
	 * @brief Validates and converts the value of a variable (abstract function)
	 *
	 * @param mixed $var
	 * @return mixed
	 */
	abstract protected function prepare(mixed $var): mixed;


	/**
	 * @brief Returns the converted value of the variable
	 *
	 * @param mixed $var
	 * @return mixed
	 */
	public function convert(mixed $var): mixed
	{
		$var = $this->_convert($var);
		if ($this->isset('next')) {
			$var = ($this->get('next'))->convert($var);
		}
		return $var;
	}

	/**
	 * @brief Returns the converted value of the variable (internal logic)
	 *
	 * @param mixed $var
	 * @return mixed
	 */
	private function _convert(mixed $var): mixed
	{
		//Перевірка на встановлені валідні значення
		if ($this->isset('valid')) {
			$validList = $this->get('valid');
			foreach ($validList as $validValue) {
				if ($validValue === $var) return $validValue;
			}
		}
		//Перевірка методом
		try {
			return $this->prepare($var);
		} catch (ExceptionValue $e) {
			//Якщо встановлено значення за замовчуванням - повертаємо його
			if ($this->isset('default')) {
				return $this->get('default');
			}
			//Якщо встановлено значення eMessage - перехоплюємо
			if ($this->isset('eMessage')) {
				throw new ExceptionValue($this->get('eMessage'), 0, $e);
			}
			throw $e;
		}
	}

	/**
	 * @brief Returns the result of the value's validity (true|false)
	 *
	 * @param mixed $var
	 * @param mixed $result
	 * @return boolean
	 */
	public function validate(mixed $var, mixed &$result=null): bool
	{
		try {
			$result=$this->convert($var);
			return true;
		} catch (ExceptionValue) {
			return false;
		}
	}

	/**
	 * Synonym of method $this->validate()
	 *
	 * @param mixed $var
	 * @param mixed $result
	 * @return bool
	 */
	public function ok(mixed $var, mixed &$result = null): bool
	{
		return $this->validate($var, $result);
	}
}
