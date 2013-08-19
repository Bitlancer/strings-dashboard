<?php

App::uses('StringsAppHelper', 'Strings.View/Helper');

class StringsHelper extends StringsAppHelper {

   public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
        $this->view = $view;
    }

	public function flattenData($rawRecord,$fields){

		$flattenedRecords = array();

		foreach($rawRecords as $rawRecord){

			$flattenedRecord = array();
			foreach($fields as $fieldName => $field){
				$flattenedRecord[$fieldName] = $rawRecord[$field['model']][$field['column']];	
			}
			$flattenedRecords[] = $flattenedRecord;
		}

		return $flattenedRecords;
	}

	public function oldModalLink($text,$source,$disabled=false,$title=false,$width=360,$additionalClasses=array()){

        $class ="modal";
        if($disabled)
            $class = "disabled";
        if(!empty($additionalClasses))
            $class .= " " . implode(' ',$additionalClasses);

		if($title === false)
			$title = $text;

		$modalAttrs = array(
			'class' => $class,
			'data-src' => $source,
			'data-title' => $title,
			'data-width' => $width
		);

        $src = "<a " . self::buildElementAttributes($modalAttrs,"'") . ">$text</a>";
   
        return $src;
    }

    public function modalLink($text,$source,$modalOptions,$additionalClasses=array()){

        //Set and merge default modal options
        $defaultModalOptions = array(
            'disabled' => false,
            'title' => $text,
            'width' => 360,
            'reloadOnClose' => false,
        );
        $modalOptions = array_merge($defaultModalOptions,$modalOptions);

        //Set classes on element
        $classes = array();
        $classes[] = $modalOptions['disabled'] ? 'disabled' : 'modal';
        if($modalOptions['reloadOnClose'])
            $classes[] = "reload";
        if(!empty($additionalClasses))
            $classes = array_unique(array_merge($classes,$additionalClasses));

        //Set element attributes
        $modalAttrs = array(
            'class' => implode(" ",$classes),
            'data-src' => $source,
            'data-title' => $modalOptions['title'],
            'data-width' => $modalOptions['width']
        );

        $src = "<a " . self::buildElementAttributes($modalAttrs,"'") . ">$text</a>"; 
        return $src;
    }


	public function link($text,$destination,$disabled=false,$target='_parent'){

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

        $src ="<a " . self::buildElementAttributes($attributes,"'") . ">$text</a>";
        return $src;
    }
}
