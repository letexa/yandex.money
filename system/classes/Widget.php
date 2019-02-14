<?php defined('FRTCFTYU') or die('No direct script access.');

/*
 * Widget controller project
 *
*/
class Widget extends Singleton {
    
    /*
    * Роут-объект текущего url
    *
    */
    protected $route; 

    /*
     * Config. 
     *
    */
    protected $_config;

    public function __construct() 
    {
        parent::__construct();

        $this->route = Router::gi()->get();

    }
	
}