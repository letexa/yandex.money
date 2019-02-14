<?php defined('FRTCFTYU') or die('No direct script access.');

/*
 * Main model project. 
 *
*/
class RedisModel extends Redis {
    
    public function __construct()
    {
        $config = Useracc::config('database')['redis'];
        $this->connect($config['host'], $config['port']);
    }
}