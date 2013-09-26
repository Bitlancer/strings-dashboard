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
        'DataTables.DataTables',
		'RequestHandler',
        'Wizard.Wizard',
	);

	//View Helpers
	public $helpers = array(
        'DataTables.DataTables',
		'Strings.Strings',
		'Strings.StringsTable',
		'Strings.StringsActionMenu',
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
        $this->setCookieOptions();

        //Pass isAdmin flag to every view
        $this->passIsAdminFlagToView();

        //Record a timestamp each time the user
        //requests a page. This can be used to
        //determine whether its necessary to
        //refresh an organization's IaaS data.
        $this->recordActivityTimestamp();
	}

    /**
     * Set default cookie options
     */
    private function setCookieOptions(){

        $this->Cookie->key = '>qfAHms1U1c{}0wC6SWZjur#!31TaB58LJQqathasGb$@11~_+!@#HKis~#^';
        $this->Cookie->httpOnly = true;
    }

    /**
     * Pass isAdmin flag to every view
     */
    private function passIsAdminFlagToView(){

        $this->set('isAdmin',$this->Auth->User('is_admin'));
    }

    /**
     * Update strings.last_activity_timestamp in the config table
     */
    private function recordActivityTimestamp(){

        $this->loadModel('Config');

        $RECORD_ACTIVITY_INTERVAL = 120;

        $currentTimestamp = time();

        //User is logged in
        $userId = $this->Auth->User('id');
        if(empty($userId))
            return;

        //To reduce load on the DB we'll only update this record
        //every $RECORD_ACTIVITY_INTERVAL per user
        $lastActivityTimestamp = $this->Session->read('lastActivityTimestamp');
        if(empty($lastActivityTimestamp) ||
           $lastActivityTimestamp + $RECORD_ACTIVITY_INTERVAL <= $currentTimestamp){

            $this->Session->write('lastActivityTimestamp',$currentTimestamp);

            $this->Config->updateAll(
                array(
                    'Config.val' => $this->Config->escapeValue(date('Y-m-d H:i:s',$currentTimestamp))
                ),
                array(
                    'Config.var' => 'strings.last_activity_timestamp',
                    'Config.organization_id' => $this->Auth->User('organization_id')
                )
            );
        }
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
     * Output generic Ajax form response
     */
     public function outputAjaxFormResponse($message,$isError=false,$redirectUri=null){

        $this->autoRender = false;

        if(empty($redirectUri)){
            $message = __($message);
        }
        else {
            if(!empty($message)){
                $messageType = $isError ? 'error' : 'success';

                $this->setFlash($message,$messageType);
            }
        }

        echo json_encode(array(
            'isError' => $isError,
            'message' => __($message),
            'redirectUri' => $redirectUri
        ));
     }

    /**
     * Add a Vendor library to the php path
     */
    protected function addVendorLibToPHPPath($vendorLibrary){

        $vendorLibPath = APP . 'Vendor' . DS . $vendorLibrary;
        set_include_path(get_include_path() . PATH_SEPARATOR . $vendorLibPath);
    }

    /**
     * Load Datatables Component & Helper on the fly
     */
    protected function loadDataTablesComponent($settings=array()){

        $this->DataTables = $this->Components->load('DataTables.DataTables');
        $this->DataTables->initialize($this,$settings);
        $this->helpers[] = 'DataTables.DataTables';
    }

    /**
     * Raw debug function
     */
    protected function debug($o){

        die(print_r($o,true));
    }

    /**
     * Strings specific log msg
     */
    public function sLog($msg,$type=LOG_ERROR){

        $orgId = $this->Auth->User('organization_id');
        $orgId = empty($orgId) ? 'Unknown' : $orgId;
        $userId = $this->Auth->User('id');
        $userId = empty($userId) ? 'Unknown' : $userId;

        $logMsg = "OrgId:$orgId UserId:$userId $msg";

        $this->log($logMsg,$type);
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
