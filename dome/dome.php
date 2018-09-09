<?php
use LSYS\Web\Response;
use LSYS\Web\Request;
include __DIR__."/Bootstarp.php";


echo Request::ip();


$res=new Response();
$handle=fopen("Bootstarp.php", "r");
// $res->set_stream_header($handle);
$res->set_download_header("sss.php");
// $res->set_redirect("./");
array_map('header', $res->get_headers(true));
// Response::stream_output($handle);
fclose($handle);
