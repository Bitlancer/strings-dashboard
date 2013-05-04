<?php

App::uses('AppHelper', 'View/Helper');

class StringsAppHelper extends AppHelper
{

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
