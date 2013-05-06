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
					$item = array_merge(array('width' => 360,'disabled' => false),$item);
                    $src .= $this->Strings->modalLink($item['text'],$item['source'],$item['width'],$item['disabled']);
                    break;
                case 'link':
					$item = array_merge(array('target' => '_parent','disabled'=> false),$item);
                   	$src .= $this->Strings->link($item['text'],$item['destination'],$item['target'],$item['disabled']);
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
}
