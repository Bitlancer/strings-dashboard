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

	public $components = array(
		'DebugKit.Toolbar',
		'Session',
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
			
		)
	);

}
