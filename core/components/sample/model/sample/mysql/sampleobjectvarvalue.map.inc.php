<?php
$xpdo_meta_map['SampleObjectVarValue']= array (
  'package' => 'sample',
  'version' => NULL,
  'table' => 'sample_objectsvarsvalues',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'object' => 0,
    'var' => 0,
    'value' => '',
  ),
  'fieldMeta' => 
  array (
    'object' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'attributes' => 'unsigned',
      'index' => 'index',
    ),
    'var' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'attributes' => 'unsigned',
      'index' => 'index',
    ),
    'value' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
  ),
  'indexes' => 
  array (
    'object' => 
    array (
      'alias' => 'object',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'object' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'var' => 
    array (
      'alias' => 'var',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'var' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'objectvar' => 
    array (
      'alias' => 'objectvar',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'object' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'var' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Object' => 
    array (
      'class' => 'SampleObject',
      'local' => 'object',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Var' => 
    array (
      'class' => 'SampleObjectVar',
      'local' => 'var',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
