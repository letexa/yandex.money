<?php defined('FRTCFTYU') or die('No direct script access.');

/*
 * Main controller project
 *
*/
class Controller extends Singleton {
    
        /*
	 * Flag show template. 
	 *
	*/
	public $show = TRUE;

	/*
	 * Default template. 
	 *
	*/
	protected $_template = 'layouts/template';
	
	/*
	 * Array views. 
	 *
	*/
	protected $_content = array();
        
        /*
        * Роут-объект текущего url
        *
        */
        protected $route; 
        
        /*
        * Request-объект
        *
        */
        protected $request; 
	
	/*
	 * Config. 
	 *
	*/
	protected $_config;
        
	public function __construct() 
	{
            parent::__construct();
            
            $this->route = Router::gi()->get();
            
            $this->request = new Request;
	}

	public function __call( $methodName, $args = array() )
	{
        if( is_callable( array($this, $methodName) ) ) {
			
			return call_user_func_array(array($this, $methodName), $args);
		}
        else {
			
			throw new Except('In controller '.get_called_class().' method '.$methodName.' not found!');
		}
            
    }
	
	public function __destruct()
	{
		if ($this->show) {

			print View::factory($this->_template)
                                                    ->set($this->_content)
                                                    ->execute();
		}
		
	}
	
}