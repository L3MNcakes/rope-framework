<?php
class ErrorController extends Rope_Controller
{
    public function indexAction($params) {
        foreach($params["errors"] as $error) {
            echo $error->getCode() . " : " . $error->getMessage();
        }

        exit;
    }
}
