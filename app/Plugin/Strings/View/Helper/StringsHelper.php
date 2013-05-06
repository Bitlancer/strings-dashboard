<?php

App::uses('StringsAppHelper', 'Strings.View/Helper');

class StringsHelper extends StringsAppHelper {

   public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
        $this->view = $view;
    }

	public function modalLink($text,$source,$enabled=true,$addClasses=array()){

        $class ="modal";
        if(!$enabled)
            $class = "disabled";

		$class .= implode(' ',$addClasses);

		$modalAttrs = array(
			'class' => $class,
			'data-src' => $source,
			'data-title' => $text
		);

        $src = "<a " . self::buildElementAttributes($modalAttrs) . ">$text</a>";
   
        return $src;
    }
}
