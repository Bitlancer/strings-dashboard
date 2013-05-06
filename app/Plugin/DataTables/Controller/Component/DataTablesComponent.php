<?php
App::uses('Component', 'Controller');
class DataTablesComponent extends Component
{

	/**
	 * Constructor
	 */
	function initialize(&$controller, $settings = array()) {

		//Set controller
        $this->controller=$controller;
        
        //Set model
        $model = $this->controller->modelClass;
        $this->model = $this->controller->$model;
        
        //Set request
        $this->request = $this->controller->request;
    }
	
	/**
	 * Get a DataTables request object
	 */
	public function getDataTable($columns,$additionalFindParameters=array(),$model=false){

		if($model === false)
			$model = $this->model;
		else
			$model = $model;

		//Set request based on method
        if($this->request->isPost())
            $request = $this->request->data;
        else
            $request = $this->request->query;

		$dataTablesRequest = new DataTablesRequest($columns,$request);

		$findParameters = array_merge_recursive($dataTablesRequest->getFindParameters(),$additionalFindParameters);

        //Get data
        $data = $model->find('all',$findParameters);

        //Get filtered count
        unset($findParameters['limit']);
        unset($findParameters['offset']);
        $filteredCount = $model->find('count',$findParameters);

        //Get unfiltered count
        $unfilteredCount = $model->find('count',$additionalFindParameters);

        return new DataTable($dataTablesRequest,$data,$unfilteredCount,$filteredCount);
	}
}

class DataTablesRequest
{
	public function __construct($columns,$request){
		
		$this->columns = $columns;
		$this->request = $request;
	}
	
	public function getColumns(){
		return $this->columns;
	}

	public function getIndexedColumns(){

		$columns = $this->columns;

		$index = 0;
        foreach($columns as $k => $v)
            $columns[$index++] = $v;

		return $columns;
	}
	
	public function getEcho(){
		return (isset($this->request['sEcho']) ? intval($this->request['sEcho']) : 1);
	}
	
	public function getLimit($defaultLimit=10){
		if(isset($this->request['iDisplayLength']) && $this->request['iDisplayLength'] != '-1')
			return $this->request['iDisplayLength'];
		else
			return $defaultLimit;
	}
	
	public function getOffset(){
		if(isset($this->request['iDisplayStart']))
			return $this->request['iDisplayStart'];
		else
			return 0;
	}
	
	public function getOrder(){

		$columns = $this->getIndexedColumns();
		
		$order = array();
        if(isset($this->request['iSortCol_0'])){
            for ($i=0;$i<intval($this->request['iSortingCols']);$i++){
                if($this->request['bSortable_'.intval($this->request['iSortCol_'.$i])] == "true"){
                    $orderColumn = $columns[intval($this->request['iSortCol_'.$i])];
                    $order[] = $orderColumn['model'].".".$orderColumn['column']." ".$this->request['sSortDir_'.$i];
                }
            }
        }
		
		return $order;
	}
	
	public function getFilter(){

		$columns = $this->getIndexedColumns();

        $filter = array();
        if(isset($this->request['sSearch']) && $this->request['sSearch'] != ""){
            for($i=0;$i<count($columns);$i++){
                if(isset($this->request['bSearchable_'.$i]) && $this->request['bSearchable_'.$i] == "true"){
                    $column = $columns[$i];
                    $filter[] = array($column['model'].".".$column['column']." LIKE" => "%" . $this->request['sSearch'] . "%");
                }
            }
        }
        if(!empty($filter))
        	return array('OR' => $filter);
        else
        	return array();
	}
	
	public function getFindParameters(){
	
		$findParameters = array(
			'conditions' => $this->getFilter(),
			'order' => $this->getOrder(),
			'offset' => $this->getOffset(),
			'limit' => $this->getLimit(),
		);

		return $findParameters;
	}
}

class DataTable
{
	public function __construct($request,$data,$unfilteredCount,$filteredCount){
		$this->request = $request;
		$this->data = $data;
		$this->unfilteredCount = $unfilteredCount;
		$this->filteredCount = $filteredCount;
	}

	public function getEcho(){
		return $this->request->getEcho();
	}

	public function getColumns(){
		return $this->request->getColumns();
	}
	
	public function getFlattenedData(){

		$columns = $this->request->getColumns();

		$flattenedResults = array();
		foreach($this->data as $row){
			$flattenedResult = array();
			foreach($columns as $column){
				if(!isset($row[$column['model']]) || !isset($row[$column['model']][$column['column']]))
					$flattenedResult[] = null;
				else
					$flattenedResult[] = $row[$column['model']][$column['column']];
			}
			$flattenedResults[] = $flattenedResult;
		}
		return $flattenedResults;
	}

	public function getData(){
		return $this->data;
	}

	public function setData($data){
		$this->data = $data;
	}

	public function getUnfilteredCount(){
		return $this->unfilteredCount;
	}

	public function getFilteredCount(){
		return $this->filteredCount;
	}
}
