<?php
spl_autoload_register(function($class) {
	include_once __DIR__ . "/model/$class.class.php";
});
