<?php
namespace {
	$_GET    = LSYS\Web\Request::sanitize($_GET);
	$_POST   = LSYS\Web\Request::sanitize($_POST);
	$_COOKIE = LSYS\Web\Request::sanitize($_COOKIE);
}
namespace LSYS\Web{
	function __($string, array $values = NULL, $domain = "default")
	{
	    $i18n=\LSYS\I18n\DI::get()->i18n(__DIR__."/I18n/");
	    return $i18n->__($string,  $values , $domain );
	}
}

