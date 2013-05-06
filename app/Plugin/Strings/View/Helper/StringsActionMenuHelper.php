<?php

App::uses('StringsAppHelper', 'Strings.View/Helper');

class StringsActionMenuHelper extends StringsAppHelper {

   public $helpers = array('Strings.Strings');

   public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
        $this->view = $view;
    }

	/**
     * Generate a full action menu
     */
    public function actionMenu($title,$items,$width=120){

        $src = $this->create($title,$width);
        foreach($items as $item){
            if(!isset($item['type']))
                throw new \InvalidArgumentException('Key type is not defined');
            switch($item['type']){
                case 'modal':
                    $src .= $this->modalItem($item['text'],$item['source'],$item['enabled']);
                    break;
                case 'link':
					if(isset($item['target']))
                    	$src .= $this->linkItem($item['text'],$item['destination'],$item['enabled'],$item['target']);
					else
						$src .= $this->linkItem($item['text'],$item['destination'],$item['enabled']); 
                    break;
                default:
                    throw new \InvalidArgumentException('Unrecognized action menu item type');
            }
        }

        $src .= $this->close();
        return $src;
    }

    /**
     * Generate an action menu
     */
    public function create($title,$width=120){

        $src = "<ul class=\"action-menu\" data-width=\"$width\">";
        $src .= "<li>$title</li>";
        $src .= "<span>";

        return $src;
    }

    public function close(){

        $src = "</span>";
        $src .= "</ul>";

        return $src;
    }

    public function modalItem($text,$source,$enabled=true){
		return $this->Strings->modalLink($text,$source,$enabled);
    }

    public function linkItem($text,$destination,$target='_self'){
   
        $src = "<a href=\"$destination\" target=\"$target\">$text</a>";
        return $src;
    }
}
