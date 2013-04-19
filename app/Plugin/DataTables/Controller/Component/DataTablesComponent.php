<?php
App::uses('Component', 'Controller');
class DataTablesComponent extends Component
{

	/**
	 * Constructor
	 */
	function initialize(&$controller, $settings = array()) {
        $this->controller=$controller;

		$model = $this->controller->modelClass;
		$this->model = $this->controller->$model;
    }
	
	/**
	 * Output datatables JSON
	 */
	public function output($method,$columns,$addFindOptions=array()){

		//Add numerical indexes to columns array
		$index = 0;
		foreach($columns as $k => $v)
			$columns[$index++] = $v;

		//Set request based on method
		if($method == 'GET')
			$request = $this->controller->request->query;
		else
			$request = $this->controller->request->data;
		
		
		$findParameters = array();
		
		//Limit & offset
		if(isset($request['iDisplayStart']) && $request['iDisplayLength'] != '-1'){
			$findParameters['offset'] = $request['iDisplayStart'];
			$findParameters['limit'] = $request['iDisplayLength'];
		}
		else {
			$findParameters['offset'] = 0;
			$findParameters['limit'] = 10;	
		}
		
		//Order
		$findParameters['order'] = array();
		if(isset($request['iSortCol_0'])){

			for ($i=0;$i<intval($request['iSortingCols']);$i++){

				if($request['bSortable_'.intval($request['iSortCol_'.$i])] == "true"){

					$orderColumn = $columns[intval($request['iSortCol_'.$i])];
					$findParameters['order'][] = $orderColumn['model'].".".$orderColumn['column']." ".$request['sSortDir_'.$i];
				}
			}
		}

		//Filter
		$filter = array();
		if(isset($request['sSearch']) && $request['sSearch'] != ""){
			for($i=0;$i<count($columns);$i++){
				if(isset($request['bSearchable_'.$i]) && $request['bSearchable_'.$i] == "true"){
					$column = $columns[$i];
					$filter[] = array($column['model'].".".$column['column']." LIKE" => "%" . $request['sSearch'] . "%");
				}
			}
		}
		$findParameters['conditions'] = array('OR' => $filter);

		//Merge in additional find options supplied by the user
		$findParameters = array_merge_recursive($findParameters,$addFindOptions);
		
		//Find results w/ limit and filter
		$results = $this->model->find('all',$findParameters);

		//Find result count w/o limit or filter
		$unfilteredResultsCount = $this->model->find('count',$addFindOptions);

		//Find result count w/ filter but w/o limit
		unset($findParameters['limit']);
		unset($findParameters['offset']);
		$filteredResultsCount = $this->model->find('count',$findParameters);
		
		$trimmedResults = array();
		foreach($results as $row){
			$trimmedRow = array();
			foreach($columns as $column){
				$columnModel = $column['model'];
				$columnName = $column['column'];
				$trimmedRow[] = $row[$columnModel][$columnName];
			}
			
			$trimmedResults[] = $trimmedRow;
		}
		
		return json_encode(array(
			'sEcho' => (isset($request['sEcho']) ? intval($request['sEcho']) : 1),
			'iTotalRecords' => $unfilteredResultsCount,
			'iTotalDisplayRecords' => $filteredResultsCount,
			'aaData' => $trimmedResults
		));
	}
}
