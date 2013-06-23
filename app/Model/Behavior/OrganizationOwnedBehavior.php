<?php

App::uses('CakeSession', 'Model/Datasource');

class OrganizationOwnedBehavior extends ModelBehavior
{
	public function setup(Model $model, $settings = array()){

    	if(!isset($this->settings[$model->alias])){
        	$this->settings[$model->alias] = array();
    	}

    	$this->settings[$model->alias] = array_merge(
        	$this->settings[$model->alias], (array) $settings
		);
	}

	public function getOrganizationId(){

		return CakeSession::read('Auth.User.organization_id');
	}

	public function beforeFind(Model $model,$query){

        $organizationId = $this->getOrganizationId();
        if(empty($organizationId))
            return false;

		if(!isset($query['conditions'])){
			$query['conditions'] = array(
				($model->alias . '.organization_id') => $organizationId
			);
		}
		else {
			$conditions = $query['conditions'];

            //Attempt to overwrite organization_id if it has been supplied by the user
            if(isset($conditions['organization_id']))
                unset($conditions['organization_id']);

            if(isset($conditions["$model->alias.organization_id"]))
                unset($conditions["$model->alias.organization_id"]);

				$query['conditions'] = array(
					'AND' => array(
						($model->alias . '.organization_id') => $organizationId,
						$conditions
					)
				);
		}

		return $query;
	}

    public function beforeDelete(Model $model,$cascade=true){

        $organizationId = $this->getOrganizationId();
        if(empty($organizationId)){
            $model->invalidate('organization_id','Unable to determine organization id');
            return false;
        }

        $object = $model->find('first',array(
            'recursive' => -1,
            'conditions' => array(
                'id' => $model->id,
                'organization_id' => $organizationId
            )
        ));

        if(empty($object)){
            $model->invalidate('organization_id','Record does not exist.');
            return false;
        }
        else {
            return true;
        }
    }

    public function beforeSave(Model $model){

        $organizationId = $this->getOrganizationId();
        if(empty($organizationId)){
            $model->invalidate('organization_id','Unable to determine organization id');
            return false;
        }

       $this->setOrganizationIdOnModel($model,$organizationId);
        return true;
    }

	public function beforeValidate(Model $model){

        $organizationId = $this->getOrganizationId();
        if(empty($organizationId)){
            $model->invalidate('organization_id','Unable to determine organization id');
            return false;
        }

        $this->_addToWhitelist($model,'organization_id');
        $this->setOrganizationIdOnModel($model,$organizationId);
		return true;
	}

    /**
     * Set and potentially overwrite an organization_id on a model
     */
    private function setOrganizationIdOnModel(&$model,$organizationId){

        if(isset($model->data['organization_id']))
            unset($model->data['organization_id']);

        $model->data[$model->alias]['organization_id'] = $organizationId;
    }
}
