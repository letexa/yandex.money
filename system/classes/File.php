<?php defined('FRTCFTYU') or die('No direct script access.');

/**
 * File worker
 * 
 */
class File {
    
    const DIR = '/public/uploads';
    
    public static function upload()
    {
        if (empty($_FILES['file'])) {
            return;
        }
        
        $uploaddir = __DIR__ . '/../..' . self::DIR;
        if (!file_exists($uploaddir)) {
            mkdir($uploaddir);
        }
        $file_name = 
        $uploadfile = $uploaddir . '/' . basename($_FILES['file']['name']);
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
            return self::DIR . '/' . $_FILES['file']['name'];
        } else {
            return false;
        }
    }
}