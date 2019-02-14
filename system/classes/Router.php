<?php defined('FRTCFTYU') or die('No direct script access.');


/*
 * Route class. He determine controller and action from url
 *
*/
class Router extends Singleton {
    
    /*
    * Контроллер по умолчанию
    *
    */
    public $controller = 'Index';
    
    /*
    * Action по умолчанию
    *
    */
    public $action = 'index';
    
    /*
    * Массив с параметрами
    *
    */
    public $params = array();
    
    /*
    * Объект роута для текущего url
    *
    */
    public $route;
    
    /*
    * Массив объектов пользовательских роутов
    *
    */
    private $_routes = array();
    
    private $_config;
    
    /*
    * Массив обязательных элементов и значений. Определяет, какие элементы (контроллер, action, параметры)
    * обязательно должны присутствовать в url и их обязательные, если есть, значения 
    *
    */    
    private $_sure = array();
    
    public function __construct()
    {
        $this->_config = Useracc::config('router');
    }
    
    /*
    * Возвращает роут-объект текущего url 
    *
    */ 
    public function get()
    {
       return $this->route;
    }
    
    /*
    * Разбор url
    *
    */ 
    public function parse()
    {
        $url = $_SERVER['REQUEST_URI'];

        $this->get_routs();

        $config = Useracc::config('global');

        if($config['directory']) {

            $directory = preg_replace('/\//', '', $config['directory']);

            preg_match('/' . $directory . '(.*)/', $_SERVER['REQUEST_URI'], $match);

            $url = $match[1];
        }

        $arroute = explode( '/', $url );
        
        //Удаляем лишние символы из url
        for($i = 0; $i < count($arroute); $i++) {
            
            if(empty($arroute[$i])) {
                continue;
            }
            
            preg_match('/([A-Za-z0-9-_]+)/', $arroute[$i], $match);
            if(isset($match[1])) {
               $arroute[$i] = $match[1];
            }
            else {
                throw new Except('Страница не найдена', 404);
            }
        }

        $this->route = new self;
        
        //По url определяем нужный контроллер, action и параметры
        $this->route->controller = isset($arroute[1]) && ! empty($arroute[1]) ? ucfirst($arroute[1]) : NULL;

        $this->route->action = isset($arroute[2]) && ! empty($arroute[2]) ? $arroute[2] : NULL;

        if( isset($arroute[3]) ) {
            for($i = 3; $i < count($arroute); $i++) {
                $this->route->params[] = $arroute[$i];
            }
        }

        $this->rount_match();
        
    }
    
    /*
    * Выбор параметра из роута
    * @param $key Название параметра
    * @return Значение параметра 
    *
    */
    public function param($key)
    {
        return isset($this->params[$key]) ? $this->params[$key] : NULL;
    }
    
    /*
    * Разбор файлов с пользовательскими роутами
    *
    */
    private function get_routs()
    {
        if(is_dir(APPPATH . $this->_config['dir'])) {
            $files = scandir(APPPATH . $this->_config['dir']);
            
            //Разбор файлов с роутами из каталога
            foreach($files as $file) {
                if($file != '.' && $file != '..') {
                    $route = require APPPATH . $this->_config['dir'] .'/'. $file;

                    //Разбор файла с роутами
                    foreach($route as $item) {
                        $elements = explode('/', $item[0]);
                        
                        for($i = 0; $i < count($elements); $i++) {
                            $elements[$i] = preg_replace('/[\(\)]*/', '', $elements[$i]);
                        }

                        if(count($elements)) {
                            $obj = new self;
                            
                            //Определяем по роуту каким должен быть контроллер
                            if( isset($elements[0]) && $elements[0]{0} == ':' ) {
                                $obj->controller = isset($item[1][ substr($elements[0], 1) ]) ? ucfirst( $item[1][ substr($elements[0], 1) ] ) : 'Index';
                            }
                            elseif( !empty($item[1]['controller']) ) {
                                $arr = explode('_', $item[1]['controller']);
                                foreach($arr as $key => $v) {
                                    if( !empty($v) && $key == 0 ) {
                                        $obj->controller = ucfirst($v);
                                    }
                                    else {
                                        $obj->controller .= '_' .ucfirst($v);
                                    }
                                }
                            }
                            elseif( isset($elements[0]) && empty($item[1]['controller']) ) {
                                $obj->controller = ucfirst($elements[0]);
                            }
                            
                            //Определяем по роуту каким должен быть action
                            if( isset($elements[1]) && $elements[1]{0} == ':' ) {
                                $obj->action = !empty($item[1][ substr($elements[1], 1) ]) ? $item[1][ substr($elements[1], 1) ] : 'index';
                            }
                            elseif( !empty($item[1]['action']) ) {
                                $obj->action = $item[1]['action'];
                            }
                            elseif( isset($elements[1]) && empty($item[1]['action']) ) {
                               $obj->action = $elements[1]; 
                            }
                            
                            //Если у роута прописаны параметры, записываем их в массив параметров
                            if( isset($elements[2]) ) {
                                
                                for($i = 2; $i < count($elements); $i++) {
                                    if( isset($elements[$i]) && $elements[$i]{0} == ':' && isset($item[1][ substr($elements[$i], 1) ]) ) {
                                        $obj->params[ substr($elements[$i], 1) ] = $item[1][ substr($elements[$i], 1) ];
                                    }
                                    else {
                                        $obj->params[ substr($elements[$i], 1) ] = NULL;
                                    }
                                }
                            }

                            $obj = $this->sure_be($item, $obj);

                            $this->_routes[] = $obj;

                        }
                        
                    }
                    
                }
            }
        }

        if($this->_routes) {
            usort($this->_routes, array('Router', 'routes_sort'));
        }

    }
    
    
    /*
    * Разбирает роут, определяет обязательные элементы (контроллер, action, параметры) и 
    * их обязательные, если есть, значения.
    * @param $route Роут-объект
    * @param $obj Объект типа Route
    * @return Обрабатываемый объект
    *
    */
    private function sure_be($route, $obj)
    {
        preg_match('/([a-z0-9-_\/:]*)\(?[a-z0-9-_\/\(\):]*\)?/', $route[0], $match);

        if( ! empty($match[1]) ) {
            
            $elements = explode('/', $match[1]);
            if(count($elements)) {

                //Определяем обязательные контроллеры
                if( isset($elements[0]) && $elements[0]{0} != ':' ) {
                    $obj->_sure['controller'] = ucfirst($elements[0]);
                }
                elseif( ! empty($elements[0]) && $elements[0]{0} == ':' ) {
                    $obj->controller = NULL;
                }

                //Определяем обязательные actions
                if( isset($elements[1]) && $elements[1]{0} != ':' ) {
                    $obj->_sure['action'] = $elements[1];
                }
                elseif( isset($elements[1]) && $elements[1]{0} == ':' ) {
                    $obj->_sure['action'] = NULL;
                }

                //Если есть обязательные параметры
                if( isset($elements[2]) ) {
                    for($i = 2; $i < count($elements); $i++) {

                        if( isset($elements[$i]) && $elements[$i]{0} != ':' ) {
                            $obj->_sure['params'][ substr($elements[$i], 1) ] = $elements[$i];
                        }
                        elseif( isset($elements[$i]) && $elements[$i]{0} == ':' ) {
                            $obj->_sure['params'][ substr($elements[$i], 1) ] = NULL;
                        }
                    }
                }
            }
        }
        return $obj;
    }
    
    /*
    * Сортировка объектов-роутеров по уровню строгости (чем больше в роуте обязательных параметров, тем
    * роут считается строже)
    *
    */
    private function routes_sort($a, $b) 
    {
        if(count($a->_sure) == count($b->_sure)) {
            
            //Проверяем у кого больше пустых значений
            $empty_a = 0;
            foreach($a->_sure as $v) {
                if(!$v) {
                   $empty_a ++; 
                }
            }
            
            $empty_b = 0;
            foreach($b->_sure as $v) {
                if(!$v) {
                   $empty_b ++; 
                }
            }
            
            if($empty_a == $empty_b) {
                return 0;
            }
            else {
                return ($empty_a < $empty_b) ? -1 : 1;
            }
        }
        
        return (count($a->_sure) > count($b->_sure)) ? -1 : 1;
    }
    
    /*
    * Поиск подходящего роута для запроса и преобразование текущего роут-объекта
    *
    */
    private function rount_match()
    {
        foreach($this->_routes as $item) {
            
            //Разбор обязательных элементов
            if($item->_sure) {

                //Определение контроллера
                if( isset($item->_sure['controller']) &&  empty($this->route->controller) ) {
                    continue;
                }
                else {
                    if( ! empty($item->_sure['controller']) && $item->_sure['controller'] != $this->route->controller ) {
                        continue;
                    } 
                }
                
                //Определение action
                if( isset($item->_sure['action']) &&  empty($this->route->action) ) {
                    $this->route->action = $item->_sure['action'];
                    continue;
                }
                else {
                    if( ! empty($item->_sure['action']) && $item->_sure['action'] != $this->route->action ) {
                        $this->route->action = $item->_sure['action'];
                        continue;
                    }
                }

                //Определение параметров
                if(isset($item->_sure['params'])) {
                    if( empty($this->route->params) ) {
                        continue;
                    }
                    else {
                        if(count($item->_sure['params']) > count($this->route->params)) {
                            continue;
                        }
                        else {
                            $n = 0;
                            foreach($item->_sure['params'] as $key => $param) {
                                if( ! empty($param) && $param != $this->route->params[$n] ) {
                                    continue;
                                }
                                else {
                                   $this->route->params[$key] = $this->route->params[$n];
                                   unset($this->route->params[$n]);
                                }
                                $n++;
                            }
                        }
                    }
                }
                
            }

            //Установка элементов по-умолчанию
            $this->route->controller = !empty($this->route->controller) && isset($item->_sure['controller']) && !empty($item->_sure['controller']) ? $item->controller : ucfirst($this->route->controller);
            $this->route->controller = empty($this->route->controller) ? $item->controller : $this->route->controller;

            $this->route->action = !empty($this->route->action) && isset($item->_sure['action']) && !empty($item->_sure['action']) ? $item->action : $this->route->action;
            $this->route->action = empty($this->route->action) ? $item->action : $this->route->action;

            if($item->params) {
                $n = 0;
                foreach($item->params as $key => $param) {
                    if( isset($this->route->params[$n]) ) {
                        $this->route->params[$key] = $this->route->params[$n];
                    }
                    else {
                       $this->route->params[$key] = NULL; 
                    }
                    unset($this->route->params[$n]);
                    $n++;
                }
            }
            
            break;
        }

    }
    
}