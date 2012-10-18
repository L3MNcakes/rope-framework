<?php

function add_log($message) {
    $log_file = ROPE_APPLICATION_PATH . "/log/message.log";
    $fh = fopen($log_file, "a");
    fwrite($fh, time() . " : " . $message . "\n");
    fclose($fh);
}
