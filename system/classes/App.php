<?php defined('FRTCFTYU') or die('No direct script access.');

/*
 * Start up the application
 *
*/
class App extends Singleton {
	
	/*
	 * Check controller exists
	 *
	*/
	private function __is_controller($controller)
	{
		if ( ! class_exists($controller) ) {
		
			throw new Except('Controller ' . $controller . ' not found', '404');
		}
		
		return new $controller;
	}
	
	/*
	 * Check action exists in controller
	 *
	*/
	private function __is_action($controller, $action)
	{

		if ( ! method_exists($controller, $action) ) {
		
			throw new Except('Action ' . $action . ' not found in controller ' . get_class($controller), 404);
		}
		
		return $action;
	}
	
	/*
	 * Creator new controller and start action
	 *
	*/
    public function start()
    {
        Router::gi()->parse();
        
        $route = Router::gi()->get();
        
        $controller = 'Controller_' . $route->controller;

        try {
            $controller = $this->__is_controller($controller);
            $action = $this->__is_action($controller, 'action_' . $route->action);
        } 
        catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }

        $controller->$action();
    }
	
}