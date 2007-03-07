<?php
require_once("skel.php");

$page = new skel();

if($_GET[state] == "req")
	{
	$page->blog->showForum(3);
	}
else
	{
	$page = new skel();
	$page->startPage();
	?>
	<br />
	<?php
	$page->blog->showText(INDEX);
		print "</div>";
	$page->endPage();

	}
?>