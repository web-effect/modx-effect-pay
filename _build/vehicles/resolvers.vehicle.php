<?php
if(!class_exists('modxVehicleResolver')){
    class modxVehicleResolver{
        public $modx=null;
        public $service=null;
        
        
        public function __construct(&$modx,$options,&$object){
            $this->modx=$modx;
            $this->config=$options['component'];
            $this->options=$options;
            $this->object=$object;
            
            $this->config['corePath']=$this->modx->getOption(
                $this->config['namespace'].'.core_path', null,
                $this->modx->getOption('core_path').'components/'.$this->config['namespace'].'/'
            );
            $this->config['modelPath']=$this->config['corePath'].($this->config['modelPath']?:('model/'));
            $this->config['servicePath']=$this->config['servicePath']?($this->config['corePath'].$this->config['servicePath']):($this->config['modelPath'].$this->config['namespace'].'/');
        }
        
        public function loadService(){
            $this->service=$this->modx->getService(
                $this->config['namespace'],
                $this->config['serviceName']?:$this->config['name'],
                $this->config['servicePath']
            );
        }
        public function run(){
            switch ($this->options[xPDOTransport::PACKAGE_ACTION]) {
                case xPDOTransport::ACTION_INSTALL:
                    $this->install();
                    break;
                case xPDOTransport::ACTION_UPGRADE:
                    $this->upgrade();
                    break;
                case xPDOTransport::ACTION_UNINSTALL:
                    $this->uninstall();
                    break;
            }
        }
        public function install(){
            
        }
        public function upgrade(){
            
        }
        public function uninstall(){
            
        }
    }
    
    class modxScriptVehicleResolver extends modxVehicleResolver{
        
    }
    
    class modxObjectVehicleResolver extends modxVehicleResolver{
        
    }
}

return true;