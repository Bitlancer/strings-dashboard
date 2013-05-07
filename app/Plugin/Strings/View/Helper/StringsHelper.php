<?php

App::uses('StringsAppHelper', 'Strings.View/Helper');

class StringsHelper extends StringsAppHelper {

   public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
        $this->view = $view;
    }

	public function modalLink($text,$source,$disabled=false,$title=false,$width=360){

        $class ="modal";
        if($disabled)
            $class = "disabled";

		if($title === false)
			$title = $text;

		$modalAttrs = array(
			'class' => $class,
			'data-src' => $source,
			'data-title' => $title,
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
