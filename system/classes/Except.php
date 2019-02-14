<?php defined('FRTCFTYU') or die('No direct script access.');

/*
 * Except class
 *
*/
class Except extends Exception {
	
	/*
	 * Array types exception
	 *
	*/
	protected $_types = array('404' => 'error',
				  '400' => 'error' );
	
	/*
	 * Type exception
	 *
	*/	
	protected $_type;
	
	public function __construct($message = null, $code = 400, Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);

		$this->_type = array_key_exists($code, $this->_types) ? $this->_types[ $code ] : $this->_types['400'];
		
		if( $_SERVER['ENV'] == 'PRODUCTION' && file_exists(APPPATH . 'Controller/Error' . EXT) === TRUE ) {

			header( 'Location: ' . Url::base('error/') . $this->getCode() );
		}
		else {
			
			die( View::factory('except/index')
                                                        ->set('type', 	 ucfirst($this->_type))
                                                        ->set('message', $message)
                                                        ->execute());
		}
	}
	
}