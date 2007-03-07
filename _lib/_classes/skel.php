<?php

require_once("elements.php");
require_once("DB.php");
require_once("zenBlog.php");
require_once("zenSession.php");

class skel {
//stringies
var $title = "your prefered page title";
//ints
var $login;
var $lang;
var $userLevel;
//classes
var $blog;
var $session;
var $tree;

var $newNews = array('eng' => 'latest posts',
					'spa' => 'ùltimas noticias',
					'ger' => 'letzten nachrichten',
					'swe' => 'senaste nytt',
					'dut' => 'laatste nieuws',
					'fin' => 'viimeisimmät uutiset'
					);
					

var $newPosts = array('eng' => 'latest posts',
					'spa' => 'ùltimas commentarios',
					'ger' => 'letzten posts',
					'swe' => 'senaste posts',
					'dut' => 'laatste posts',
					'fin' => 'viimeisimmät uutiset'
					);

var $noComments = array('eng' => 'no comments yet, click here to add one',
					'spa' => 'no hay comentarios, tecleo aquí para añadir',
					'ger' => 'keine kommentaren jetzt, klick hier ein zu machen',
					'swe' => 'inga kommentarer än, klicka här för att skriva en',
					'dut' => 'nog geen commentaar, klik hier om er een toe te voegen',
					'fin' => 'ei mitään kommentteja vielä, paina tästä, jos haluat kirjoittaa
sellaisen'
					);

var $comments = array('eng' => 'last comment added by',
					'spa' => 'el comentario pasado agregó de',
					'ger' => 'der letzter Kommentar gemacht von',
					'swe' => 'senaste kommentaren gjord av',
					'dut' => 'laatste commentaar toegevoegd door',
					'fin' => 'viimeisen kommentin teki'
					);

var $older = array('eng' => 'older',
					'spa' => 'más viejo',
					'ger' => 'alter',
					'swe' => 'äldre',
					'dut' => 'ouder',
					'fin' => 'vanhemmat'
					);
					
var $newer = array('eng' => 'newer',
					'spa' => 'más nuevo',
					'ger' => 'neuer',
					'swe' => 'nyare',
					'dut' => 'jonger',
					'fin' => 'uusimmat'
					);
					
var $languages = array('eng' => 'english',
					'spa' => 'spanish',
					'ger' => 'german',
					'swe' => 'swedish',
					'dut' => 'dutch',
					'fin' => 'suomeksi'
					);
					
var $langs= array(
					0=>array('swe','swedish','svenska'),
					1=>array('spa','spanish','español'),
					2=>array('eng','english','english'),
					3=>array('ger','german','deutsch'),
					4=>array('dut','dutch','nederlands'),
					5=>array('fin','finnish','suomeksi')
				);
				
var $anonymous= array('eng' => 'anonymous',
					'spa' => 'anónimo',
					'ger' => 'anonym',
					'swe' => 'anonym',
 					'dut' => 'anoniem',
					'fin' => 'nimetön'
					);
					
var $moreText= array('eng' => 'rest of text ...',
					'spa' => 'resto de texto ...',
					'ger' => 'Rest Text ...',
					'swe' => 'resten av texten ...',
 					'dut' => 'rest van text ...',
					'fin' => 'tekstin loppu...'
					);
					
var $hideText = array('eng' => 'hide text',
					'spa' => 'oculta el texto',
					'ger' => 'versteck Text',
					'swe' => 'göm texten',
 					'dut' => 'verberg text',
					'fin' => 'tekstin loppu...'
					);

var $postText = array(
					'postComment' => array('eng' => 'post new comment',
											'spa' => 'escibir nuevo
comentario',
											'ger' => 'neuer Kommentar
machen',
											'swe' => 'skriv ny
kommentar',
											'dut' => 'post nieuw
komentaar',
											'fin' => 'kirjoita uusi
kommentti'
											),
					'name' => array('eng' => 'name',
									'spa' => 'nombre',
									'ger' => 'Nahme',
									'swe' => 'namn',
									'dut' => 'naam',
									'fin' => 'nimi'
									),
					'submit' => array('eng' => 'submit',
									'spa' => 'someter',
									'ger' => 'submit',
									'swe' => 'submit',
									'dut' => 'submit',
									'fin' => 'lähetä'
									),
					'addLinks' => array('eng' => "add links in form: <a
href='[site]'>[name]</a>, www.[rest site] or
http://[site]",
										'spa' => "añadir links en forma de:
<a
href='[sitio]'>[nombre]</a>, www.[resto sitio] o http://[sitio]",
										'ger' => "linken in diese Form
hinzufügen: <a
href='[site]'>[nahm]</a>, www.[rest site] or http://[site]",
										'swe' => "lägg till länk i den här
formen: <a
href='[site]'>[name]</a>, www.[rest site] or http://[site]",
										'dut' => "voeg links toe in vorm: <a
href='[site]'>[naam]</a>, www.[rest site] of http://[site]",
										'fin' => "liitä linkkiin tässä
muodossa: <a
href='[sivu]'>[nimi]</a>, www.[uutiset sivu], http://[sivu]" 
									)
							);
				
var $pages= array(
					0=>array('blog.php',
							'langs' => array('eng' => 'blog',
											'spa' => 'blog',
											'ger' => 'Blog',
											'swe' => 'blog',
											'dut' => 'blog',
											'fin' => 'Aloitussivu'
											)
							),
					1=>array('picpage.php',
							'langs' => array('eng' => 'pics',
											'spa' => 'fotos',
											'ger' => 'Bilder',
											'swe' => 'bilder',
											'dut' => 'foto\'s',
											'fin' => 'kuvia'
											)),
					2=>array('projects.php',
							'langs' => array('eng' => 'projects',
											'spa' => 'projects',
											'ger' => 'projects',
											'swe' => 'projects',
											'dut' => 'projects',
											'fin' => 'projects'
											)
							
							)
				);
				
	

	function skel ()
		{
		$this->session = new session;
		$this->login = $this->session->evalSession();
		
		if($this->login)
			{
			$use = "admin";
			$this->userLevel = $this->session->user[user_level];
			}
		else
			$use = "user";
			
		if($_GET[lang])
			$this->lang = $_GET[lang];
//		elseif($_COOKIE[lang])
//			$this->lang = $_COOKIE[lang];
		else
			$this->lang = "eng";
			
		setCookie(lang,$this->lang,time()+6000*60*24);
		
		$inBlog = array(
					"use" => $use,
					"lang" => $this->lang,
					"table" => SQLTABLE,
					"langs" => $this->langs,
					"languages" => $this->languages,
					"noComments" => $this->noComments,
					"comments" => $this->comments,
					"older" => $this->older,
					"newer" => $this->newer,
					"anonymous" => $this->anonymous,
					"postText" => $this->postText,
					"moreText" => $this->moreText,
					"hideText" => $this->hideText,
					"newsName" => $this->pages[0]['langs'],
					"docName" => $this->pages[4]['langs']
				);
				
		$this->blog = new zenBlog($inBlog);
		}

	function startPage()
		{
		$this->blog->testMe();
		print "
			<html>
			<head>
			<title>";
		echo $this->title;
		print "</title>
			<link rel=stylesheet href='_lib/_base/jaded.css' type='text/css'>";
		
		$this->blog->inHeader();
		
		print "</head>";
	
		print "
			<body>
			<a name='1'></a>
			$_SERVER[php_self]
				<table class='all'>
				<tbody>
				
				<tr>
					<td colspan=2 />";
				

					
		$hrefArr = explode("/",$_SERVER[PHP_SELF]);
		$page = $hrefArr[sizeof($hrefArr)-1];
		print "
		
				</tr>
				
				<tr>
				
					<td class='left'>
					<a href='http://www.yoursite.com/index.php'><img
src='_img/site_banner.jpg' border='0'
align='center'></a><br />";
		

print "			<div class='fedora-corner-tr'>&nbsp;</div>
			<div class='fedora-corner-tl'>&nbsp;</div>
			<div class='fedora-content'>
			";
			

			$hrefArr = explode("/",$_SERVER[PHP_SELF]);
			$page = $hrefArr[sizeof($hrefArr)-1];
		
			for($x=0;$x<sizeof($this->pages);$x++)
				{
				if($this->pages[$x][0] == $page)
					{
					$langPrint = $this->pages[$x][langs][$this->lang];
					$bold = "class='thisMenuPage'";
					}
				else
					$bold = "class='menu'";
				
				print "<a $bold
href='".$this->pages[$x][0]."'>".$this->pages[$x][langs][$this->lang]."</a><br /> ";
				
				}
				
					
			if ($this->login)
				{
				print"<br /><br />admin stuff:
					<br /><a href='news.php?state=newTopic'>new news in
".$this->languages[$this->lang]."</a>
					<br /><a href='news.php?state=newArticle'>new document in
".$this->languages[$this->lang]."</a>
					<br />
					<br /><a href='news.php?state=toTranslate'>still to translate into
".$this->languages[$this->lang]."</a>";
					
				if($this->userLevel > 1)
					{
					print "<br /><br /><a href='news.php?state=getTextBlocks'>get textBlocks</a>
					<br /><a href='news.php?state=newTextBlock'>add new text block in
".$this->languages[$this->lang]."</a>";
					}
				}
				
			if($this->login)
				{
				print '<br /><br />';
				$this->session->showLogin();
				}	
			
			print "<br /><br />
				".$this->newNews[$this->lang]."<br /><div id='lastPosts' class='bottom'>";
					$this->blog->showLastPosts();
					print "</div><br />";


/*					
			if(!$this->login)
				{
				print "
					<span class='trigger' onmouseover=\"this.style.color='black'\"
onmouseout=\"this.style.color='#998377'\" onClick=\"showBranch('login')\">
					login<br /><br /></span>

				<span class='branch' style='display: none;' id='login'>";
				$this->session->showLogin();
				print "<br /></span>";
				}
*/
			
			print "</div>
			<div class='fedora-corner-br'>&nbsp;</div>
			<div class='fedora-corner-bl'>&nbsp;</div>
					</td>
					
				";

		$this->session->showVerbose();

		print "			
		<td class='main' valign='top'>";
		
		print "

			<div class='fedora-corner-tr'>&nbsp;</div>
			<div class='fedora-corner-tl'>&nbsp;</div>
			<div class='fedora-content'>";

	if(!("projects.php" == $page))
	$this->blog->showFormThreads();			
			
//			print $langPrint;
//			print "<div class='blockyPager top'>"."</div>";
		

		print "<div class='item_body' id='postBlock0'>";
		}
	
	function endPage()
		{
		print "
				</div>
		</div>
			<div class='fedora-corner-br'>&nbsp;</div>
			<div class='fedora-corner-bl'>&nbsp;</div>
		
							<!-- einde pagina -->

					</td>

				</tr>
	
				<tr>
					<td>

					<!-- evt voeter -->
					</td>
				</tr>
			</tbody>
			</table>
		</body>
		</html>";
		}
		
	function req()
	{
	$this->blog->showForum(1);
	}
}
?>