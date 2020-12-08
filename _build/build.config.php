<?php

$config=[
    'project'=>dirname(__DIR__).'/',
    'build'=>__DIR__.'/',
    'resolvers' => __DIR__ . '/resolvers/',
    'vehicles' => __DIR__ . '/vehicles/',
    'includes' => __DIR__ . '/includes/',
    'data' => __DIR__ . '/data/',
    'component'=>[
        'namespace'=>'effectpay',
        'name'=>'Effect Pay',
        'version'=>'0.5.2',
        'release'=>'alpha',
        //'core'=>dirname(__DIR__).'/core/components/',
        //'assets'=>dirname(__DIR__).'/assets/components/',
        'resolvers'=>[
            'before'=>[],
            'after'=>[]
        ],
        'attributes'=>[
            'requires'=>['php' => '>=7.2'],
            'setup-options'=>['source' => __DIR__.'/setup.options.php']
        ]
    ],
];
require(__DIR__.'/data.config.php');
require(__DIR__.'/env.config.php');

if(!$config['component']['core']){
    $config['component']['core']=$config['project'].'core/components/'.$config['component']['namespace'].'/';
}
if(!$config['component']['assets']){
    $config['component']['assets']=$config['project'].'assets/components/'.$config['component']['namespace'].'/';
}

require(__DIR__.'/resolvers.config.php');

if(!$config['component']['attributes']['changelog']&&file_exists($config['component']['core'].'docs/changelog.txt')){
    $config['component']['attributes']['changelog']=file_get_contents($config['component']['core'].'docs/changelog.txt');
}
if(!$config['component']['attributes']['license']&&file_exists($config['component']['core'].'docs/license.txt')){
    $config['component']['attributes']['license']=file_get_contents($config['component']['core'].'docs/license.txt');
}
if(!$config['component']['attributes']['readme']&&file_exists($config['component']['core'].'docs/readme.txt')){
    $config['component']['attributes']['readme']=file_get_contents($config['component']['core'].'docs/readme.txt');
}


