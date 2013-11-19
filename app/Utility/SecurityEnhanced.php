<?php

App::uses('Security', 'Utility');

class SecurityEnhanced extends Security {

	public static function hash($string, $type = null, $salt = false, $format=false) {

        $hash = parent::hash($string, $type, $salt);
        if($format === false or $format == 'hex'){
            //Its already encoded as hex 
        }
        elseif($format == "base64"){
            $hash = base64_encode(pack('H*',$hash));
        }
        else
            throw Exception('Unknown format');

        return $hash;
	}
}
