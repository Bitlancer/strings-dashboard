<?php

class ImplementationsController extends AppController
{

	public function index(){

		$this->autoRender = false;

		$result = $this->Implementation->find('count',array(
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => 'provider',
					'alias' => 'Provider',
					'type' => 'inner',
					'foreignKey' => false,
					'conditions' => array('Provider.id = Implementation.provider_id') 	
				),
				array(
					'table' => 'service_provider',
					'type' => 'inner',
					'foreignKey' => false,
					'conditions' => array('Provider.id = service_provider.provider_id')
				),
				array(
					'table' => 'service',
					'alias' => 'Service',
					'type' => 'inner',
					'foreignKey' => false,
					'conditions' => array('service_provider.service_id = Service.id')
				)
			),
			'conditions' => array(
                'Implementation.organization_id' => $this->Auth->user('organization_id'),
				'Service.name' => 'infrastructure'
			)
		));

		echo print_r($result,true);

	}

}
