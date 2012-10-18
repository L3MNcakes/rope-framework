<?php
/**
 * @package     RopeFramework
 * @filename    index.php
 * @author      Brandon Soucie <x3n0ph0be@tormail.org>
 *
 */

define("ROPE_BASE_PATH", getcwd());
define("ROPE_CORE_PATH", getcwd() . "/core");
define("ROPE_APPLICATION_PATH", getcwd() . "/app");
define("ROPE_CONFIG_PATH", getcwd() . "/app/config");
define("ROPE_DEFAULT_CONFIG", "configuration.ini");

require_once(ROPE_CORE_PATH . "/Bootstrap.php");
