<?php

App::uses('AppHelper', 'View/Helper');

class StringsHelper extends AppHelper 
{

	/**
	 * Generate an action menu
	 *
	 *
	 */
	public static function createActionMenu($menuWidth,$menuTitle){

		$src = "<ul class=\"action-menu\" data-width=\"$menuWidth\">";
		$src .= "<li>$menuTitle</li>";
		$src .= "<span>";

		return $src;
	}

	public static function closeActionMenu(){

		$src = "</span>";
        $src .= "</ul>";

		return $src;
	}

	public static function actionMenuItem($text,$source,$enabled=true){

		$class ="modal";
		if(!$enabled)
			$class = "disabled";

		$src = "<a class=\"$class\" data-src=\"$source\" data-title=\"$text\">$text</a>";
	
		return $src;
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
	 * @param string[] $ctaClasses Call-to-action css classes
	 * @param string $ctaSrc Call-to-action data-src attribute value
	 * @param string HTML table
	 */
	public static function buildStringsDatatable($tableElementID,$tableTitle,$tableColumns,$tableData,$tableDataLength,$ctaTxt,$ctaTitle,$ctaSrc,$ctaEnabled){

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
	
		return self::buildTable($tableColumns,$tableValues,$tableAttributes);
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
	 public static function buildTable ($tableColumns,$tableValues,$tableAttributes=array(),$thAttributes=array(),$tdAttributes=array()){
		
		$tdAltClass="alt";
		
		$tableAttributes = self::toKeyValueString($tableAttributes);
		$thAttributes = self::toKeyValueString($thAttributes);
		
		//Extract css so we can set alt on every other row
		$tdClass = "";
		if(isset($tdAttributes['class'])){
			$tdClass .= " " . $tdAttributes['class'];
			unset($tdAttributes['class']);
		}
		$tdAttributes = self::toKeyValueString($tdAttributes);
		
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
	 
	 /**
	  * Converts element attributes stored in an associative array into a string
	  *
	  * Wrapper for toKeyValueString that's specific to HTML attributes
	  *
	  * @param mixed[] $attributes Associative array containing element attributes
	  * @param string $quote Attribute values will be wrapped in this quote
	  * @return string The attributes string
	  */
	 public static function buildElementAttributes($attributes,$quote="\""){
		return self::toKeyValueString($attributes,'=',' ',$quote);
	 }
	 
	 /**
	  * Converts associate array into a key-value delimited string
	  * 
	  * @param mixed[] $fields Key-value array
	  * @param string $keyValueDelimiter The delimiter that separates key and value
	  * @param string $fieldDelimiter The delimiter that separates pairs of key-values
	  * @param string $escapeValue Each value will be wrapped in this value
	  * @return string The key-value string
	  */
	 public static function toKeyValueString($fields,$keyValueDelimiter='=',$fieldDelimiter=' ',$escapeValue="\""){
		
		$keyValueArray = array();
		foreach($fields as $k => $v){
			$keyValueArray[] = $k.$keyValueDelimiter.$escapeValue.$v.$escapeValue;
		}
		
		return implode($fieldDelimiter,$keyValueArray);
	 }
}
