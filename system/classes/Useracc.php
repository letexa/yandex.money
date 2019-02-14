<?php defined('FRTCFTYU') or die('No direct script access.');


/*
 * Superclass application.
 *
 *
*/
class Useracc {


	/*
	 * Configuration method.
	 * @return array
	 *
	*/
	public static function config($file)
	{
		$config = array();
		
		/*
		 * Configuration from system directory.
		 *
		*/
		if ( file_exists(SYSTEM . 'config/' . $file . EXT) ) {
			
			$config = include SYSTEM . 'config/' . $file . EXT;
		}
		
		/*
		 * Configuration from modules directory.
		 *
		*/
		if ( file_exists(DOCROOT . 'modules') ) {
			
			foreach ( scandir(DOCROOT . 'modules') as $dir) {

				if ( is_dir(DOCROOT . 'modules/' . $dir) && file_exists( DOCROOT . 'modules/' . $dir .'/config/'. $file . EXT) )	{

					$config = include DOCROOT . 'modules/' . $dir .'/config/'. $file . EXT;
				}				
			}
		}

		/*
		 * Configuration from application directory.
		 *
		*/
		if ( file_exists(APPPATH . 'config/' . $file . EXT) ) {

			$config = include APPPATH . 'config/' . $file . EXT;
		}
		
		return $config;
	}
	
	/*
	 * Message method.
	 * @return array
	 *
	*/
	public static function message($file)
	{
		$message = array();
		
		/*
		 * Message from system directory.
		 *
		*/
		if ( file_exists(SYSTEM . 'message/' . $file . EXT) ) {
			
			$message = include SYSTEM . 'message/' . $file . EXT;
		}
		
		/*
		 * Message from modules directory.
		 *
		*/
		if ( file_exists(DOCROOT . 'modules') ) {
			
			foreach ( scandir(DOCROOT . 'modules') as $dir) {

				if ( is_dir(DOCROOT . 'modules/' . $dir) && file_exists( DOCROOT . 'modules/' . $dir .'/message/'. $file . EXT) )	{

					$message = include DOCROOT . 'modules/' . $dir .'/message/'. $file . EXT;
				}				
			}
		}

		/*
		 * Message from application directory.
		 *
		*/
		if ( file_exists(APPPATH . 'message/' . $file . EXT) ) {

			$message = include APPPATH . 'message/' . $file . EXT;
		}
		
		return $message;
	}
	
	/*
	 * Calling api.
	 * @param string $string
	 * @param Parameter for action 
	 *
	*/
	public static function api($string = NULL, $param = NULL)
	{
		if ( ! defined('SID') ) {
		
			session_start();
		}
		
		$class_name = 'Api';
		
		$action = 'index';
		
		if($string) {
			
			$array = explode('.', $string);
			
			if( isset($array[0]) ) {
				
				$class_name = ucfirst($array[0]);
			}
			
			if( isset($array[1]) ) {
				
				$action = $array[1];
			}
			
		}
		
		$file = API . $class_name. EXT;
		if( ! file_exists($file) )
			return false;
		require_once ($file);
		
		$api = new $class_name;
		
		$api->show = FALSE;

		return $api->$action($param);
		
	}

}