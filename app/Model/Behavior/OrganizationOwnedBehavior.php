<?php

App::uses('CakeSession', 'Model/Datasource');

class OrganizationOwnedBehavior extends ModelBehavior
{
	public function setup(Model $Model, $settings = array()){

    	if(!isset($this->settings[$Model->alias])){
        	$this->settings[$Model->alias] = array();
    	}

    	$this->settings[$Model->alias] = array_merge(
        	$this->settings[$Model->alias], (array) $settings
		);
	}

	/*
	public function setOwningOrganization(Model $Model,$organizationId){
		
		//$this->settings[$Model->alias]['owningOrganization'] = $organizationId;
		$this->settings['owningOrganization'] = $organizationId;
	}
	*/

	public function getOrganizationId(){

		return CakeSession::read('Auth.User.organization_id');
	}

	public function beforeFind(Model $Model,$query){

		if(!isset($query['conditions'])){
			$query['conditions'] = array(
				($Model->alias . '.organization_id') => $this->getOrganizationId()
			);
		}
		else {
			$conditions = $query['conditions'];
			if(!isset($conditions['orgaization_id']) && 
				!isset($conditions[$Model->alias . ".organization_id"])){

				$query['conditions'] = array(
					'AND' => array(
						($Model->alias . '.organization_id') => $this->getOrganizationId(),
						$conditions
					)
				);
			}
		}

		return $query;
	}

	public function beforeValidate(Model $Model){

        $orgId = $this->getOrganizationId();
        if(empty($orgId)){
            $Model->invalidate('organization_id','Organization Id is required');
            return false;
        }

        if(!isset($Model->data[$Model->alias]['organization_id']) || !isset($Model->data['organization_id'])){
            $this->_addToWhitelist($Model,'organization_id');
            $Model->data[$Model->alias]['organization_id'] = $orgId;
        }

		return true;
	}
}
