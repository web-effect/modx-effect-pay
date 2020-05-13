<?php

switch($modx->event->name){
    case 'OnLoadWebDocument':{
        $modx->placeholders['sample.plugin']='Sample plugin worked';
        break;
    }
    case 'MyCustomEvent':{
        $modx->placeholders['sample.plugin']='Sample plugin worked';
        break;
    }
}