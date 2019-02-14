<?php defined('FRTCFTYU') or die('No direct script access.');

/*
 * Db connection class. 
 *
*/
class Connection
{
    /*
     * Array databases.
     * 
     */
    private static $_dbs = [];
    
    /*
     * Get db connection
     * @param $db_name Name basedate
     * 
     */
    public static function get($db_name)
    {
        if(self::$_dbs) {
            return self::$_dbs[$db_name];
        } else {
            $config = Useracc::config('database')['mysql'];
            if(is_array($config)) {
                foreach($config as $key => $item) {
                    $dsn = "mysql:host={$item[ $_SERVER['ENV'] ]['host']};dbname={$item[ $_SERVER['ENV'] ]['database']}";
                    $opt = array(
                            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    );
                    self::$_dbs[$key] = new PDO($dsn, $item[ $_SERVER['ENV'] ]['username'], $item[ $_SERVER['ENV'] ]['password'], $opt);
                    self::$_dbs[$key]->exec('set names utf8');
                }
                return self::$_dbs[$db_name];
            } else {
                die('Config is not array');
            }
        }
    }
}
