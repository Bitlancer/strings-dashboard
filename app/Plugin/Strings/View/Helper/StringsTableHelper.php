<?php

App::uses('StringsAppHelper', 'Strings.View/Helper');

class StringsTableHelper extends StringsAppHelper {

   public $helpers = array('Strings.Strings');

   public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
        $this->view = $view;
    }

	public function infoTable($fields,$emptyTableMessage='No information available'){

        //Convert key=>value $fields into array(label,value)
        $tableValues = array();
        foreach($fields as $key => $value){
            if(!is_numeric($value) && empty($value))
                $value = "<span class=\"null\"></span>";
            $tableValues[] = array("<strong>$key</strong>",$value);
        }

        return $this->table(array(),$tableValues,array('class' => 'details'),array(),array(),$emptyTableMessage);
    }

    public function cleanTable($tableColumns,$tableValues,$emptyTableMessage='No data available'){
        return $this->table($tableColumns,$tableValues,array('class' => 'clean'),array(),array(),$emptyTableMessage);
    }

    /**
     * Generates a Strings specific Datatable w/ a CTA
     *
     * @param string $tableElementID Table HTML element id
     * @param string $tableTitle Tabel title
     * @param string[] $tableColumns Table column headings
     * @param mixed $tableData If string, data-src attribute value else two dimensional array containing table data
     * @param string $ctaTxt Call-to-action button text
     * @param string $ctaTitle Call-to-action data-title attribute
     * @param strings $ctaWidth Call-to-action data-width attribute - used for modal width
     * @param string[] $ctaClasses Call-to-action css classes
     * @param string $ctaSrc Call-to-action data-src attribute value
     * @return string HTML table
     */
    public function datatableWithCta($tableElementID,$tableColumns,$tableData,$dataTableOptions,$ctaModal,$ctaSrc,$ctaText,$ctaDisabled,$ctaClasses,$ctaTitle='Create',$ctaWidth='360'){

        //Default CTA classes
        $ctaClasses = array_merge($ctaClasses,array(
            'primary',
            'cta'
        ));

        if($ctaDisabled)
            $ctaClasses[] = 'disabled';
        elseif($ctaModal)
            $ctaClasses[] = 'modal';
        else {}

        //Expand ctaClasses into css list
        $ctaClasses = implode(' ',$ctaClasses);

        //Build cta element
        if($ctaModal){
            $ctaAttributes = array(
                'data-src' => $ctaSrc,
                'data-title' => $ctaTitle,
                'data-width' => $ctaWidth,
                'class' => $ctaClasses
            );
        }
        else {
            $ctaAttributes = array(
                'class' => $ctaClasses
            );
            if($ctaSrc !== false && !$ctaDisabled)
                $ctaAttributes['href'] = $ctaSrc;

        }
        $ctaElement = "<a " . self::buildElementAttributes($ctaAttributes,"'") . ">$ctaText</a>"; 
        $dataTableOptions['data-cta'] = $ctaElement;

        return $this->datatable($tableElementID,$tableColumns,$tableData,$dataTableOptions);
    }

	/**
     * Generates a Strings specific Datatable
     *
     * @param string $tableElementID Table HTML element id
     * @param string $tableTitle Tabel title
     * @param string[] $tableColumns Table column headings
     * @param mixed $tableData If string, data-src attribute value else two dimensional array containing table data
     * @param string $ctaElement Call-to-action a element
     * @return string HTML table
     */
    public function datatable($tableElementID,$tableColumns,$tableData,$dataTableOptions){

        //Determine data source
        $loadDataViaAjax = false;
        $tableDataSrc = "";
        $tableValues = array();
        if(is_array($tableData)){
            $tableValues = $tableData;
        }
        else {
            $loadDataViaAjax = true;
            $tableDataSrc = $tableData;
        }

        $tableAttributes = array(
            'data-type' => 'datatable',
            'id' => $tableElementID,
            'data-src' => $tableDataSrc,
            'data-length' => 15,
            'data-empty-table' => 'No data available',
            'data-processing' => 'false',
            'data-title' => 'false',
            'data-raw-title' => 'false',
            'data-search' => 'false',
            'data-processing' => 'false',
            'data-refresh' => 'false'
        );

        $tableAttributes = array_merge($tableAttributes,$dataTableOptions);

        if($loadDataViaAjax){
            $src = "<table " . self::buildElementAttributes($tableAttributes,"\"") . ">";
            $src .= "<thead><tr>";
            foreach($tableColumns as $column){
                $src .= "<th>$column</th>";
            }
            $src .= "</tr></thead>";
            $src .= "<tbody>";
            $src .= "<tr><td colspan=\"100\" class=\"blank\">";
            if($tableAttributes['data-processing'] != 'true')
                $src .= "<img class=\"loading\" src=\"/img/loading.gif\" />";
            $src .= "</td></tr>";
            $src .= "</tbody>";
            $src .= "</table>";
            return $src;
        }
        else
            return $this->table($tableColumns,$tableValues,$tableAttributes);
    }

	/**
      * Generate an HTML table
      *
      * @param string[] $tableColumns List of columns for thead
      * @param string[][] $tableValues Two-dimensional array containing table data
      * @param string[] $tableAttributes Key-value array of attributes to add to table element
      * @param string[] $thAttributes Key-value array of attributes to add to each th element
      * @param string[] $tdAttributes Key-value array of attributes to add to each td element
      * @return string The HTML source for the table
      */
     public function table($tableColumns,$tableValues,$tableAttributes=array(),$thAttributes=array(),$tdAttributes=array(),$emptyTableMessage='No data available'){

        $tdAltClass="alt";

        $tableAttributes = $this->toKeyValueString($tableAttributes);
        $thAttributes = $this->toKeyValueString($thAttributes);

        //Extract css so we can set alt on every other row
        $tdClass = "";
        if(isset($tdAttributes['class'])){
            $tdClass .= " " . $tdAttributes['class'];
            unset($tdAttributes['class']);
        }
        $tdAttributes = $this->toKeyValueString($tdAttributes);

        $tableSrc = "<table $tableAttributes>";
        $tableSrc .= "<thead>";
        $tableSrc .= "<tr>";
        foreach($tableColumns as $column)
            $tableSrc .= "<th $thAttributes>$column</th>";
        $tableSrc .= "</tr>";
        $tableSrc .= "</thead>";
        $tableSrc .= "<tbody>";
		if(empty($tableValues))
			$tableSrc .= "<tr><td colspan=\"100\" class=\"blank\">$emptyTableMessage</td></tr>";
        foreach($tableValues as $row){
            $tableSrc .= "<tr>";
            $altIndex = 0;
            foreach($row as $value){
                $class = $tdClass.($altIndex%2==0 ? "" : $tdAltClass);
                $tableSrc .= "<td $tdAttributes $class>$value</td>";
            }
            $tableSrc .="</tr>";
        }
        $tableSrc .= "</tbody>";
        $tableSrc .= "</table>";

        return $tableSrc;
     }	
}
