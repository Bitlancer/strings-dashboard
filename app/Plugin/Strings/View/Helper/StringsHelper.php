<?php

App::uses('StringsAppHelper', 'Strings.View/Helper');

class StringsHelper extends StringsAppHelper {

   public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
        $this->view = $view;
    }

	public function modalLink($text,$source,$width=360,$disabled=false,$addClasses=array()){

        $class ="modal";
        if($disabled)
            $class = "disabled";

		$class .= " " . implode(' ',$addClasses);

		$modalAttrs = array(
			'class' => $class,
			'data-src' => $source,
			'data-title' => $text,
			'data-width' => $width
		);

        $src = "<a " . self::buildElementAttributes($modalAttrs) . ">$text</a>";
   
        return $src;
    }

	public function link($text,$destination,$target="_parent",$disabled=false){

        if($disabled){
            $attributes = array(
                'class' => 'disabled'
            );
        }
        else {
            $attributes = array(
                'href' => $destination,
                'target' => $target
            );
        }

        $src ="<a " . self::buildElementAttributes($attributes) . ">$text</a>";
        return $src;
    }
}
