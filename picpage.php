<?php
require_once("skel.php");
require_once("picPageClass.php");

$page = new skel();

if($_GET[state] == "req")
	$page->req();
else
	{
$pics = new picPage();
$page->startPage();
$pics->displayPage();
if($page->login)
	$pics->showUpload();
$page->endPage();
}
?>