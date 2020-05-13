<?php

$config['component']['resolvers']=array_merge_recursive($config['component']['resolvers'],[
    'before'=>[
        'core'=>[
            'type'=>'file',
            'options'=>[
                'source' => $config['component']['core'],
                'target' => "return MODX_CORE_PATH . 'components/';",
            ]
        ],
        'assets'=>[
            'type'=>'file',
            'options'=>[
                'source' => $config['component']['assets'],
                'target' => "return MODX_ASSETS_PATH . 'components/';",
            ]
        ],
        'options'=>[
            'type'=>'php',
            'options'=>[
                'source' => $config['resolvers'].'setupoptions.resolver.php',
            ]
        ],
        'model'=>[
            'type'=>'php',
            'options'=>[
                'source' => $config['resolvers'].'model.resolver.php',
            ]
        ]
    ],
    'after'=>[]
]);