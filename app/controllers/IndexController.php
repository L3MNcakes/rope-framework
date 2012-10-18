<?php

class IndexController extends Rope_Controller
{
    public function indexAction() {
        // echo "<PRE>";
        // print_r($this->getApplication()->getConfig('default'));
        $this->loadView();
    }
}
