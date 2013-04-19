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
		'DataTables.DataTables',
	);

	public $helpers = array('Strings');

	public function beforeFilter(){

		//Set default cookie options
    	$this->Cookie->key = '>qfAHms1U1c{}0wC6SWZjur#!31TaB58LJQqathasGb$@11~_+!@#HKis~#^';
    	$this->Cookie->httpOnly = true;
	}

	public function parseDatatablesRequest($model,$request,$columns){
		
		$findParameters = array();
		
		if(isset($request['iDisplayStart']) && $request['iDisplayLength'] != '-1'){
			$findParameters['offset'] = $request['iDisplayStart'];
			$findParameters['limit'] = $request['iDisplayLength'];
		}
		
		$findParameters['order'] = array();
		if(isset($request['iSortCol_0']))
		{
			for ($i=0;$i<intval($request['iSortingCols']);$i++)
			{
				if($request['bSortable_'.intval($request['iSortCol_'.$i])] == "true")
				{
					$findParameters['order'][] = $model.".".$columns[intval($request['iSortCol_'.$i])] . " " . 
						$request['sSortDir_'.$i];
				}
			}
		}

		return $findParameters;
	}
}
