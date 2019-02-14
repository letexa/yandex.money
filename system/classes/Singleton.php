<?php defined('FRTCFTYU') or die('No direct script access.');

/*
 * Creator new object for patern singleton
 *
*/
abstract class Singleton {
    

	/*
	 * Array objects classes 
	 *
	*/
	private static $_aInstances = array();
	
	public function __construct(){}
	
	private static function getInstance() 
	{
		$sClassName = get_called_class();
		
		if( class_exists($sClassName) ) {
		
			if( ! isset( self::$_aInstances[ $sClassName ] ) ) {
				
				// If object class not was create, his create 
				self::$_aInstances[ $sClassName ] = new $sClassName();
			}
			
			// return objest
			return self::$_aInstances[ $sClassName ]; 
		}
		
		return NULL;
	}
	
	/*
	 * Call method getInstance()
	 *
	*/
	public static function gi() 
	{
		return self::getInstance();
	}
	
	final private function __clone() {}
	
}