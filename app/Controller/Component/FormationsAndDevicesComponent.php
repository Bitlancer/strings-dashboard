<?php

App::uses('Component', 'Controller');

class FormationsAndDevicesComponent extends Component {

    /*
     * The controller that is utilizing this component
     */
    public $controller;

    public function initialize($controller){

        $this->controller = $controller;
    }

    public function _parseAndValidateLoadBalancer($deviceModel,$deviceInput,$implementationId){

        $this->controller->loadModel('Implementation');

        $errors = array();

        $implementation = $this->controller->Implementation->findById($implementationId);
        $providerId = $implementation['Implementation']['provider_id'];
        $attrs = $this->controller->Implementation->Provider->getLoadbalancerAttributes($providerId);

        //Virtual IP type
        if(!isset($deviceInput['virtualIpType']) || empty($deviceInput['virtualIpType']))
            $errors[] = 'Virtual IP is required.';
        else {
            $virtualIpTypes = json_decode($attrs['load_balancers.virtual_ip_types']);
            $virtualIpType = $deviceInput['virtualIpType'];
            if(!in_array($virtualIpType,$virtualIpTypes))
                $errors[] = 'Invalid virtual ip.';
            else {
                $deviceModel['DeviceAttribute'][] = array(
                    'var' => 'implementation.virtual_ip_type',
                    'val' => $virtualIpType
                );
            }
        }

        //Protocol
        if(!isset($deviceInput['protocol']) || empty($deviceInput['protocol']))
            $errors[] = 'Protocol is required.';
        else {
            $protocols = json_decode($attrs['load_balancers.protocols']);
            $protocols = Hash::extract($protocols,'{n}.name');
            $protocol = $deviceInput['protocol'];
            if(!in_array($protocol,$protocols)){
                $errors[] = 'Invalid protocol.';
            }
            else {
                $deviceModel['DeviceAttribute'][] = array(
                    'var' => 'implementation.protocol',
                    'val' => $protocol
                );
            }
        }

        //Port
        if(!isset($deviceInput['port']) || empty($deviceInput['port']))
            $errors[] = 'Port is required.';
        else {
            $port = $deviceInput['port'];

            if(!is_numeric($port) || $port < 1 || $port > 65535){
                $errors[] = 'Invalid port. Port must be an integer between 1 and 65535.'; 
            }
            else {
                $deviceModel['DeviceAttribute'][] = array(
                    'var' => 'implementation.port',
                    'val' => $port
                );
            }
        }
        
        //Algorithm
        if(!isset($deviceInput['algorithm']) || empty($deviceInput['algorithm']))
            $errors[] = 'Algorithm is required.';
        else {
            $algorithms = json_decode($attrs['load_balancers.algorithms']);
            $algorithms = Hash::extract($algorithms,'{n}.name');
            $algorithm = $deviceInput['algorithm'];
            if(!in_array($algorithm,$algorithms)){
                $errors[] = 'Invalid algorithm.';
            }
            else {
                $deviceModel['DeviceAttribute'][] = array(
                    'var' => 'implementation.algorithm',
                    'val' => $algorithm
                );
            }
        }

        return array($deviceModel,$errors);
    }

    public function _parseAndValidateInstance($deviceModel,$deviceInput,$instanceVarDefs=array()){

        $this->controller->loadModel('Implementation');

        $deviceErrors = array();
        $hieraVariables = array();

        $implementationId = $deviceModel['Device']['implementation_id'];

        static $defaultImageId = array();
        if(empty($deviceImageId))
            $defaultImageId = $this->controller->Implementation->getDefaultImageId($implementationId);

        static $flavorIds = array();
        if(empty($flavorIds)){
            $flavorIds = array_keys($this->_getProviderFlavors($implementationId));
        }

        //Flavor
        if(!isset($deviceInput['flavor']) || empty($deviceInput['flavor']))
            $deviceErrors['infrastructure'][] = 'Flavor is required.';
        elseif(!in_array($deviceInput['flavor'],$flavorIds))
            $deviceErrors['infrastructure'][] = 'Invalid flavor.';
        else {
            $deviceModel['DeviceAttribute'][] = array(
                'var' => 'implementation.flavor_id',
                'val' => $deviceInput['flavor']
            );
        }

        //Image id
        $deviceModel['DeviceAttribute'][] = array(
            'var' => 'implementation.image_id',
            'val' => $defaultImageId
        );

        //Validate instance variables
        if(!empty($instanceVarDefs)){

            $variablesInput = isset($deviceInput['variables']) ? $deviceInput['variables'] : array();

            list($hieraVariables,$systemConfigErrors) =
                $this->_parseAndValidateInstanceVariables($deviceModel,$variablesInput,$instanceVarDefs);

            if(!empty($systemConfigErrors))
                $deviceErrors['system'] = $systemConfigErrors;
        }

        return array($deviceModel,$hieraVariables,$deviceErrors);
    }

    public function _parseAndValidateInstanceVariables($device,$input,$instanceVarDefs){

        $this->controller->loadModel('HieraVariable');

        //Get external FQDN
        $deviceFqdn = "";
        $deviceAttrs = $device['DeviceAttribute'];
        foreach($deviceAttrs as $attr){
            if($attr['var'] == 'dns.external.fqdn')
                $deviceFqdn = $attr['val'];
        }
        if(empty($deviceFqdn))
            throw new InternalErrorException('Could not determine devices FQDN.');

        $hieraKey = "fqdn/$deviceFqdn";

        list($variables,$errors) =
            $this->controller->HieraVariable->parseAndValidateDeviceVariables($instanceVarDefs,$input,$hieraKey);

        return array($variables,$errors);
    }

    public function _getInstanceFormData($implementationId,$blueprintParts){

        $this->controller->loadModel('Role');

        $formData = array();

        //Flavors
        $flavors = $this->_getProviderFlavors($implementationId);
        $formData['instanceFlavors'] = $flavors;

        //Instance variables 
        $instanceVarDefs = array();
        foreach($blueprintParts as $part){
            if($part['DeviceType']['name'] == 'instance'){
                $roleId = $part['BlueprintPart']['role_id'];
                if(!isset($instanceVarDefs[$roleId])){
                    $instanceVarDefs[$roleId] =
                        $this->controller->Role->getRoleVariables($roleId);
                }
            }
        }
        $formData['instanceVarDefs'] = $instanceVarDefs;

        return $formData;
    }

    public function _getLoadBalancerFormData($implementationId){

        $this->controller->loadModel('Implementation');

        $formData = array();

        $implementation = $this->controller->Implementation->findById($implementationId);
        $providerId = $implementation['Implementation']['provider_id'];

        $attrs = $this->controller->Implementation->Provider->getLoadbalancerAttributes($providerId);

        //virtual ip types
        $virtualIpTypes = json_decode($attrs['load_balancers.virtual_ip_types']);
        $formData['loadBalancerVirtualIpTypes'] = Hash::combine($virtualIpTypes,'{n}','{n}');

        //protocols
        $protocols = json_decode($attrs['load_balancers.protocols']);
        $formData['loadBalancerProtocolPortMap'] = Hash::combine($protocols,'{n}.name','{n}.port');
        $formData['loadBalancerProtocols'] = Hash::combine($protocols,'{n}.name','{n}.name');
        array_walk($formData['loadBalancerProtocols'],function(&$name){
            $name = Inflector::humanize($name);
        });

        //algorithms
        $algorithms = json_decode($attrs['load_balancers.algorithms']);
        $formData['loadBalancerAlgorithms'] = Hash::combine($algorithms,'{n}.name','{n}.name');
        array_walk($formData['loadBalancerAlgorithms'],function(&$name){
            $name = Inflector::humanize(strtolower($name));
        });

        return $formData;
    }

    public function _getProviderRegionsAndFlavors($implementationId){

        $regions = $this->_getProviderRegions($implementationId);
        $flavors = $this->_getProviderFlavors($implementationId);

        return array($regions,$flavors);
    }

    public function _getProviderRegions($implementationId){

        $this->controller->loadModel('Implementation');

        //Get list of provider regions
        $regions = $this->controller->Implementation->getRegions($implementationId);
        if(empty($regions))
            throw new InternalErrorException('Regions have not been defined for this provider');
        $regions = Hash::combine($regions,'{n}.id','{n}.name');

        return $regions;
    }

    public function _getProviderFlavors($implementationId){

        $this->controller->loadModel('Implementation');

        //Get a list of provider flavors
        $flavors = $this->controller->Implementation->getFlavors($implementationId);
        if(empty($flavors))
            throw new InternalErrorException('Flavors have not been defined for this provider');
        $flavors = Hash::combine($flavors,'{n}.id','{n}.description');

        return $flavors;
    }
}
