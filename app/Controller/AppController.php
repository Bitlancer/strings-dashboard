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
		'DebugKit.Toolbar',
		'Session',
		'Cookie',
		'Auth' => array(
			'authError' => 'Please login',
			'authenticate' => array(
				'Sha1' => array(        //User::login sets scope dynamically - must be updated if this is changed
					'fields' => array(
						'username' => 'name',
						'password' => 'password'
					)
				)
			),
			'authorize' => array('Controller'),
            'unauthorizedRedirect' => false,
		),
		'RequestHandler',
		'DataTables.DataTables',
        'Wizard.Wizard',
	);

	//View Helpers
	public $helpers = array(
		'Strings.Strings',
		'Strings.StringsTable',
		'Strings.StringsActionMenu',
		'DataTables.DataTables',
        'Form',
		'Time',
        'Gravatar'
	);

    /**
     * Default authorization logic - child controllers must override
     */
	public function isAuthorized($user){

        if($user['is_admin'])
            return true;

        return false;
	}

	public function beforeFilter(){

		//Set default cookie options
    	$this->Cookie->key = '>qfAHms1U1c{}0wC6SWZjur#!31TaB58LJQqathasGb$@11~_+!@#HKis~#^';
    	$this->Cookie->httpOnly = true;
	}

    /**
     * Whether the current request is for json
     */
    public function isJsonRequest(){
        return $this->request->ext == 'json';
    }

    /**
     * Display a generic flash message
     */
    public function setFlash($message,$type='error'){
        $this->Session->setFlash(__($message),'default',array(),$type);
    }

    /**
     * Add a Vendor library to the php path
     */
    protected function addVendorLibToPHPPath($vendorLibrary){

        $vendorLibPath = APP . 'Vendor' . DS . $vendorLibrary;
        set_include_path(get_include_path() . PATH_SEPARATOR . $vendorLibPath);
    }

    /**
     * Search for a list of entities with a matching name
     * Used for autocomplete forms
     */
    public function searchByName(){

        $this->autoRender = false;

        $search = $this->request->query['term'];

        $model = $this->modelClass;

        $results = $this->$model->find('all',array(
            'contain' => array(),
            'fields' => array(
                'id','name'
            ),
            'conditions' => array(
                'name LIKE' => "%$search%",
            )
        ));

        foreach($results as $index => $result){
            $results[$index] = $result[$model]['name'];
        }

        echo json_encode($results);
    }
}
