<?php defined('FRTCFTYU') or die('No direct script access.');

/**
 * Generate string.
 *
 */
class Strgenerate {

	public static function str($num = NULL)
	{
		if ( $num == NULL ) {
			$num = rand(6,10);
		}
		else {
			$num = (integer) $num;
		}
		
		$chars = 'abdefhiknrstyzABDEFHIKNRSTYZ0123456789';
		$numChars = strlen($chars);
		$string = '';
		
		for ($i = 0; $i < $num; $i++) {
			$string .= substr($chars, rand(1, $numChars) - 1, 1);
		}
		return $string;
	}
	
}