<?php
/**
 * MODX CMP package build script
 *
 */
/*ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
ini_set('log_errors', 1);
ini_set('ignore_repeated_errors', 0);
ini_set('ignore_repeated_source', 0);
ini_set('report_memleaks', 1);
ini_set('track_errors', 1);
ini_set('docref_root', 0);
ini_set('docref_ext', 0);
ini_set('error_reporting', -1);
ini_set('log_errors_max_len', 0);*/

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0); /* makes sure our script doesnt timeout */

require_once __DIR__.'/build.config.php';
if(!$config['modx'])throw new Exception('You must declare MODX path in $config["modx"]');
if(!$config['builder'])throw new Exception('You must declare Builder path in $config["builder"]');

define('MODX_CORE_PATH', $config['modx']);
define('MODX_CONFIG_KEY','config');
require_once $config['builder'];
$modx= new modX_idle();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

$builder=new packageBuilder($modx,$config);
$builder->build();

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\nPackage Built.\nExecution time: {$totalTime}\n");

session_write_close();
exit();