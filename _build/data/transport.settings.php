<?php

$data['modSystemSetting'] = [
    'shop' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.shop',
            'value' => 'shopkeeper3',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],

    'return_page' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.return_page',
            'value' => 1,
            'xtype' => 'numberfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],

    'merchant_email' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.merchant_email',
            'value' => '',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],

    'tax' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.tax',
            'value' => '',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],

    /** робокасса */

    'rid' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.robokassa.id',
            'value' => '',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],
    'rtest' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.robokassa.is_test',
            'value' => '1',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],
    'rpass' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.robokassa.passwords',
            'value' => 'testPass1||testPass2||pass1||pass2',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],

    /** сбербанк */

    'sid' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.sberbank.id',
            'value' => '',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],
    'stest' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.sberbank.is_test',
            'value' => '1',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],
    'spass' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.sberbank.passwords',
            'value' => 'testPass||pass1',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],

    /** paykeeper */

    'pid' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.paykeeper.id',
            'value' => '',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],
    'ppass' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.paykeeper.password',
            'value' => '',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],
    'pserver' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.paykeeper.server',
            'value' => '',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],
    'psecret' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.paykeeper.secret',
            'value' => '',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],


    /** альфа-банк */

    'aid' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.alpha.id',
            'value' => '',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],
    'apass' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.alpha.password',
            'value' => '',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],


    /** промсвязьбанк */
    'psbtest' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.psb.is_test',
            'value' => '1',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],
    'psbterminal' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.psb.terminal',
            'value' => '',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],
    'psbmerchant' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.psb.merchant',
            'value' => '',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],
    'psbkeys' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.psb.keys',
            'value' => 'компонента1||компонента2',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],


    /** */

    'shkstatuses' => [
        'fields' => [
            'key' => $config['component']['namespace'].'.shk.statuses',
            'value' => '2||6||7',
            'xtype' => 'textfield',
            'namespace' => $config['component']['namespace'],
            'area' => $config['component']['namespace'].'.main'
        ],
        'options' => $config['data_options']['modSystemSetting']
    ],
    
];
