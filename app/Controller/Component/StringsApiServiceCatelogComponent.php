<?php

/**
 * This Component is meant to encapsulate the logic for
 * selecting an appropriate Strings API endpoint for
 * dealing with the specified service.
 */

App::uses('Component', 'Controller');

class StringsApiServiceCatelogComponent extends Component {

    public function getUrl($service, $endpoint = false){

        return $this->getApiUrl($service, $endpoint);
    }

    public function getApiUrl($service, $endpoint=false){

        $api_url = false;

        switch($service){
            default:
                $api_url = "http://localhost:8080";
        }

        if($endpoint !== false)
            return $api_url . $endpoint;
        else
            return $api_url;
    }
}
