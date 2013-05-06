<?php

App::uses('StringsAppHelper', 'Strings.View/Helper');

class StringsTableHelper extends StringsAppHelper {

   public $helpers = array('Strings.Strings');

   public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
        $this->view = $view;
    }

	/**
     * Generates a Strings specific Datatable
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
     * @param string HTML table
     */
    public function datatable($tableElementID,$tableTitle,$tableColumns,$tableData,$tableDataLength,$ctaTxt,$ctaTitle,$ctaSrc,$ctaWidth,$ctaEnabled){

        //Default CTA classes
        $ctaClasses = array(
            'primary',
            'cta'
        );

        if($ctaEnabled)
            $ctaClasses[] = 'modal';
        else
            $ctaClasses[] = 'disabled';

        //Determine data source
        $tableDataSrc = "";
        $tableValues = array();
        if(is_array($tableData)){
            $tableValues = $tableData;
        }
        else {
            $tableDataSrc = $tableData;
        }

        //Expand ctaClasses into css list
        $ctaClasses = implode(' ',$ctaClasses);

        //Build cta element
        $ctaAttributes = array(
            'data-src' => $ctaSrc,
            'data-title' => $ctaTitle,
			'data-width' => $ctaWidth,
            'class' => $ctaClasses
        );
        $ctaElement = "<a " . self::buildElementAttributes($ctaAttributes,"'") . ">$ctaTxt</a>";

        $tableAttributes = array(
            'data-type' => 'datatable',
            'id' => $tableElementID,
            'data-title' => $tableTitle,
            'data-src' => $tableDataSrc,
            'data-length' => $tableDataLength,
            'data-cta' => $ctaElement
        );

        return $this->table($tableColumns,$tableValues,$tableAttributes);
    }

	public function infoTable($fields){

		//Convert key=>value $fields into array(label,value)
		$tableValues = array();
		foreach($fields as $key => $value){
			$tableValues[] = array("<strong>$key</strong>",$value);
		}

		return $this->table(array(),$tableValues,array('class' => 'details'));
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
     public function table($tableColumns,$tableValues,$tableAttributes=array(),$thAttributes=array(),$tdAttributes=array()){

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
