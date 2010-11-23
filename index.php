<?php
require_once('config.php');

//set_include_path(get_include_path().PATH_SEPARATOR.PATH_PAGES.PATH_SEPARATOR.PATH_CLASS.PATH_SEPARATOR.PATH_CLASS.'module');
//spl_autoload_extensions('.class.php');
//spl_autoload_register();
if(DEBUG) {
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set('display_errors', 1); 
}

function __autoload($class_name) {
	$filename = PATH_PAGES.$class_name.'.class.php';
	if(file_exists($filename)) {
		require_once $filename;
		return;
	}
	
	$filename = PATH_CLASS.$class_name.'.class.php';
	if(file_exists($filename))
		require_once $filename;
}

Sessionmanager::start();

$tmpUrl = new Url();
$page = mb_convert_case($tmpUrl->getPage(), MB_CASE_TITLE);

if(!Sessionmanager::isLogged() || isset($tmpUrl->getPageParams()->logout))
	$page = Login;
else if(empty($page))
	$page = Mainpage;
else if($page == Login && Sessionmanager::isLogged())
	$page = Mainpage;

$errorCode = NULL;
if(class_exists($page)) {
	$oPage = new $page();
	if($oPage instanceof IPage && $oPage instanceof Kernel) {
		if(Util::isAjaxRequest()) {
			$errorCode = $oPage->generateAjax(); // This is the AJAX pipeline.
		} else {
			$errorCode = $oPage->generate(); // This is the default pipeline.
		}
	} else {
		$errorCode = 501;
	}
} else {
	$errorCode = 404;
}

if(isset($errorCode)) {
	$tmpUrl->setPage('Error');
	$tmpUrl->setParam('code', $errorCode);
	header('Location: '.$tmpUrl->buildUrlString());
}

?>
