<?php

$config['data_options']=[
    'modSystemSetting'=>[
        'search_by'=>['key'],
        'update'=>false,
        'preserve'=>true
    ],
    'modCategory'=>[
        'search_by'=>['category'],
    ],
    'modCategory.child'=>[
        'search_by'=>['category','parent'],
    ],
    'modPlugin'=>[
        'search_by'=>['name'],
    ],
    'modPluginEvent'=>[
        'search_by'=>['pluginid', 'event'],
        'preserve'=>true,
        'update'=>false
    ],
    'modEvent'=>[
        'search_by'=>['name'],
        'preserve'=>true
    ],
    'modSnippet'=>[
        'search_by'=>['name'],
    ],
];

