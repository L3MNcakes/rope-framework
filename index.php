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
define("ROPE_RIAK_HOST", "127.0.0.1");
define("ROPE_RIAK_PORT", "8091");

require_once(ROPE_CORE_PATH . "/Bootstrap.php");
