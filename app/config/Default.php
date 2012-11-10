<?php

class DefaultConfig extends Rope_Config
{
    protected $_shortName = "default";

    protected $_options = array(
        "version" => "0.1",
        "load_configs" => array('Test'),
        "default_controller" => "Index",
        "default_action" => "indexAction",
        "default_error_controller" => "Error",
        "default_view_name" => "default",
        "riak_host" => "127.0.0.1",
        "riak_port" => "8091",
    );
}
