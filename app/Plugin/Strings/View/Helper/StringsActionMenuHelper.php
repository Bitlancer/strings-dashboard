<?php

App::uses('StringsAppHelper', 'Strings.View/Helper');

class StringsActionMenuHelper extends StringsAppHelper {

   public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
        $this->view = $view;
    }

	/**
     * Generate a full action menu
     */
    public static function actionMenu($title,$items,$width=120){

        $src = self::create($title,$width);
        foreach($items as $item){
            if(!isset($item['type']))
                throw new \InvalidArgumentException('Key type is not defined');
            switch($item['type']){
                case 'modal':
                    $src .= self::modalItem($item['text'],$item['source'],$item['enabled']);
                    break;
                case 'link':
					if(isset($item['target']))
                    	$src .= self::linkItem($item['text'],$item['destination'],$item['enabled'],$item['target']);
					else
						$src .= self::linkItem($item['text'],$item['destination'],$item['enabled']); 
                    break;
                default:
                    throw new \InvalidArgumentException('Unrecognized action menu item type');
            }
        }

        $src .= self::close();
        return $src;
    }

    /**
     * Generate an action menu
     */
    public static function create($title,$width=120){

        $src = "<ul class=\"action-menu\" data-width=\"$width\">";
        $src .= "<li>$title</li>";
        $src .= "<span>";

        return $src;
    }

    public static function close(){

        $src = "</span>";
        $src .= "</ul>";

        return $src;
    }

    public static function modalItem($text,$source,$enabled=true){

        $class ="modal";
        if(!$enabled)
            $class = "disabled";

        $src = "<a class=\"$class\" data-src=\"$source\" data-title=\"$text\">$text</a>";
   
        return $src;
    }

    public static function linkItem($text,$destination,$target='_self'){
   
        $src = "<a href=\"$destination\" target=\"$target\">$text</a>";
        return $src;
    }
}
