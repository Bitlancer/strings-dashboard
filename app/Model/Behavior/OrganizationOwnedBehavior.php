<?php

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

	public function setOwningOrganization(Model $Model,$organizationId){
		
		//$this->settings[$Model->alias]['owningOrganization'] = $organizationId;
		$this->settings['owningOrganization'] = $organizationId;
	}

	public function beforeFind(Model $Model,$query){

		if(!isset($query['conditions']))
			$query['conditions'] = array();

		$query['conditions']['organization_id'] = $this->settings['owningOrganization'];

		return $query;
	}

	public function beforeSave(Model $Model){
		return true;
	}

}
