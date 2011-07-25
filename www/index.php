<?php

// uncomment this line if you must temporarily take down your site for maintenance
// require '.maintenance.php';

// absolute filesystem path to the web root
define('WWW_DIR', __DIR__);

define('APP_DIR_FRONT', WWW_DIR . "/../app");
define('APP_DIR_ADMIN', WWW_DIR . "/../admin");
define('APP_DIR_INSTALLATION', WWW_DIR . "/../installation");

// Detect $path
$pathName = str_replace($_SERVER['DOCUMENT_ROOT'],"",str_replace("index.php","",$_SERVER['SCRIPT_FILENAME']));
$serverName = $_SERVER['SERVER_NAME'];
if(substr($serverName, -1) == "/") $serverName = substr ($serverName, 0, -1);
if(substr($pathName, 0, 1) == "/") $pathName = substr ($pathName, 1);
$path = $serverName."/".$pathName;
if(substr($path, -1) == "/") $path = substr ($path, 0, -1);
define('ROOT_PATH', $path);

if(strpos($_SERVER["REQUEST_URI"], $pathName."admin/installation") === false){
	if(strpos($_SERVER["REQUEST_URI"], $pathName."admin") === false){
		if(!file_exists(WWW_DIR . "/../temp/installed")){
			//die("Location: /" . $path . "/admin/installation");
			header("Location: //" . $path . "/admin/installation");
		}else{
			define('APP_DIR', APP_DIR_FRONT);
			define('VENNE_MODE_FRONT', 1);
		}
	}else{
		define('APP_DIR', APP_DIR_ADMIN);
		define('VENNE_MODE_ADMIN', 1);
	}
}else{
	define('APP_DIR', APP_DIR_INSTALLATION);
	define('VENNE_MODE_INSTALLATION', 1);
}

// absolute filesystem path to the libraries
define('LIBS_DIR', WWW_DIR . '/../libs');

// absolute filesystem path to the temporary files
define('TEMP_DIR', WWW_DIR . '/../temp');

// load bootstrap file
require APP_DIR . '/bootstrap.php';
