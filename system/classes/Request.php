<?php defined('FRTCFTYU') or die('No direct script access.');


/*
 * Request class.
 *
*/
class Request {
    
    private $request;
    
    public function __construct()
    {
        $this->request = $_REQUEST;
    }
    
    public function query($key = NULL)
    {
        if($key) {
            return isset($this->request[$key]) ? $this->request[$key] : NULL;
        }
        else {
            return $this->request;
        }
    }
    
    public function method()
    {
        return mb_strtolower($_SERVER['REQUEST_METHOD']);
    }
    
}