<?php
require_once("DB.php");
require_once("zenBlog.php");
require_once("elements.php");
require_once("zenSession.php");

$session = new session();
if($session->evalSession())
	$use = "admin";
else
	$use = "user";

$blog = new zenBlog($use);
$blog->showForum(1);
?>