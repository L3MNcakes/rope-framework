<?php
if(!defined('ROPE_BASE_PATH')) die('You shall not pass!');
/**
 * @package     RopeFramework
 * @filename    Controller.php
 * @author      Brandon Soucie <x3n0ph0be@tormail.org>
 *
 * Class definition for Rope-Framework Controller.
 *
 */
class Rope_Controller
{
    private static $_app;

    public function __construct(&$app=null) {
        self::$_app =& $app;
    }

    public function getApplication() {
        return self::$_app;
    }

    public function loadView($viewName=null, $ext='php') {
        if(!isset($viewName) || $viewName == null) {
            $viewName = strtolower(get_class($this));
            $viewName = str_replace('controller','',$viewName);
            if($viewName == "index") {
                $viewName = $this->getApplication()->getConfig()->get('default_view_name');
            }
        }

        $viewFile = ROPE_APPLICATION_PATH . "/views/" . $viewName . "." . $ext;
        if(file_exists($viewFile)) {
            include_once($viewFile);
        } else {
            $error = new Rope_Error('View', 'Rope-View ' . $viewFile . ' does not exist.');
            $error->display();
        }
    }

    public function param($paramName=null) {
        return $this->getApplication()->getParameter($paramName);
    }
}
