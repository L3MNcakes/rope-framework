<?php
if(!defined('ROPE_BASE_PATH')) die('You shall not pass!');
/**
 * @package     RopeFramework
 * @filename    Bootstrap.php
 * @author      Brandon Soucie <L3MNcakes@gmail.com>
 * 
 * ROPE BOOTSTRAPPER FILE
 * This is the Rope-Framework bootstrapper file. It's responsible for
 * gathering all of the resources required to get a Rope Application
 * started.
 *
 */

// LOAD ALL USER-DEFINED LIBRARIES
$dh = opendir(ROPE_APPLICATION_PATH . "/libraries");
while(($file = readdir($dh)) !== false) {
    if(!is_dir(ROPE_APPLICATION_PATH . "/libraries/". $file) && preg_match('/php/', $file)) {
        require_once(ROPE_APPLICATION_PATH . "/libraries/" . $file);
    }
}

// LOAD COMMON FUNCTIONS
if(file_exists(ROPE_CORE_PATH . "/Functions.php")) {
    require_once(ROPE_CORE_PATH . "/Functions.php");
}

// LOAD CONSTANTS
if(file_exists(ROPE_CORE_PATH . "/Constants.php")) {
    require_once(ROPE_CORE_PATH . "/Constants.php");
}

// LOAD APPLICATION CLASS
if(file_exists(ROPE_APPLICATION_PATH . "/override/Application.php")) {
    // Check for override
    require_once(ROPE_APPLICATION_PATH . "/override/Application.php");
} else {
    require_once(ROPE_CORE_PATH . "/Application.php");
}

// LOAD CONTROLLER CLASS
if(file_exists(ROPE_APPLICATION_PATH . "/override/Controller.php")) {
    // Check for override
    require_once(ROPE_APPLICATION_PATH . "/override/Controller.php");
} else {
    require_once(ROPE_CORE_PATH . "/Controller.php");
}

// LOAD MODEL CLASS
if(file_exists(ROPE_APPLICATION_PATH . "/override/Model.php")) {
    // Check for override
    require_once(ROPE_APPLICATION_PATH . "/override/Model.php");
} else {
    require_once(ROPE_CORE_PATH . "/Model.php");
}

// LOAD VIEW CLASS
if(file_exists(ROPE_APPLICATION_PATH . "/override/View.php")) {
    // Check for override
    require_once(ROPE_APPLICATION_PATH . "/override/View.php");
} else {
    require_once(ROPE_CORE_PATH . "/View.php");
}

// LOAD CONFIG CLASS
if(file_exists(ROPE_APPLICATION_PATH . "/override/Config.php")) {
    // Check for override
    require_once(ROPE_APPLICATION_PATH . "/override/Config.php");
} else {
    require_once(ROPE_CORE_PATH . "/Config.php");
}

// LOAD ERROR CLASS
if(file_exists(ROPE_APPLICATION_PATH . "/override/Error.php")) {
    // Check for override
    require_once(ROPE_APPLICATION_PATH . "/override/Error.php");
} else {
    require_once(ROPE_CORE_PATH . "/Error.php");
}

// LOAD ALL APP-CONFIG CLASSES
$dh = opendir(ROPE_CONFIG_PATH);
while(($file = readdir($dh)) !== false) {
    if(!is_dir(ROPE_CONFIG_PATH . "/" . $file) && preg_match('/php/', $file)){
        require_once(ROPE_CONFIG_PATH . "/" . $file);
    }
}
closedir($dh);

// LOAD ALL APP-CONTROLLER CLASSES
$dh = opendir(ROPE_APPLICATION_PATH . "/controllers");
while(($file = readdir($dh)) !== false) {
    if(!is_dir(ROPE_APPLICATION_PATH . "/controllers/" . $file) && preg_match('/php/', $file)) {
        require_once(ROPE_APPLICATION_PATH . "/controllers/" . $file);
    }
}
closedir($dh);

// LOAD ALL APP-MODEL CLASSES
$dh = opendir(ROPE_APPLICATION_PATH . "/models");
while(($file = readdir($dh)) !== false) {
    if(!is_dir(ROPE_APPLICATION_PATH . "/models/" . $file) && preg_match('/php/', $file)) {
        require_once(ROPE_APPLICATION_PATH . "/models/" . $file);
    }
}

// Create Application
$app = new Rope_Application();
// echo "<PRE>";
// print_r($app);
$app->run();
