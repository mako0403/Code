<?php

	header("Content-Type:text/html; charset=utf-8");
	
	error_reporting(E_ALL ^E_NOTICE ^E_WARNING);
	
	// 设置PHP运行内存
	if (intval(ini_get('memory_limit')) < 64) {
	    ini_set('memory_limit', '64M');
	}
	
	define('NOW_TIME', time());
	
	PHP_VERSION > '5.1' ? date_default_timezone_set('Asia/Shanghai') : '';
