<?php defined('FRTCFTYU') or die('No direct script access.');

/*
 * View class. 
 *
*/
class View extends Singleton {
	
	/*
	 * Additional view file. 
	 *
	*/
	public $view;
	
	/*
	 * Constants array. 
	 *
	*/
	static public $constants = array();
	
	/*
	 * Array of local variables. 
	 *
	*/
	protected $_data = array();
	
	static public function factory($view)
	{
            $obj = new View();

            if ($view) {

                    $file = APPPATH . 'view' . DIRECTORY_SEPARATOR . $view . EXT;

                    if( ! file_exists($file) ) {

                            $file = SYSTEM . 'view' . DIRECTORY_SEPARATOR . $view . EXT;
                    }

                    if( ! file_exists($file) ) {

                        foreach (scandir(MODPATH) as $module) {
                            
                            if( file_exists(MODPATH . $module .DIRECTORY_SEPARATOR. 'view' .DIRECTORY_SEPARATOR. $view . EXT) ) {

                                $file = MODPATH . $module .DIRECTORY_SEPARATOR. 'view' .DIRECTORY_SEPARATOR. $view . EXT;

                                break;
                            }
                        }
                    }

                    $obj->view = $file;

            }

            return $obj;
	}
	
	/*
	 * Set new constant. 
	 * @param $name Name constant
	 * $param $value Value constant
	 *
	*/
	static public function set_const($name, $value)
	{
		self::$constants[$name] = $value;
	}
	
	/*
	 * Get constant. 
	 * @param $name Name constant
	 * @return Value constant
	 *
	*/
	static public function get_const($name)
	{
		return self::$constants[$name];
	}
	
	/*
	 * Return content View object. 
	 *
	*/
	public function execute() 
	{
            extract($this->_data, EXTR_SKIP);

            ob_start();

            try
            {
                // Load the view within the current scope
                include $this->view;

            }
            catch (Exception $e)
            {
                // Delete the output buffer
                ob_end_clean();

                // Re-throw the exception
                throw $e;
            }

            return ob_get_clean();
	}
	
	
	/*
	 * Set variable in a template. 
	 *
	*/
	public function set($variable = NULL, $value = NULL)
	{
		if ( is_array($variable) ) {
		
			foreach ($variable as $key => $item) {
				
				$this->_data[$key] = $item;
			}			
		}
		else {
		
			$this->_data[$variable] = $value;
		}

		return $this;
	}
	
    
}