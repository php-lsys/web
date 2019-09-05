<?php
use LSYS\Web\Response;
use LSYS\Web\Request;
include __DIR__."/Bootstarp.php";


echo Request::ip();


$res=new Response();
$handle=fopen("Bootstarp.php", "r");
// $res->setStreamHeader($handle);
$res->setDownloadHeader("sss.php");
// $res->setRedirect("./");
array_map('header', $res->getHeaders(true));
// Response::streamOutput($handle);
fclose($handle);
