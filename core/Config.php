<?php
if(!defined('ROPE_BASE_PATH')) die('You shall not pass!');
/**
 * @package     RopeFramework
 * @filename    Config.php
 * @author      Brandon Soucie <x3n0ph0be@tormail.org>
 *
 * Class definition for Rope-Framework Configuration.
 *
 */
class Rope_Config
{
    protected $_shortName;
    protected $_options;

    public function __construct($options=null) {
        if($options && is_array($options)) {
            $this->_options = $options;
        }
    }

    public function get($optionName) {
        if(isset($this->_options[$optionName])) {
            return $this->_options[$optionName];
        }

        return False;
    }

    public function getShortName() {
        return $this->_shortName;
    }
}
