<?php

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	//Controller Components
	public $components = array(
		//'DebugKit.Toolbar',
		'Session',
		'Cookie',
		'Auth' => array(
			'authError' => 'Please login',
			'authenticate' => array(
				'Form' => array(
					'fields' => array(
						'username' => 'name',
						'password' => 'password'
					)
				)
			),
			
		),
		'RequestHandler',
		'DataTables.DataTables'
	);

	//View Helpers
	public $helpers = array(
		'Strings',
		'DataTables.DataTables'
	);

	public function beforeFilter(){

		//Set default cookie options
    	$this->Cookie->key = '>qfAHms1U1c{}0wC6SWZjur#!31TaB58LJQqathasGb$@11~_+!@#HKis~#^';
    	$this->Cookie->httpOnly = true;
	}
}
