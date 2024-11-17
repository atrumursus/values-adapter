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

use AtrumUrsus\ValuesAdapter\Exception\ExceptionValue;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleEnum;
use AtrumUrsus\ValuesAdapter\ParamTypes\ParamSingleString;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberToCarrierMapper;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumber;
use libphonenumber\geocoding\PhoneNumberOfflineGeocoder;

/**
 * @brief converts a variable to a phone number
 *
 * setting addition parameters
 *  - country - country by default (for those phones in which the country is not specified)
 *              | країна за замовчанням (для тих телефонів в яких країна не вказана)
 *  - locale - the language code for which the description should be written
 *  - output - output type
 *              | формат виводу
 *
 *     - self::OUTPUT_E164          - Produces  +380441234567
 *     - self::OUTPUT_INTERNATIONAL - Produces  +380 44 123 4567
 *     - self::OUTPUT_NATIONAL      - Produces  044 123 4567
 *     - self::OUTPUT_RFC3966       - Produces  tel:+380-44-123-4567
 *     - self::OUTPUT_COUNTRY       - Produces  UA
 *     - self::OUTPUT_TYPE          - Produces  FIXED_LINE
 *     - self::OUTPUT_DESCRIPTION   - Produces  м. Київ (@see parameter locale)
 *
 * use library:
 *  @link https://github.com/giggsey/libphonenumber-for-php?tab=readme-ov-file
 *
 * @method self default( mixed $var )
 * @method self valid(... mixed $var)
 * @method self eMessage( string $msg)
 * @method self next( AdapterAbstract $adapter)
 * @method self country(string $alpha2)
 * @method self locale(string $languageCode)
 * @method self output(string $outputType)
 *
 */

class VPhone extends VString
{
	const OUTPUT_E164 = 'E164';
	const OUTPUT_INTERNATIONAL = 'International';
	const OUTPUT_NATIONAL = 'National';
	const OUTPUT_RFC3966 = 'RFC3966';
	const OUTPUT_COUNTRY = 'Country';
	const OUTPUT_TYPE = 'Type';
	const OUTPUT_DESCRIPTION = 'Description';

	/**
	 *  @brief construct
	 */
	public function __construct()
	{
		parent::__construct();
		$this->output('International');
		$currentLocale = setlocale(LC_ALL, 0);
		if (!empty($currentLocale)) {
			$this->locale($currentLocale);
		}
	}

	/**
	 * @brief Returns a list of parameters and their type
	 *
	 * @return array
	 */
	protected function params(): array
	{
		$param = parent::params();
		$param['output'] = new ParamSingleEnum([
			static::OUTPUT_E164,
			static::OUTPUT_INTERNATIONAL,
			static::OUTPUT_NATIONAL,
			static::OUTPUT_RFC3966,
			static::OUTPUT_COUNTRY,
			static::OUTPUT_TYPE,
			static::OUTPUT_DESCRIPTION
		]);
		$param['country'] =	new ParamSingleString(2, 2);
		$param['locale'] = new ParamSingleString();
		return $param;
	}


	/**
	 * @brief Validates and converts the value of a variable
	 *
	 * @param mixed $var
	 * @return mixed
	 */
	protected function prepare(mixed $var): string
	{
		$phoneUtil = PhoneNumberUtil::getInstance();
		try {
			$phone = $phoneUtil->parse($var, ($this->isset('country')) ? $this->get('country') : null);
		} catch (NumberParseException $e) {
			try {
				$phone = $phoneUtil->parse('+' . $var, ($this->isset('country')) ? $this->get('country') : null);
			} catch (NumberParseException $e) {
				throw new ExceptionValue('Phone value is corrupt', 0, $e);
			}
		}
		try {
			$result =	match (($this->isset('output') ? $this->get('output') : static::OUTPUT_INTERNATIONAL)) {
				static::OUTPUT_E164 => $phoneUtil->format($phone, PhoneNumberFormat::E164),
				static::OUTPUT_NATIONAL => $phoneUtil->format($phone, PhoneNumberFormat::NATIONAL),
				static::OUTPUT_RFC3966 => $phoneUtil->format($phone, PhoneNumberFormat::RFC3966),
				static::OUTPUT_COUNTRY => $phoneUtil->getRegionCodeForNumber($phone),
				static::OUTPUT_TYPE => self::prepareOutputType($phone, $phoneUtil),
				static::OUTPUT_DESCRIPTION => self::prepareOutputDescription($phone, $this->isset('locale') ? $this->get('locale') : 'en'),
				default  =>	$phoneUtil->format($phone, PhoneNumberFormat::INTERNATIONAL),
			};
			return parent::prepare($result);
		} catch (NumberParseException $e) {
			throw new ExceptionValue('Phone value is corrupt', 0, $e);
		}
	}

	/**
	 * Prepare output data for self::OUTPUT_DESCRIPTION
	 *
	 * @param PhoneNumber $phone
	 * @param string $locale
	 * @return string
	 */
	protected static function prepareOutputDescription(PhoneNumber $phone, string $locale): string
	{
		$geoCoder = PhoneNumberOfflineGeocoder::getInstance();
		$carrierMapper = PhoneNumberToCarrierMapper::getInstance();
		return trim(
			$geoCoder->getDescriptionForNumber($phone, $locale)
				. ' '
				. $carrierMapper->getNameForNumber($phone, $locale)
		);
	}

	/**
	 * Prepare output data for self::OUTPUT_TYPE
	 *
	 * @param PhoneNumber $phone
	 * @param PhoneNumberUtil $phoneUtil
	 * @return string
	 */
	protected static function prepareOutputType(PhoneNumber $phone, PhoneNumberUtil $phoneUtil): string
	{
		$values	= PhoneNumberType::values();
		$type = $phoneUtil->getNumberType($phone);
		return $values[$type] ?? 'UNKNOWN';
	}
}
