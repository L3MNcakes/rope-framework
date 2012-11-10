<?php
if(!defined('ROPE_BASE_PATH')) die('You shall not pass!');
/**
 * @package     RopeFramework
 * @filename    Application.php
 * @author      Brandon Soucie <x3n0ph0be@tormail.org>
 *
 * Class definition for Rope Framework Application.
 *
 */
class Rope_Application
{
    private static $_instance;

    public $config;
    public $controller;
    public $action;
    public $parameters;
    public $errors;

    public function __construct() {
        add_log("SYSTEM: Constructed Rope_Application");

        // Register instance to static
        self::$_instance =& $this;

        // Initialize Configuration options
        $this->_initConfig();

        // Initialize Errors
        $this->_initError();

        // Initialize Route
        $this->_initRoute();
    }

    protected function _initConfig() {
        add_log("SYSTEM: Intilizing Configuration");

        // Initialize Default Configuration
        $this->config["default"] = new DefaultConfig();
        add_log("CONFIG: Loaded Default");

        // Check for additional configuration files
        $load_configs = $this->getConfig("default")->get('load_configs');
        if(!empty($load_configs)) {
            foreach($load_configs as $load_config) {
                add_log("CONFIG: Loaded " . $load_config);
                // Initialize additional configurations
                $className = ucfirst($load_config) . "Config";
                $instance = new $className();
                $shortName = $instance->getShortName();
                $this->config[$shortName] = $instance;
            }
        }

        add_log("SYSTEM: Configuration init Complete.");
    }

    protected function _initError() {
        add_log("SYSTEM: Intilizing Error Object");
        // Registers the App with the Rope_Error class
        Rope_Error::registerApp($this);
        add_log("SYSTEM: Error Object init Complete.");
    }

    protected function _initRoute() {
       add_log("SYSTEM: Initilizing Route...");
       // Grab request URI
       $requestURI = explode('/', $_SERVER['REQUEST_URI']);

       // Determine controller and action from request URI
       // Set defaults in the DefaultConfig
       $this->controller = isset($requestURI[2]) ? $requestURI[2] : $this->getConfig("default")->get('default_controller');
       $this->action = isset($requestURI[3]) ? $requestURI[3] : $this->getConfig("default")->get('default_action');

       add_log("ROUTE: Controller - " . $this->controller);
       add_log("ROUTE: Action - " . $this->action);

       // Set the parameters from the request URI
       if(count($requestURI) > 4) {
           for($i=4;$i<count($requestURI);$i=$i+2) {
               $this->parameters[$requestURI[$i]] = $requestURI[$i+1];
           }
       }
       add_log("ROUTE: Params - Loaded : " . (count($this->parameters)));
       add_log("SYSTEM: Route init complete");
    }

    public function run() {
        add_log("APPLICATION: Running Application");

        // Define important riak constants from config
        define("ROPE_RIAK_HOST", $this->getConfig("default")->get('riak_host'));
        define("RIPE_RIAK_PORT", $this->getConfig("default")->get('riak_port'));

        // Check for errors
        if(count($this->errors) > 0) {
            add_log("APPLICATION: Caught an error... Re-Routing...");
            // Re-route to default error controller set in config
            $this->controller = $this->getConfig("default")->get('default_error_controller');
            $this->action = $this->getConfig("default")->get('default_action');
            // Pass errors in as a parameter to error controller
            unset($this->parameters);
            $this->parameters["errors"] = $this->errors;

            add_log("ROUTE: Controller - " . $this->controller);
            add_log("ROUTE: Action - " . $this->action);
            add_log("ROUTE: Params - Loaded : " . (count($this->parameters)));
        }

        // Load the class from specified controller
        $className = ucfirst($this->controller) . "Controller";
        if(class_exists($className)) {
            // Load and call specified method from action 
            $instance = new $className($this);
            $method = $this->action;
            if(method_exists($instance, $method)) {
                $instance->$method($this->parameters);
                exit;
            }
        }

        // Direct to 404 Error if class or method does not exist
        $error = new Rope_Error(ROPE_ERROR_404_CODE, ROPE_ERROR_404_MESSAGE);
        $error->display();
    }

    public function getConfig($shortName=null) {
        // Figure out which config to query
        if(!isset($shortName) || $shortName == '') {
            $shortName = 'default';
        }

        // Return Rope_Config Object
        if(isset($this->config[$shortName])) {
            return $this->config[$shortName];
        } else {
            return new Rope_Config();
        }
    }

    public function setError($errorObj) {
        if(!is_object($errorObj) || !($errorObj instanceof Rope_Error)) {
            $errorObj = new Rope_Error('Error', 'System flagged an Error but failed to provide details.');
        }

        $this->errors[] = $errorObj;
    }

    public function getParameter($paramName=null) {
        if(!$paramName) {
            return $this->parameters;
        }

        if(isset($this->parameters[$paramName])) {
            return $this->parameters[$paramName];
        }

        return False;
    }
}
