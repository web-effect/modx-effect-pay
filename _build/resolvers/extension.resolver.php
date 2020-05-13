<?php
if(!class_exists('modxExtensionResolver')){
    class modxExtensionResolver extends modxScriptVehicleResolver{
        
        
        /********************************************************/
        public function install(){
            $this->addExtensionPackage();
        }
        public function upgrade(){
            $this->addExtensionPackage();
        }
        public function uninstall(){
            $this->removeExtensionPackage();
        }
        /********************************************************/
        
        public function addExtensionPackage(){
            if ($this->modx instanceof modX) {
                $modelRelPath=str_replace($this->config['corePath'],'',$this->config['modelPath']);
                $this->modx->addExtensionPackage($this->config['namespace'], '[[++core_path]]components/'.$this->config['namespace'].'/'.$modelRelPath);
            }
        }
        public function removeExtensionPackage(){
            if ($this->modx instanceof modX) {
                $this->modx->removeExtensionPackage($this->config['namespace']);
            }
        }
    }
}

$extensionResolver=new modxExtensionResolver($transport->xpdo,$options,$object);
$extensionResolver->run();