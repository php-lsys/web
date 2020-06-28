<?php
namespace {
	$_GET    = LSYS\Web\Request::sanitize($_GET);
	$_POST   = LSYS\Web\Request::sanitize($_POST);
	$_COOKIE = LSYS\Web\Request::sanitize($_COOKIE);
}
namespace LSYS\Web{
    function __(?string $string, array $values = NULL, string$domain = "default"):string
	{
	    $i18n=\LSYS\I18n\DI::get()->i18n(__DIR__."/I18n/");
	    return $i18n->__($string,  $values , $domain );
	}
}

