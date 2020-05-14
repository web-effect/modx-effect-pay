<?php

$sconfig=[
    'effectpay.shk.hook'=>[
		'description' => '',
    ],
];

foreach($sconfig?:[] as $snippet=>$options){
    $snippet_file=$config['component']['core'].'elements/snippets/'.$snippet.'.php';
    if(!file_exists($snippet_file))continue;
    $data['modSnippet'][$snippet]=[
        'fields'=>[
            'name' => $snippet,
            'description' => $options['description'],
            'snippet' => trim(str_replace(['<?php', '?>'], '', file_get_contents($snippet_file))),
			'source' => 2,
			'static' => true,
			'static_file' => "components/effectpay/elements/snippets/$snippet.php",
        ],
        'options'=>$config['data_options']['modSnippet'],
        'relations'=>[
            'modCategory'=>[
                'main'=>'Snippets'
            ]
        ]
    ];
}