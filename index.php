<?php
if ($_SERVER['ENV'] == 'DEVELOPMENT') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

require_once 'vendor/autoload.php';

$application = __DIR__ . '/application';

$system = __DIR__ . '/system';

$modules = __DIR__ . '/modules';

$lib = __DIR__ . '/lib';

$classes = __DIR__ . '/classes';

define('DOCROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('UPLOADS', DOCROOT . '/uploads');

if ( ! is_dir($application) && is_dir(DOCROOT.$application))
	$application = DOCROOT.$application;

if ( ! is_dir($system) && is_dir(DOCROOT.$system))
	$system = DOCROOT.$system;

if ( ! is_dir($modules) && is_dir(DOCROOT.$modules))
	$modules = DOCROOT.$modules;

if ( ! is_dir($lib) && is_dir(DOCROOT.$lib))
	$lib = DOCROOT.$lib;

if ( ! is_dir($classes) && is_dir(DOCROOT.$classes))
	$classes = DOCROOT.$classes;

define('SYSTEM',   realpath($system).DIRECTORY_SEPARATOR);
define('APPPATH',  realpath($application).DIRECTORY_SEPARATOR);
define('FRTCFTYU', realpath($system).DIRECTORY_SEPARATOR);
define('MODPATH',  realpath($modules).DIRECTORY_SEPARATOR);
define('LIB',      realpath($lib).DIRECTORY_SEPARATOR);
define('CLASSES',  realpath($classes).DIRECTORY_SEPARATOR);

define('EXT', '.php');

include_once SYSTEM . 'before' . EXT;

App::gi()->start();
