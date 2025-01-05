<?php /*
+-------------------------------------------------------+
| Rajarshi Das						                    |
+-------------------------------------------------------+
| Created On: 26-Dec-2022				                |
| Updated On: 											|
+-------------------------------------------------------+
| Form Validation for non ci3 modules.					|
+-------------------------------------------------------+
*/



// --------------------------------------------------------------------

/**
 * Performs a Regular Expression match test.
 *
 * @param	string
 * @param	string	regex
 * @return	bool
 */
function regex_match($str, $regex)
{
	return (bool) preg_match($regex, $str);
}

// --------------------------------------------------------------------

/**
 * Minimum Length
 *
 * @param	string
 * @param	string
 * @return	bool
 */
function min_length($str, $val)
{
	if (!is_numeric($val)) {
		return FALSE;
	}

	return ($val <= mb_strlen($str));
}

// --------------------------------------------------------------------

/**
 * Max Length
 *
 * @param	string
 * @param	string
 * @return	bool
 */
function max_length($str, $val)
{
	if (!is_numeric($val)) {
		return FALSE;
	}

	return ($val >= mb_strlen($str));
}

// --------------------------------------------------------------------

/**
 * Exact Length
 *
 * @param	string
 * @param	string
 * @return	bool
 */
function exact_length($str, $val)
{
	if (!is_numeric($val)) {
		return FALSE;
	}

	return (mb_strlen($str) === (int) $val);
}
// --------------------------------------------------------------------

/**
 * Valid Email
 *
 * @param	string
 * @return	bool
 */
function valid_email($str)
{
	if (function_exists('idn_to_ascii') && preg_match('#\A([^@]+)@(.+)\z#', $str, $matches)) {
		$domain = defined('INTL_IDNA_VARIANT_UTS46')
			? idn_to_ascii($matches[2], 0, INTL_IDNA_VARIANT_UTS46)
			: idn_to_ascii($matches[2]);

		if ($domain !== FALSE) {
			$str = $matches[1] . '@' . $domain;
		}
	}

	return (bool) filter_var($str, FILTER_VALIDATE_EMAIL);
}

// --------------------------------------------------------------------

/**
 * Alpha
 *
 * @param	string
 * @return	bool
 */
function alpha($str)
{
	return ctype_alpha($str);
}

// --------------------------------------------------------------------

/**
 * Alpha-numeric
 *
 * @param	string
 * @return	bool
 */
function alpha_numeric($str)
{
	return ctype_alnum((string) $str);
}

// --------------------------------------------------------------------

/**
 * Alpha-numeric w/ spaces
 *
 * @param	string
 * @return	bool
 */
function alpha_numeric_spaces($str)
{
	return (bool) preg_match('/^[A-Z0-9 ]+$/i', $str);
}

// --------------------------------------------------------------------

/**
 * Alpha-numeric with underscores and dashes
 *
 * @param	string
 * @return	bool
 */
function alpha_numeric_dash($str)
{
	return (bool) preg_match('/^[a-z0-9_-]+$/i', $str);
}

// --------------------------------------------------------------------

/**
 * Alpha-numeric with underscores and dashes and space
 *
 * @param	string
 * @return	bool
 */
function alpha_numeric_text($str)
{
	return (bool) preg_match('/^[a-z0-9 _\-,.();\/]+$/i', $str);
}

// --------------------------------------------------------------------

/**
 * Alpha-numeric with dot
 *
 * @param	string
 * @return	bool
 */
function alpha_numeric_dot($str)
{
	return (bool) preg_match('/^[a-z0-9.]+$/i', $str);
}
// --------------------------------------------------------------------

/**
 * Alpha-numeric with comma (Rajarshi)
 *
 * @param	string
 * @return	bool
 */
function alpha_numeric_comma($str)
{
	return (bool) preg_match('/^[a-z0-9,]+$/i', $str);
}
// --------------------------------------------------------------------

/**
 * Numeric
 *
 * @param	string
 * @return	bool
 */
function numeric($str)
{
	return (bool) preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $str);
}

// --------------------------------------------------------------------

/**
 * Integer
 *
 * @param	string
 * @return	bool
 */
function integer($str)
{
	return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
}

// --------------------------------------------------------------------

/**
 * Decimal number
 *
 * @param	string
 * @return	bool
 */
function decimal($str)
{
	return (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
}

// --------------------------------------------------------------------

/**
 * Greater than
 *
 * @param	string
 * @param	int
 * @return	bool
 */
function greater_than($str, $min)
{
	return is_numeric($str) ? ($str > $min) : FALSE;
}

// --------------------------------------------------------------------

/**
 * Equal to or Greater than
 *
 * @param	string
 * @param	int
 * @return	bool
 */
function greater_than_equal_to($str, $min)
{
	return is_numeric($str) ? ($str >= $min) : FALSE;
}

// --------------------------------------------------------------------

/**
 * Less than
 *
 * @param	string
 * @param	int
 * @return	bool
 */
function less_than($str, $max)
{
	return is_numeric($str) ? ($str < $max) : FALSE;
}

// --------------------------------------------------------------------

/**
 * Equal to or Less than
 *
 * @param	string
 * @param	int
 * @return	bool
 */
function less_than_equal_to($str, $max)
{
	return is_numeric($str) ? ($str <= $max) : FALSE;
}

// --------------------------------------------------------------------

/**
 * Value should be within an array of values
 *
 * @param	string
 * @param	string
 * @return	bool
 */
function in_list($value, $list)
{
	return in_array($value, explode(',', $list), TRUE);
}

// --------------------------------------------------------------------

/**
 * Is a Natural number  (0,1,2,3, etc.)
 *
 * @param	string
 * @return	bool
 */
function is_natural($str)
{
	return ctype_digit((string) $str);
}

// --------------------------------------------------------------------

/**
 * Is a Natural number, but not a zero  (1,2,3, etc.)
 *
 * @param	string
 * @return	bool
 */
function is_natural_no_zero($str)
{
	return ($str != 0 && ctype_digit((string) $str));
}

// --------------------------------------------------------------------

/**
 * Valid Base64
 *
 * Tests a string for characters outside of the Base64 alphabet
 * as defined by RFC 2045 http://www.faqs.org/rfcs/rfc2045
 *
 * @param	string
 * @return	bool
 */
function valid_base64($str)
{
	return (base64_encode(base64_decode($str)) === $str);
}
