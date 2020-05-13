<?php
if(!class_exists('modxModelResolver')){
    class modxModelResolver extends modxScriptVehicleResolver{
        public $manager=null;
        
        
        public function __construct(&$modx,$options,&$object){
            parent::__construct($modx,$options,$object);
            $this->manager=$this->modx->getManager();
        }
        
        /********************************************************/
        public function install(){
            $this->resolveTables();
        }
        public function upgrade(){
            $this->resolveTables();
        }
        public function uninstall(){
            
        }
        /********************************************************/
        
        public function resolveTables(){
            $this->loadService();
            $meta_file=$this->config['servicePath']."metadata.".$this->modx->config['dbtype'].'.php';
            include $meta_file;
            foreach($xpdo_meta_map as $baseclass=>$arr){
                if(strpos($baseclass,'xPDO')!==0)continue;
                foreach($arr as $class){
                    $this->resolveTableByClass($class);
                }
            }
        }
        public function resolveTableByClass($class){
            unset($this->modx->map[$class]);
            $this->modx->loadClass($class, $this->config['servicePath']);
            
            $newTable = true;
            $sql = "SHOW TABLES LIKE '" . trim($this->modx->getTableName($class), '`') . "'";
            $stmt = $this->modx->prepare($sql);
            if($stmt->execute() && $stmt->fetchAll())$newTable = false;
            
            if($newTable){
                $this->manager->createObjectContainer($class);
            }else{
                $this->modx->log(modX::LOG_LEVEL_INFO, 'Altering table: ' . $class);
                $this->updateTableByClass($class);
            }
        }
        
        public function updateTableByClass($class){
            $this->modx->log(modX::LOG_LEVEL_INFO, ' - Updating columns');
            $this->updateTableColumnsByClass($class);
            $this->modx->log(modX::LOG_LEVEL_INFO, ' - Updating indexes');
            $this->updateTableIndexesByClass($class);
        }
        
        public function updateTableColumnsByClass($class){
            $tableName = $this->modx->getTableName($class);
            $tableName = str_replace('`', '', $tableName);
            $dbname = $this->modx->getOption('dbname');
    
            $c = $this->modx->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = :dbName AND table_name = :tableName");
            $c->bindParam(':dbName', $dbname);
            $c->bindParam(':tableName', $tableName);
            $c->execute();
    
            $unusedColumns = $c->fetchAll(PDO::FETCH_COLUMN, 0);
            $unusedColumns = array_flip($unusedColumns);
    
            $meta = $this->modx->getFieldMeta($class);
            $columns = array_keys($meta);
    
            foreach ($columns as $column) {
                if (isset($unusedColumns[$column])) {
                    $this->manager->alterField($class, $column);
                    $this->modx->log(modX::LOG_LEVEL_INFO, ' -- altered column: ' . $column);
                    unset($unusedColumns[$column]);
                } else {
                    $this->manager->addField($class, $column);
                    $this->modx->log(modX::LOG_LEVEL_INFO, ' -- added column: ' . $column);
                }
            }
    
            foreach ($unusedColumns as $column => $v) {
                $this->manager->removeField($class, $column);
                $this->modx->log(modX::LOG_LEVEL_INFO, ' -- removed column: ' . $column);
            }
        }
        
        public function updateTableIndexesByClass($class){
            $tableName = $this->modx->getTableName($class);
            $tableName = str_replace('`', '', $tableName);
            $dbname = $this->modx->getOption('dbname');
    
            $c = $this->modx->prepare("SELECT DISTINCT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE table_schema = :dbName AND table_name = :tableName AND INDEX_NAME != 'PRIMARY'");
            $c->bindParam(':dbName', $dbname);
            $c->bindParam(':tableName', $tableName);
            $c->execute();
    
            $oldIndexes = $c->fetchAll(PDO::FETCH_COLUMN, 0);
    
            foreach ($oldIndexes as $oldIndex) {
                $this->manager->removeIndex($class, $oldIndex);
                $this->modx->log(modX::LOG_LEVEL_INFO, ' -- removed index: ' . $oldIndex);
            }
    
            $meta = $this->modx->getIndexMeta($class);
            $indexes = array_keys($meta);
    
            foreach ($indexes as $index) {
                if ($index == 'PRIMARY') continue;
                $this->manager->addIndex($class, $index);
                $this->modx->log(modX::LOG_LEVEL_INFO, ' -- added index: ' . $index);
            }
        }
    }
}

$modelResolver=new modxModelResolver($transport->xpdo,$options,$object);
$modelResolver->run();