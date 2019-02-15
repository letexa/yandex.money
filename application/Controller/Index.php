<?php defined('FRTCFTYU') or die('No direct script access.');

/*
 * Default controller
 *
*/

class Controller_Index extends Controller {
    
    protected $_template = 'layouts/index';
    
    private $_errors = [];
    
    private $reflection;
    
    public function __construct() 
    {
        parent::__construct();
        $this->config = Useracc::config('global');
    }
	
    /**
     * Index URL
     *
    */
    public function action_index() 
    {
        if ($_FILES) {
            $storage = new \Upload\Storage\FileSystem(UPLOADS);
            $file = new \Upload\File('file', $storage);
            
            $new_filename = uniqid();
            $file->setName($new_filename);
            
            $file->addValidations(array(
                new \Upload\Validation\Mimetype('text/x-php'),
                new \Upload\Validation\Size('5M')
            ));
            
            try {
                $file->upload();
                $classes = get_declared_classes();
                require_once UPLOADS .'/'. $file->getNameWithExtension();
                $diff = array_diff(get_declared_classes(), $classes);
                if ($diff) {
                    $this->reflection = new ReflectionClass(reset($diff));
                }
            } catch (\Exception $e) {
                $this->_errors = $file->getErrors();
            }
        }
        
        $view = View::factory('index/main')->set('errors', $this->_errors);
        $this->_content['content'] = View::factory('index/main')->set('errors', $this->_errors);
        if ($_FILES) {
            $view->set('reflection', $this->reflection);
        }
        $this->_content['content'] = $view->execute();
    }
    
}