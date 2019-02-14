<?php defined('FRTCFTYU') or die('No direct script access.');


/*
 * Url class.
 *
*/
abstract class Url {
	
	/*
	 * Form url.
	 * @param string Internal url
	 *
	*/
	static public function base($url = NULL)
	{

		$config = Useracc::config('global');

		preg_match('/\/*(.*)/', $config['directory'], $match);
		
		$directory = isset($match[1]) && ! empty($match[1]) ? '/' . $match[1] : NULL;
		
		preg_match('/\/*(.*)/', $url, $match);
		
		$url = isset($match[1]) ? $match[1] : NULL;
		
		return $directory . '/' . $url;
		
	}
}