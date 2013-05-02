<?php

App::uses('AppHelper', 'View/Helper');

class DataTablesHelper extends AppHelper {

   public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
		$this->view = $view;
    }

    public function output($dataTable,$dataCallback=false){

		$outputData = $dataTable->getFlattenedData();
		if(is_callable($dataCallback)){

			$outputDataLen = count($outputData);
			$rawData = $dataTable->getData();
	
			$newOutputData = array();
			for($x=0;$x<$outputDataLen;$x++)
				$newOutputData[$x] = call_user_func($dataCallback,$this->view,$outputData[$x],$rawData[$x]);
			
			$outputData = $newOutputData;
		}
		
		
		return json_encode(array(
			'sEcho' => $dataTable->getEcho(),
			'iTotalRecords' => $dataTable->getUnfilteredCount(),
			'iTotalDisplayRecords' => $dataTable->getFilteredCount(),
			'aaData' => $outputData
		));	
	}
}
