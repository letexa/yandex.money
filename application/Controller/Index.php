<?php defined('FRTCFTYU') or die('No direct script access.');

/*
 * Default controller
 *
*/

class Controller_Index extends Controller {
    
    protected $_template = 'layouts/index';
    
    const DIR = DOCROOT . '/public/uploads';
    
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
        $this->_content['content'] = View::factory('index/main')->execute();

        if ($_FILES) {
            $storage = new \Upload\Storage\FileSystem(self::DIR);
            $file = new \Upload\File('file', $storage);
            
            $new_filename = uniqid();
            $file->setName($new_filename);
            
            $file->addValidations(array(
                new \Upload\Validation\Mimetype('text/x-php'),
                new \Upload\Validation\Size('5M')
            ));
            
            try {
                $file->upload();
            } catch (\Exception $e) {
                $errors = $file->getErrors();
                print_r($errors);
            }
            
        }
    }
    
}