<?php

App::uses('AppHelper', 'View/Helper');

class DataTablesHelper extends AppHelper {

    public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
        $this->view = $view;
    }

    public function getColumnHeadings($view='default'){
        return array_keys($this->settings[$view]['dataTable']['columns']);
    }

    public function render($recordCallback=false,$view='default'){

        $outputData = $this->flattenData($view);
        if(is_callable($recordCallback)){

            $outputDataLen = count($outputData);
            $rawData = $this->settings[$view]['dataTable']['data'];
    
            $newOutputData = array();
            for($x=0;$x<$outputDataLen;$x++)
                $newOutputData[$x] = call_user_func($recordCallback,$this->view,$outputData[$x],$rawData[$x]);
            
            $outputData = $newOutputData;
        }

        return json_encode(array(
            'sEcho' => $this->settings[$view]['dataTable']['echo'],
            'iTotalRecords' => $this->settings[$view]['dataTable']['unfilteredCount'],
            'iTotalDisplayRecords' => $this->settings[$view]['dataTable']['filteredCount'],
            'aaData' => $outputData
        ));
    }

    protected function flattenData($view='default'){

        $data = $this->settings[$view]['dataTable']['data'];
        $columns = $this->settings[$view]['dataTable']['columns'];

        $flattenedResults = array();
        foreach($data as $row){
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
}
