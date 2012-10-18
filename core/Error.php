<?php
if(!defined('ROPE_BASE_PATH')) die('You shall not pass!');
/**
 * @package     RopeFramework
 * @filename    Error.php
 * @author      Brandon Soucie <x3n0ph0be@tormail.org>
 *
 * Class definition for Rope-Framework Errors.
 *
 */
class Rope_Error
{
    private static $_app;
    private $_code;
    private $_message;

    public function __construct( $code=null, $message=null, &$app=null) {
        if(isset($app) && $app instanceof Rope_Application) {
            self::$_app =& $app;
        }

        $this->_code = $code;
        $this->_message = $message;

        if(!isset(self::$_app) || !(self::$_app instanceof Rope_Application)) {
            add_log("Warning : Rope_Error initialized without Application reference");
        }

        if(isset($this->_code) || isset($this->_error)) {
            add_log($this->_code . " : " . $this->_message);
        }
    }

    public static function registerApp(&$app) {
        if($app instanceof Rope_Application) {
            self::$_app =& $app;
        }
    }

    public function getCode() {
        return $this->_code;
    }

    public function getMessage() {
        return $this->_message;
    }

    public function display() {
        self::$_app->setError($this);
        self::$_app->run();
    }
}
