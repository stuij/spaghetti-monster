<?php
require_once("skel.php");
require_once("picPageClass.php");

$page = new skel();
$page->startPage();
$page->session->showLogin();
$page->endPage();
?>