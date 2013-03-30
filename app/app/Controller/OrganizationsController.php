<?php

class OrganizationsController extends AppController
{

	public function create(){

		if($this->request->is('post')){
			if($this->Organization->save($this->request->data)){
				$this->Session->setFlash('Saved organization successfully');
			}
			else {
				debug($this->Organization->validationErrors);
			}
		}
	}

	public function edit(){

		if($this->request->is('post')){	

			$this->Organization->id = 3;

			$validFields = array('name','short_name');

			if($this->Organization->save($this->request->data,true,$validFields)){
				$this->Session->setFlash('Updated organization successfully');
			}
			else {
				//$this->Session->setFlash(implode(". ",$this->Organization->validationErrors));
				debug($this->Organization->validationErrors);
			}
		}
	}
}
