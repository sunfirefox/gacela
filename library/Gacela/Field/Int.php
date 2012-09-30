<?php
/**
 * @author noah
 * @date 3/19/11
 *
 *
*/

namespace Gacela\Field;

/**
 * Error Codes - 'null', 'not_int'
 */
class Int extends Field
{
	const TYPE_CODE = 'invalid_int';
	const BOUNDS_CODE = 'out_of_bounds';

	/**
	 * @static
	 * @param $meta
	 * @param $value
	 * @return bool|string
	 */
	public static function validate($meta, $value)
	{
		if(is_null($value)) {
			if($meta->sequenced) {
				return true;
			} else {
				if(!$meta->null) {
					return self::NULL_CODE;
				}

				return $meta->null;
			}
		}

		if(!is_int($value)) {
			return self::TYPE_CODE;
		} elseif(strlen(abs($value)) > $meta->length) {
			return self::LENGTH_CODE;
		} elseif($value < $meta->min || $value > $meta->max) {
			return self::BOUNDS_CODE;
		}

		return true;
	}

	/**
	 * @static
	 * @param $meta
	 * @param $value
	 * @param bool $in
	 * @return int|mixed|null
	 */
	public static function transform($meta, $value, $in = true)
	{
		if(!is_int($value) && is_numeric($value)) {
			$value = (int) $value;
		} elseif($value === '' || $value === false) {
			$value = null;
		}

		return $value;
	}
}
