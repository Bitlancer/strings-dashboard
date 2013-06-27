<?php

class TestsController extends AppController {

    public $uses = array('Device');

    public function index(){

        $this->viewPath .= DS . "test";

    }

}
