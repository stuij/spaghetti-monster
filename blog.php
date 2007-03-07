<?php
require_once("skel.php");

$page = new skel();

if($_GET[state] == "req")
	{
	$page->blog->showForum(1);
	}
else
	{
	$page = new skel();
	$page->startPage();
	$page->blog->showForum(1);
	$page->endPage();
	}
?>