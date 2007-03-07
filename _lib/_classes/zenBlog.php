<?php
  /*
   CREATE TABLE your_table_name
   (
   id INT(5) NOT NULL AUTO_INCREMENT,
   title VARCHAR(120) default NULL,
   body TEXT default NULL,
   submitter VARCHAR(60) default NULL,
   idRefer INT(5) NOT NULL default '0',
   picture VARCHAR(40) default NULL,
   type VARCHAR(2) default NULL,
   lang VARCHAR(3) default '0',
   langRefer INT(5) default '0',
   deleted INT(1) default '0',
   dateTime DATETIME,
   PRIMARY KEY (id),
   UNIQUE id (id)
   );
  */

class zenBlog {

  var $sTable;

  var $iThreadPageSize = 4;
  var $iPageSize;
  var $iPostPageSize = 5;

  var $use;
  var $submitter;

  var $lang;
  var $langs;
  var $languages;
  var $langRefer = 0;
  var $newsName;
  var $docName;
  var $noComments;
  var $comments;
  var $older;
  var $newer;
  var $anonymous;
  var $postText;
  var $moreText;
  var $hideText;

  var $picdir;
  var $makeThumbPage = 'thumbs.php';
  var $maxWidth = 175;
  var $maxHeight = 175;

  var $editToken = EDIT_TOKEN;

  var $script;
  var $scrBeg;
  var $scrNoFoldBeg;
  var $scrNoFoldCloseClass;
  var $scrBefore;
  var $scrAfter;
  var $scrEnd;

  function zenBlog($config)
  {

    $this->script = 1;
		
    $this->sTable = $config["table"];
    $this->use = $config["use"];
    $this->lang = $config["lang"];
    $this->langs = $config["langs"];
    $this->languages = $config["languages"];
    $this->newsName = $config["newsName"];
    $this->docName = $config["docName"];
    $this->noComments = $config["noComments"];
    $this->comments = $config["comments"];
    $this->older = $config["older"];
    $this->newer = $config["newer"];
    $this->anonymous = $config["anonymous"];
    $this->postText = $config["postText"];
    $this->hideText = $config["hideText"];
    $this->moreText = $config["moreText"];
		
    $this->picdir = SITE_DIR."_img/".$this->sTable."/";
		
    if($_COOKIE[submitter])
      $this->submitter = $_COOKIE[submitter];
		
    if($this->script == 1)
      {
			
        $this->scrBeg = '<span class="trigger" onmouseover="this.style.color=\'black\'"
onmouseout="this.style.color=\'#998377\'" onClick="loadLink(\''.$_SERVER[PHP_SELF].'?state=req&justPosts=yes&id=';
        $this->scrNoFoldBeg = '<span class="';
        $this->scrNoFoldCloseClass = 'trigger"  onmouseover="this.style.color=\'black\'"
onmouseout="this.style.color=\'#998377\'"
onClick="loadNoFoldLink(\''.$_SERVER[PHP_SELF].'?state=req&justPosts=yes&id=';
        $this->scrBefore = '\',\'';
        $this->scrAfter = '\')">';
        $this->scrEnd = '</span>';
			
      }
    $this->iDBConn = mysql_connect(SQLHOST, SQLUSER, SQLPASS) or
      die("could not connect cause".mysql_error() );
		
    mysql_select_db(SQLDATABASE, $this->iDBConn);
  }

  function inHeader()
  {
    ?>
    <script language="JavaScript">

      var use = 0;
    var element;
    var page = 0;
    var lastPosts = 0;
		
    var url = "<?php print"$_SERVER[PHP_SELF]"; ?>";


    function handleHttpResponse() 
    { 
      if (http.readyState == 4) 
        { // Split the comma delimited response into an array 
          results = http.responseText; 
          document.getElementById(element).innerHTML= results;


          if(use == 1)
            {
              window.location="#posts";
              window.scrollBy(0,-30);
            }
        } 
    }
			
    function last()
    {
      element = "lastPosts";
      http.open("GET", url + "?state=req&lastPosts=yes", true);
      lastPosts = 0;
      http.onreadystatechange = handleHttpResponse; http.send(null);
    }
    
    function checkPrime(Multiplier)
    {
      if(Multiplier == 1)
        return Multiplier;

      var Prime;
      for(i=1;i<Multiplier;i++)
        if(Multiplier /i == Math.round(Multiplier /i) && i != 1 &&  i != Multiplier)
          return (Multiplier);
      
      return (0);
    }

    function checkPs(ps)
    {
      var i;
      
      for(i=0;i<3;i++)
        if(checkPrime(ps[i]))
          return ps[i];
      
      return 0;
    }

			
    function updatePost(id,victim) 
    {
      var submitter = document.getElementById("submitter" + id).value;
      var body = document.getElementById("body" + id).value;
      var _type = document.getElementById("type" + id).value;
      var p1 = parseInt(document.getElementById("p1").value);
      var p2 = parseInt(document.getElementById("p2").value);
      var p3 = parseInt(document.getElementById("p3").value);
      var nr = parseInt(document.getElementById("nr").value);
      var pRes;
      var ps = [p1,p2,p3];
      
      if(pRes = checkPs(ps))
        alert(pRes + " is not a prime!! come on, try the prime thing next time you lazy person! I worked hard on
it.")
        
          if(p1 + p2 + p3 == nr)
            {
              lastPosts = 1;
              http.open("GET", url + "?state=req&submitter=" + escape(submitter) + "&body=" + escape(body) +
                        "&p1=" + p1 + "&p2=" + p2 + "&p3=" + p3 + "&nr=" + nr +
                        "&type=" + escape(_type) + "&idRefer=" + escape(id), true);
              use = 0;
              element = victim;
              http.onreadystatechange = handleHttpResponse; http.send(null);
            }
          else
            alert("One of the numbers is not a number or they don't add up to the requested number." +
                  "\n Could you PLEASE put a bit more effort into this!");

        
    }
			
    function loadPostLink(page,victim) 
    {
      http.open("GET", page, true);
      use = 1;
      element = victim;
      http.onreadystatechange = handleHttpResponse; http.send(null);
    }
			
    function loadNoFoldLink(page,victim) 
    {
      http.open("GET", page, true);
      use = 0;
      element = victim;

      http.onreadystatechange = handleHttpResponse; http.send(null);
      //			if(victim == 'postBlock0')
      //				{
      var a = getAnchorPosition(victim);
      window.scrollTo(0,a.y-40);

      //				window.scrollBy(0,-1000000);
      //				location.hash=1;
      //				}
    }

			
    function loadLink(page,victim) 
    {
      http.open("GET", page, true);
      use = 0;
      element = victim;
      showBranch(victim);
      http.onreadystatechange = handleHttpResponse; http.send(null);
    }

    function showPosts(form) 
    {
      var URL = document.form.site.options[document.form.site.selectedIndex].value;
      http.open("GET", URL, true);
      use = 0;
      element = "postBlock0";
      http.onreadystatechange = handleHttpResponse; http.send(null);

    }

    function getHTTPObject() 
    {
      var xmlhttp;
      /*@cc_on
       @if (@_jscript_version >= 5)
       try 
       {
       xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
       } 
       catch (e) 
       {
       try 
       {
       xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
       } 
       catch (E)
       {
       xmlhttp = false;
       }
       }
       @else
       xmlhttp = false;
       @end @*/

      if (!xmlhttp && typeof XMLHttpRequest != 'undefined') 
        {
          try 
            {
              xmlhttp = new XMLHttpRequest();
            } 
          catch (e) 
          {
            xmlhttp = false;
          }
        }
      return xmlhttp;
    }
    var http = getHTTPObject(); // We create the HTTP Objectvar url =
    "http://slet.cybertiesje.is-a-geek.org/jaded/index.php"; // The server-side script 
    var use = 0;

    function showBranch(branch)
    {
      var objBranch = document.getElementById(branch).style;
      if(objBranch.display=="block")
        objBranch.display="none";
      else
        objBranch.display="block";
    }


    // below i ripped from :
    // ===================================================================
    // Author: Matt Kruse <matt@mattkruse.com>
    // WWW: http://www.mattkruse.com/
    // ==================================================================

    function getAnchorPosition(anchorname){var useWindow=false;var coordinates=new Object();var x=0,y=0;var
use_gebi=false, use_css=false,
                                                                                                          use_layers=false;if(document.getElementById){use_gebi=true;}else
        if(document.all){use_css=true;}else
          if(document.layers){use_layers=true;}if(use_gebi &&
                                                  document.all){x=AnchorPosition_getPageOffsetLeft(document.all[anchorname]);y=AnchorPosition_getPageOffsetTop(document.all[anchorname]);}else
        if(use_gebi){var
            o=document.getElementById(anchorname);x=AnchorPosition_getPageOffsetLeft(o);y=AnchorPosition_getPageOffsetTop(o);}else
          if(use_css){x=AnchorPosition_getPageOffsetLeft(document.all[anchorname]);y=AnchorPosition_getPageOffsetTop(document.all[anchorname]);}else
            if(use_layers){var found=0;for(var
                                             i=0;i<document.anchors.length;i++){if(document.anchors[i].name==anchorname){found=1;break;}}if(found==0){coordinates.x=0;coordinates.y=0;return
                                                                                                                                                                                       
                                                                                                                                                                                       
coordinates;}x=document.anchors[i].x;y=document.anchors[i].y;}else{coordinates.x=0;coordinates.y=0;return
                                                                                                                                                                                                                                                                                            
                                                                                                                                                                                                                                                                                            
coordinates;}coordinates.x=x;coordinates.y=y;return coordinates;}
    function getAnchorWindowPosition(anchorname){var coordinates=getAnchorPosition(anchorname);var x=0;var
                                                                                                         y=0;if(document.getElementById){if(isNaN(window.screenX)){x=coordinates.x-document.body.scrollLeft+window.screenLeft;y=coordinates.y-document.body.scrollTop+window.screenTop;}else{x=coordinates.x+window.screenX+(window.outerWidth-window.innerWidth)-window.pageXOffset;y=coordinates.y+window.screenY+(window.outerHeight-24-window.innerHeight)-window.pageYOffset;}}else
        if(document.all){x=coordinates.x-document.body.scrollLeft+window.screenLeft;y=coordinates.y-document.body.scrollTop+window.screenTop;}else
          if(document.layers){x=coordinates.x+window.screenX+(window.outerWidth-window.innerWidth)-window.pageXOffset;y=coordinates.y+window.screenY+(window.outerHeight-24-window.innerHeight)-window.pageYOffset;}coordinates.x=x;coordinates.y=y;return
                                                                                                                                                                                                                                                     
                                                                                                                                                                                                                                                     
coordinates;}
    function AnchorPosition_getPageOffsetLeft(el){var ol=el.offsetLeft;while((el=el.offsetParent) != null){ol +=
el.offsetLeft;}return ol;}
    function AnchorPosition_getWindowOffsetLeft(el){return
AnchorPosition_getPageOffsetLeft(el)-document.body.scrollLeft;}
    function AnchorPosition_getPageOffsetTop(el){var ot=el.offsetTop;while((el=el.offsetParent) != null){ot +=
el.offsetTop;}return ot;}
    function AnchorPosition_getWindowOffsetTop(el){return
AnchorPosition_getPageOffsetTop(el)-document.body.scrollTop;}

    </script>

	
        <link rel="icon" href="http://www.fallenfrukt.com/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="http://www.fallenfrukt.com/favicon.ico" type="image/x-icon" />

	<?php
        }


	
  function showFormThreads()
  {
    if($this->use=="admin")
      $deleted = "";
    else
      $deleted = "AND deleted=0";
		
    $sSql = "SELECT id,title,dateTime FROM ".$this->sTable." WHERE 
													idRefer=0
													$deleted
												ORDER BY
													dateTime
												DESC
												";
    $items = $this->sqlGetArray($sSql);

    print '
		<form name="form">
			<select name="site" size=1 width=100 onChange="javascript:showPosts()">
				<option value="">choose blog entry:';
    $i = 0;
    for($i=0; $i < sizeof($items); $i++)
      {
        print "<option value='$_SERVER[PHP_SELF]?state=req&id=" .$items[$i][id] ."'>" .$items[$i][title] .", "
          .$this->pretty_datetime_small($items[$i][dateTime]);
      }
    print '
			</select>
		</form>';
  }
		
  function showAllThreads($id)
  {

    $pagerStuff = $this->pagerReturn($id);
		
    $aThreads = $pagerStuff[items];

    if (!$aThreads)
      print "<br /><br />no news yet in this language";
    else
      {
        $aThreads = $this->addStuffAndDo($aThreads);

        if($pagerStuff[pages])
          print "<div class='blockyPager'>$pagerStuff[pages]</div><br>";
			
        while(list($key, $val) = each($aThreads))
          {
            $this->showSingleThread($val,0);
            print "<br>";
          }
      }
    if($pagerStuff[pages])
      print "<div class='blockyPager top'>$pagerStuff[pages]<br /></div>";
  }
		
  function showThreadAndHisPosts($get)
  {
    $id = $get[id];
    $page = $get[page];
		
    if($this->use=="admin")
      $deleted = "";
    else
      $deleted = "AND deleted=0";
		
    $sSql = "SELECT * FROM ".$this->sTable." WHERE 
													id=$id
												$deleted
												";

    $aRow = $this->sqlGetRow($sSql);
		
    if($this->type == 1)
      $name = $this->newsName[$this->lang];
    if($this->type == 3)
      $name = $this->docName[$this->lang];
			
		
    if($this->script)
      {
        $goto = "$this->scrNoFoldBeg$this->scrNoFoldCloseClass";
        $goto .= "$this->scrBefore"."postBlock0$this->scrAfter";
        $goto .= "[<- $name]$this->scrEnd";
      }
    else
      $goto = "<a href='$_SERVER[PHP_SELF]'>[<- to news index]</a>";
			
    $open = 1;
    $nav = "<div class='blockyFont blockyPager'><br />$goto</div>";
		
    if($_GET[id] == 0)
      {
        $open = 0;
        $nav = "";
      }
			
    print $nav;
			
    $this->showSingleThread($aRow,$open);

    //$this->showAllPosts($id);
		
    //$this->showPostPost($id);
    //print $nav;
  }
		
  function showAllPosts($id)
  {
    $pagerStuff = $this->pagerReturn($id);
    print "<a name='posts'></a>";
    print "<div class='blockyPager bottom'>$pagerStuff[pages]</div>";
    if(!$pagerStuff[items])
      print "<div class='blockyPager2'>...</div>";
    else
      {
			
        while(list($key, $val) = each($pagerStuff[items]))
          {
            $this->showSinglePost($val);
          }
      }
			
    if($pagerStuff[items])
      $display = "none";
    else
      $display = "block";
			
    print "<div class='blockyPager'>$pagerStuff[pages]<br />
				<span class='trigger' ".'onmouseover="this.style.color=\'black\'"
onmouseout="this.style.color=\'#998377\'"
'."onClick=\"showBranch('postPost$id')\">> ".$this->postText[postComment][$this->lang]." <
				</span></div>

		<span style='display: $display;' class='branch' id='postPost$id'>";
    $this->showPostPost($id);
    print "</span>";
  }
		
  function showText($langRefer)
  {
    $this->type = 3;
		
    if($_GET[state])
      $state = $_GET[state];
    if($_POST[state])
      $state = $_POST[state];
			
    switch ($state) :
    case('insertThread') :
      $this->insertItem($_POST);
    if($_GET[count])
      $extra .= "&count=$_GET[count]";
					
    if($_POST[lang])
      $extra .= "&lang=$_POST[lang]";
				
    print "<head>
				<META HTTP-EQUIV=Refresh CONTENT='0; URL=$_SERVER[PHP_SELF]?id=$_POST[id]$extra'>
				</head>";
    break;

  case('thread') :
    $this->showThreadAndHisPosts($_GET);
    break;
				
  case('insertPost') :    
    //    $this->insertItem($_POST);
				
    if($_POST[idRefer])
      $result = $this->lastPage($_POST[idRefer]);
				
    if ($result)
      $extra .= "&page=$result";
				
    print "<head>
				<META HTTP-EQUIV=Refresh CONTENT='0;
URL=$_SERVER[PHP_SELF]?id=$_POST[idRefer]&state=thread$extra#posts'>
				</head>";
    break;
	
  case('newTopic') :
    if ($this->use == "admin")
      $this->showPostThread(0,$this->lang,1,0,'');
    else
      print "sorry mr, no permission!";
    break;

  case('newTextBlock') :
    if ($this->use == "admin")
      $this->showPostThread(0,$this->lang,3,0,'');
    else
      print "sorry mr, no permission!";
    break;
				
  case('updateItem') :
    if($this->use == 'admin')
      {
        $this->updateItem($_POST);
					
        if($_GET[thread] == 'yes')
          {
            $extra .= "&state=thread";
            $extra .= "&id=$_POST[thread]";
            $extra .= "#posts";
          }
        if($_GET[page])
          $extra .= "&page=$_GET[page]";
						
        if($_POST[lang])
          $extra .= "&lang=$_POST[lang]";
				
        print "<head>
					<META HTTP-EQUIV=Refresh CONTENT='0; URL=$_SERVER[PHP_SELF]?$extra'>
					</head>";
      }
    else
      print "uve got no permission";
    break;
				
  case('editPost') :
    if($this->use == 'admin')
      {
        $this->showAdminPost();
      }
    break;

  case('editThread') :
    if($this->use == 'admin')
      {
        $this->showAdminThread($_GET[type]);
      }
    break;
				
  case('editBlock') :
    if($this->use == 'admin')
      {
        $this->showAdminThread(3);
      }
    break;
		
  case('req') :
    if($_GET[justPosts] == 'yes')
      {
        if($_GET[id] == 0)
          {
            $this->showAllThreads($_GET[id]);
          }
        else
          {
            $this->showAllPosts($_GET[id]);
          }
      }
    elseif($_GET[type] == '2')
      {
        if($this->validate_prime_question())
          {
            $this->insertItem($_GET);
            $_GET[page] = $this->lastPage($_GET[idRefer]);
            $this->showAllPosts($_GET[idRefer]);
          }
        else
          {
            print "summ. went wrong with post validation. bug me about it unless you're a spammer, thanks.";
          }
      }
    elseif($_GET[lastPosts] == 'yes')
      {
        $this->showLastPosts();
      }
    else
      $this->showThreadAndHisPosts($_GET);
    break;
				
  default:
    $sSql = "SELECT * FROM ".$this->sTable." WHERE 
													langRefer=$langRefer
												AND
													lang='$this->lang'
												AND
													type='$this->type'
												";


    $aRow = $this->sqlGetRow($sSql);		
				
    if($aRow)
      $this->showThreadAndHisPosts($aRow);
    break;
    endswitch;
  }
		
  function showSingleThread($aRow, $open)
  {
    $aRow = $this->addLastPostData($aRow);
		
    $body = $this->afterEffects($aRow[body], "body");
		
    if($_GET[id] == update0)
      $body = $this->breakBody($body);

    if($this->type == 1) $link = "<a
href='http://www.fallenfrukt.com/blog.php?state=thread&id=$aRow[id]'>permalink</a>";
     else
       $link = "";
     
		
    if(!$aRow[title])
      $title = "[no title]";
    else 
      $title = $aRow[title]; 
			
		
    if (!$aRow[submitter])
      $submitter = $this->anonymous[$this->lang];
    else
      $submitter=$aRow[submitter];
		
    if ($aRow[deleted] == 1)
      $deleted = "[deleted] ";
    else
      $deleted = "";

    if ($aRow[picture])
      $picture = "<a href='$this->picdir$aRow[picture]' target='_blank'> <img
src='".$this->makeThumbPage."?img=$aRow[picture]&width=$this->maxWidth&height=$this->maxHeight' border=0
align='right' ></a>";
    #		$picture = "<img src='$this->picdir$aRow[picture]' alt='picture' width='300' height='200'
    #align='right' border='0'>";
    else
      $picture = "";
			
    if($aRow[postSubmitter])
      {

        $postSubmitter = $aRow[postSubmitter];
        $postDT = $this->pretty_datetime($aRow[postDT]);
        $postSentence = $this->comments[$this->lang]." $postSubmitter, $postDT";
      }
    else
      {
        $postSentence = $this->noComments[$this->lang];
      }
			
    if($aRow[type]!=3)
      $dateTime = $this->pretty_datetime($aRow[dateTime]);
		
    if($this->use == "admin")
      {
	
        if($_GET[state] == "thread")
          $extra .= "&thread=yes";
        if($_GET[page])
          $extra .= "&page=$_GET[page]";
				
        $edit = "</tr><tr><td valign='bottom' colspan='2'/>
				<a
href='$_SERVER[PHP_SELF]?state=editThread&type=$aRow[type]&id=$aRow[id]$extra'>$this->editToken</a></tr>";
      }
			
    if($this->script)
      {
        $postUrl = $this->scrBeg.$aRow[id];
        $postUrl .= $this->scrBefore."postBlock$aRow[id]".$this->scrAfter;
        $postUrl .= $postSentence.$this->scrEnd;
      }
    else
      {
        $postUrl = "<a href='$_SERVER[PHP_SELF]?state=thread&id=$aRow[id]#posts'>$postSentence</a>";
      }
			

    print "
		<table class=threadTable cellspacing='0' cellpadding='0'>
			<tr>
				<td>
					<table width=100%>
						<tr>
							<td class='threadTitle'>$deleted$title
							<td class='threadCreatedDate' align='right'>$dateTime
						</tr>
					</table>
				<td >&nbsp;
			</tr>
			<tr>
				<td class=threadBody>
					$picture";
					
    if($_GET[id] == update0)
      {
        if($body[1])
          {
            print "$body[0]<br /><br />";
            print "
					<span class='trigger' onmouseover=\"this.style.color='black'\"
onmouseout=\"this.style.color='#998377'\" onClick=\"showBranch('body$aRow[id]')\">
					".$this->moreText[$this->lang]."</span>

				<span class='branch' style='display: none;' id='body$aRow[id]'>";
            print "$body[1]<br /><br />
				<span class='trigger' onmouseover=\"this.style.color='black'\"
onmouseout=\"this.style.color='#998377'\"
onClick=\"showBranch('body$aRow[id]')\">
				
				".$this->hideText[$this->lang]."</span>
				<br /></span>";
            print "| $link";
          }
        else
          {
            print $body[0]; print "<br /><br />$link";				
          }
			
      }  
    else
      {
        print $body;
        print "<br /><br />$link";
      }			

    print "		$edit
			</tr>
			<tr>
				<td class=threadPosted>$postUrl</td>
			</tr>
		</table>
		<div id='postBlock$aRow[id]'>";
		
    if($open)
      $this->showAllPosts($aRow[id]);
		

    print "</div>";
  }
			
  function showSinglePost($aRow)
  {
    if (!$aRow[submitter])
      $submitter = $this->anonymous[$this->lang];
    else
      $submitter=$this->afterEffects($aRow[submitter], "postsubmit");
			
    $dateTime = $this->pretty_datetime($aRow[dateTime]);
		
    $title = $this->afterEffects($aRow[title], "posttitle");
		
    $body = $this->afterEffects($aRow[body], "post");
		
    if($this->use == "admin")
      {
        $extra = "&thread=yes";
			
        if($_GET[page])
          $extra .= "&page=$_GET[page]";
			
        $edit = "<a href='$_SERVER[PHP_SELF]?state=editPost&id=$aRow[id]$extra'>$this->editToken</a>";
      }
		
    print "
		<table class=postTable cellspacing='0' cellpadding='0'>
			<tr class=postTopRow>
				<td class=postSubmitter>$submitter</td>
				<td class=postDateTime>$dateTime</td>
			</tr>
			<tr>
				<td class=postLeftBody>
				$edit
				</td>
				<td class=postRightBody>$body
			</tr>
			</table>";
  }
		
  function getToDoLangThreads()
  {
    $lang = $this->lang;

    $query1 = "SELECT langRefer FROM ".$this->sTable." WHERE type!=2 GROUP BY langRefer";
    $query2 = "SELECT langRefer,type FROM ".$this->sTable." WHERE lang ='$lang' AND type!=2 GROUP BY langRefer";
		
    $threadPond = $this->sqlGetArray($query1);
    $threadFishie = $this ->sqlGetArray($query2);
		
    $same;
		
    for($x=0;$x<sizeof($threadPond);$x++)
      {
        $same = false;
						
        for($y=0;$y<sizeof($threadFishie);$y++)
          {
            if($threadFishie[$y][langRefer] == $threadPond[$x][langRefer])
              {
                $same = true;
              }
          }
			
        if ($same == false)
          {
            $todo[] = $threadPond[$x][langRefer];
          }
      }
		
				
    if($todo)
      print "these topics have as of yet no translation for $this->lang. Click one to update if you please.<br />";
    else
      print "all topics are translated into $this->lang. You can drink some tea now.";
				
    for($x=0;$x<sizeof($todo);$x++)
      {
        $sSql = "SELECT id,title FROM ".$this->sTable." WHERE 
														langRefer=".$todo[$x][$langRefer]."
														AND
type !=2
													LIMIT 1";
			
        $missing = $this->sqlGetRow($sSql);
			
        if(!$missing[title])
          $missing[title] = "[no title]";
			
        print "<br /><a href='$_SERVER[PHP_SELF]?state=editThread&type=".$threadFishie[0][type]."&id=$missing[id]'
>$missing[title]</a>";
      }
			
  }
	
  function getTextBlocks()
  {
    $sSql = "SELECT * FROM ".$this->sTable." WHERE 
														type=3
													GROUP BY
														langRefer";
														
			
    $textBlocks = $this->sqlGetArray($sSql);

    print "<table width=100%>";
		
    for($y=0;$y<sizeof($textBlocks);$y++)
      {
        if(!$textBlocks[$y][title])
          $textBlocks[$y][title] = "[no title]";
			
        print "<tr><td width=50%><a href='$_SERVER[PHP_SELF]?state=editBlock&id=".$textBlocks[$y][id]."'
>".$textBlocks[$y][title]. "</a><td width=50%> lr: ".$textBlocks[$y][langRefer]."</tr>";
      }
		
    print "</table>";
  }
		
  function getAssThreads($langRefer)
  {
    $sSql = "SELECT * FROM ".$this->sTable." WHERE 
												langRefer=$langRefer
												AND
													type !=2
												";
		
    $threads = $this->sqlGetArray($sSql);
		
    return $threads;
  }
		
  function showAdminThread($type)
  {
    $sSql = "SELECT * FROM ".$this->sTable." WHERE 
													id=$_GET[id]";
    $aRow = $this->sqlGetRow($sSql);
		
    $this->langRefer = $aRow[langRefer];
		
    if($this->lang)
      {
        $threads = $this->getAssThreads($aRow[langRefer]);

        $picture = $threads[0][picture];
			
        if(!$threads[0][title])
          $title = '[no title]';
        else
          $title = $threads[0][title];
			
			
        print "<h3>admin for topic: $title</h3>click on one the gray language topics below to open the language
block so you
can alter the text, use it as a translation model or just for the sheer fun of it. Click on the grey topic of an
open language block to
minimize it:<br /><br />";
				
        for($x=0;$x<sizeof($threads);$x++)
          {
            if($threads[$x][lang] == $this->lang)
              $display = 'block';
            else
              $display = 'none';
				
            if(!$threads[$x][lang])
              $threads[$x][lang] = "[not in language group]";
            print "<span class='trigger' onmouseover=\"this.style.color='black'\"
onmouseout=\"this.style.color='#998377'\" onClick=\"showBranch('edit$x')\">
				".$this->languages[$threads[$x][lang]]." -> ".$threads[$x][title]."<br /></span>

				<span class='branch postPost' style='display: $display;'
id='edit$x'><h3>".$this->languages[$threads[$x][lang]]."</h3>";
            $this->showSingleAdminThread($threads[$x]);
            print "<br /></span>";
				
            $langs[] = $threads[$x][lang];
				
          }

        print "<br>click on one of the grey languages below to add a translation of the topic and as above click on
the grey
language link of an open block to close it:<br /><br />";
							
        for($x=0;$x<sizeof($this->langs);$x++)
          {
            $also = false;
				
            for($y=0;$y<sizeof($langs);$y++)
              {
                if($this->langs[$x][0]==$langs[$y])
                  $also = true;
              }
					
            if($also == false)
              {
                if($this->langs[$x][0] == $this->lang)
                  $display = 'block';
                else
                  $display = 'none';
					
                print "<span class='trigger' onmouseover=\"this.style.color='black'\"
onmouseout=\"this.style.color='#998377'\" onClick=\"showBranch('todo$x')\">
					".$this->langs[$x][1]."<br /></span>

					<span class='branch postPost' style='display: $display;'
id='todo$x'><h3>".$this->langs[$x][1]."</h3>";
                $this->showPostThread(0,$this->langs[$x][0],$type,1,$picture);
                print "</span>";
              }
          }

      }
    else
      {
        $this->showSingleAdminThread($aRow);
      }
  }
		
  function showSingleAdminThread($aRow)
  {
    if ($aRow[deleted] == 0)
      {
        $selected[0] = "SELECTED='true'";
        $selected[1] = "";
      }
    else
      {
        $selected[0] = "";
        $selected[1] = "SELECTED='true'";
      }

    if($_GET[thread] == 'yes')
      $extra .= "&thread=yes";
    if($_GET[page])
      $extra .= "&page=$_GET[page]";
				
    print "

			<form method='post' enctype='multipart/form-data' action='$_SERVER[PHP_SELF]?$extra'>
			<table>
				<tr><td>posted by: <td><input type='Text' name='submitter' value='$aRow[submitter]'
maxlength='16'
size='17'></tr>
				<tr><td>subject:<td><input type='Text' name='title' value='$aRow[title]'
maxlength='80' size='40'></tr>
				<input type='hidden' name='MAX_FILE_SIZE' value='90000000' />
				<tr><td>upload new picture:<td><input name='userfile' type='file' maxlength='80'
size='40'/></tr>
				<tr><td>alter picture:<td><input type='Text' name='picture' value='$aRow[picture]'
maxlength='80'
size='40'></tr>
				<tr><td>description: <td><textarea rows='20' name='body'
cols='60'>$aRow[body]</textarea> </td> </tr>
				<tr><td>deleted: <td><select SIZE='1' NAME='deleted'>
				<option value='0' $selected[0]>no</option>
				<option value='1' $selected[1]>yes</option>
				</select></td></tr>
				<input type='hidden' name='id' value='$aRow[id]'>
				<input type='hidden' name='thread' value='$aRow[id]'>
				<input type='hidden' name='langRefer' value='$aRow[langRefer]'>
				<input type='hidden' name='lang' value='$aRow[lang]'>
				<input type='hidden' name='type' value='$aRow[type]'>
				<input type='hidden' name='state' value='updateItem'>
			</table>
			<br>
			<input type='Submit' name='submit' value='submit'>
			<input type='reset' value='reset'>
			</form>
		";
  }
		
  function showAdminPost()	
  {
    $sSql = "SELECT * FROM ".$this->sTable." WHERE 
												id=$_GET[id]";
    $aRow = $this->sqlGetRow($sSql);
		
    if ($aRow[deleted] == 0)
      {
        $selected[0] = "SELECTED='true'";
        $selected[1] = "";
      }
    else
      {
        $selected[0] = "";
        $selected[1] = "SELECTED='true'";
      }
			
    if($_GET[thread] == "yes")
      $extra = "&thread=yes";
    if($_GET[page])
      $extra .= "&page=$_GET[page]";
			
    print "
			
			<table>
				<form method='post' action='$_SERVER[PHP_SELF]?$extra'>
				<tr><td>posted by: <td><input type='Text' name='submitter' value='$aRow[submitter]'
maxlength='13'
size='17'></tr>
				<tr><td>subject:<td><input type='Text' name='title' value='$aRow[title]'
maxlength='40' size='41'></tr>
				<tr><td>description: <td><textarea rows='7' name='body'
cols='50'>$aRow[body]</textarea></tr>
				<tr><td>deleted: <td><select SIZE='1' NAME='deleted'>
				<option value='0' $selected[0]>no</option>
				<option value='1' $selected[1]>yes</option>
				</select></tr>
				<tr><td><td><input type='Submit' name='submit' value='submit'>
				<input type='reset' value='reset'>
			<input type='hidden' name='id' value='$aRow[id]'>
			<input type='hidden' name='thread' value='$aRow[idRefer]'>
			<input type='hidden' name='state' value='updateItem'>
			<br><br><br>
			</tr>
			</form>
			</table>
			
		";
  }
	
  function showPostThread($id,$lang,$type,$langAdmin,$picture)
  {
		
    if($type == 1)
      $topic = "add news item";
    elseif($type == 3)
      $topic = "add text block";
    elseif($type == 4)
      $topic = "add document";
		
    print "<br /><br />
			$topic<br><br>

			<form method='post' enctype='multipart/form-data' action='$_SERVER[PHP_SELF]'>
			<table>
				<tr><td>posted by: <td><input type='Text' name='submitter'
value='".$this->submitter."' maxlength='16'
size='17'></tr>
				<tr><td>subject:<td><input type='Text' name='title' maxlength='80' size='41'></tr>";
				
    if(!$langAdmin)
      {
        print"<input type='hidden' name='MAX_FILE_SIZE' value='90000000' />
				<tr><td>upload picture:<td><input name='userfile' type='file' size='41'
maxlength='80'/></tr>
				<tr><td>picname only:<td><input type='Text' name='picture' maxlength='40'
size='41'></tr>";
      }
    print	"<tr><td>description: <td><textarea rows='20' name='body' cols='60' maxlength='100'></textarea>
</td></tr>
				<input type='hidden' name='type' value='$type'>
				<input type='hidden' name='langRefer' value='$this->langRefer'>
								<input type='hidden' name='lang' value='$lang'>
				<input type='hidden' name='idRefer' value='$id'>
				<input type='hidden' name='picture' value='$picture'>
				<input type='hidden' name='state' value='insertThread'>
			
			<tr><td><td>
			<input type='Submit' name='submit' value='submit'>
			<input type='reset' value='reset'>
			</form>
			</tr>
			</table>";
    //	<br>
    //	<a href='$_SERVER[PHP_SELF]'>back to topics</a>
		
  }

  function showPostPost($id)
  {
    $scrId = $id;
			
    if($this->script)
      {
        $clickMe = "onClick=\"updatePost($id,'postBlock$id');\"";
        $click = "<input type='button' name='button$id'
value='".$this->postText[postComment][$this->lang]."'".$clickMe.">"
          ;
      }
    else
      $click = "<input type='Submit' name='submit' value='submit'>";
			
    $rnr = rand(100,999);
    print "
		<table class='postPost' align='center'>
			<tr>
				<td>
				<br>
				<form method='post' action='$_SERVER[PHP_SELF]'>
			
					<table cellspacing='0' cellpadding='0' align='center'>
                                                
						<tr>
							<td />
								".$this->postText[name][$this->lang].": 
							<td />
								<input type='Text' name='submitter$scrId'
id='submitter$scrId'
value='".$this->submitter."' maxlength='15' size='15'>
						</tr>
						<tr>
							<td colspan='2'>
								<textarea rows='6' name='body' id='body$scrId'
cols='41'></textarea>
                                                                <br><br>enter three primes that together form the
number $rnr:
                                                                <br>or just enter three numbers that together form
the number $rnr:
                                                                <br><br>admittedly the latter form is rather more
boring and you are <br>
                                                                therefore
                                                                strongly encouraged to use the former form. It's
just that i'm<br>afraid
                                                                that only providing the former, which was previously
the case, deters
                                                                <br>about 98 percent of the potential posting
population.
                                                                <br><br>
                                                                <input type='Text' name='p1' id='p1' size='2'> + 
                                                                <input type='Text' name='p2' id='p2' size='2'> + 
                                                                <input type='Text' name='p3' id='p3' size='2'> =
$rnr
                        					


                                                                <input type='hidden' name='nr' id='nr' value='$rnr'
                                                                <input type='hidden' name='type' id='type$scrId'
value='2'>
								<input type='hidden' name='idRefer'
id='idRefer$scrId' value='$id'>
								<input type='hidden' name='state'
value='insertPost'><br /><br />
								$click
							</td>
							</tr>
					</table>
				</form>
			</tr>
			<tr>
				<td colspan='2' /><br />";
    $sanitizeThis = $this->postText[addLinks][$this->lang];
    print $this->sanitize_html($sanitizeThis); 
    print "
			</tr>
		</table>";
  }
		
  function updateItem($aRow)
  {
    $uploadfile = $this->picdir . $_FILES['userfile']['name'];
				
    if($_FILES['userfile']['name'])
      {
        $picture = $_FILES['userfile']['name'];
        if (!move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile))
          die("tja...");
      }
    else
      $picture = $aRow[picture];
			
    $sql = sprintf("UPDATE ".$this->sTable." SET 
						    title=%s,
						    body=%s,
						    submitter=%s,
						    deleted=%s,
						    picture=%s
					     WHERE 
						    id='$aRow[id]'",
                   $this->sanitizeIncoming(trim($aRow[title])),
                   $this->sanitizeIncoming(trim($aRow[body])),
                   $this->sanitizeIncoming(trim($aRow[submitter])),
                   $this->sanitizeIncoming($aRow[deleted]),
                   $this->sanitizeIncoming(trim($picture)) );

    $result = $this->sqlQuery($sql);
		
    if($picture && ($this->lang) && (($aRow[type] == 3) || ($aRow[type] == 1)))
      {
        $sql = sprintf("UPDATE ".$this->sTable." SET 
														picture=%s
													WHERE 
														langRefer='$aRow[langRefer]'",
                       $this->sanitizeIncoming(trim($picture)) );

        $result = $this->sqlQuery($sql);
      }
			
  }
				
  function insertItem($aRow)
  {
    $uploadfile = $this->picdir . $_FILES['userfile']['name'];
		
    if($_FILES['userfile']['name'])
      {
        $picture = $_FILES['userfile']['name'];
        if (!move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile))
          die("tja...");
      }
    else
      $picture = $aRow[picture];
			
    $dateTime = date("y-m-d H:i:s");
		
    if(!$idrefer= $aRow[idrefer])
      $idrefer=0;
			
    if($this->lang)
      {
        if( (($aRow[type] == 3) || ($aRow[type] == 1)) && !$aRow[langRefer])
          {
            $sSql = "select MAX(langRefer) FROM ".$this->sTable;
            $maxLangRefer = $this->sqlGetRow($sSql);
            $aRow[langRefer] = $maxLangRefer["MAX(langRefer)"]+1;
          }
      }
			
    if($aRow[type] == 2)
      {
        $sSql = "SELECT * FROM ".$this->sTable." WHERE 
													id=$aRow[idRefer]";

        $sql = $this->sqlGetRow($sSql);
			
        $aRow[langRefer] = $sql[langRefer];
      }
		
    $sSql = sprintf("INSERT INTO ".$this->sTable." (title,
												body,
												submitter,
												idRefer,
												langRefer,
												lang,
												picture,
												type,
												dateTime
											)
								 
											VALUES
												(%s,
												%s,
												%s,
												%s,
												%s,
												%s,
												%s,
												%s,
												'$dateTime'
												)",
                    $this->sanitizeIncoming(trim($aRow[title])),
                    $this->sanitizeIncoming(trim($aRow[body])),
                    $this->sanitizeIncoming(trim($aRow[submitter])),
                    $this->sanitizeIncoming($aRow[idRefer]),
                    $this->sanitizeIncoming($aRow[langRefer]),
                    $this->sanitizeIncoming(trim($aRow[lang])),
                    $this->sanitizeIncoming(trim($picture)),
                    $this->sanitizeIncoming($aRow[type]));
		
    $result = $this->sqlQuery($sSql);
  }
				
  // misc. functions
	
  function pagerReturn($id)
  {
    if(!$id)
      {
        $arrLeft = $this->newer[$this->lang];
        $arrRight = $this->older[$this->lang];
        $question = '';
        $id = 0;
        $idShow = '';
        $ascdesc = ' DESC';
        $this->iPageSize = $this->iThreadPageSize;
        $threadPost = '';
        $posts = '';
        $type = "AND type=".$this->type;
        if($this->lang)
          {
            $addLang = "AND lang='$this->lang'";
          }
        $searchTerm = 'idRefer';
        $idhack=$id;
      }
    else
      {
        $posts = '#posts';
        $arrLeft = $this->older[$this->lang];
        $arrRight = $this->newer[$this->lang];
        $question = '?';
        $idShow = "id=$id&";
        $idShow2 = "$idShow&";
        $ascdesc = ' ASC';
        $this->iPageSize = $this->iPostPageSize;
        $threadPost = 'state=thread&';
        $searchTerm = 'langRefer';
        $typeAdd 	= 'AND type=2';
			
        $sSql = "SELECT * FROM ".$this->sTable." WHERE 
													id=$id";
        $idhack = $id;
        $sql = $this->sqlGetRow($sSql);
			
        $id = $sql[langRefer];
      }
			
    $page = $_GET[page];
			
    /* welgevormde scriptlink
     '<span class="trigger" onmouseover="style.cursor=\'hand\'"
     onClick="loadPostLink(\''.$_SERVER[PHP_SELF].'?state=req&id='.$aRow[$x][idRefer].$page.'\',\'content\')">'.
     $aRow[$x][submitter].', '.$this->pretty_datetime_small($aRow[$x][dateTime]).',<br />on: '.$title.'</span><br
/><br
     />';
    */
			
			
    if($this->use=="admin")
      $deleted = "";
    else
      $deleted = "AND deleted=0";
			
			
    $sSql = "select count(*) FROM ".$this->sTable." WHERE
													$searchTerm=$id
													$typeAdd
													$deleted
													$type
													$addLang";
		
    $iCount = $this->sqlGetRow($sSql);
		
    $iPage = 1;

    if(($iPage == $page) || (!$page))
      {
        if($this->script)
          $thisPage = "thisPage";
        else
          $thisPage = "class='thisPage'";
      }
			

    if($this->script)
      {
        $pages = " | $this->scrNoFoldBeg$thisPage$this->scrNoFoldCloseClass";
        $pages .= "$idhack&$posts$this->scrBefore"."postBlock$idhack$this->scrAfter";
        $pages .= $iPage.$this->scrEnd." | ";
      }
    else
      {
        $pages = " |
					<a $thisPage href='$_SERVER[PHP_SELF]?$threadPost$idShow$posts'>$iPage</a> |
";
      }
		
    $iPage++;
    for($i=$this->iPageSize; $i<$iCount[0]; $i+=$this->iPageSize)
      {
        if($iPage == $page)
          {
            if($this->script)
              $thisPage = "thisPage";
            else
              $thisPage = "class='thisPage'";
          }
        else
          {
            $thisPage = '';
          }
				
        if($this->script)
          {
            $pages .= " $this->scrNoFoldBeg$thisPage$this->scrNoFoldCloseClass";
            $pages .= "$idhack&page=$iPage$this->scrBefore"."postBlock$idhack$this->scrAfter";
            $pages .= $iPage.$this->scrEnd." | ";
          }
        else
          {
            $pages .= " <a $thisPage href='$_SERVER[PHP_SELF]?".$threadPost.$idShow2."page=$iPage$posts'>$iPage</a>
| ";
          }
				
        $iPage++;
      }
			
    if(!$page)
      $page = 1;
			
    if($page>1)
      {
        if($page>2)
          $extra = "page=".($page-1);
			
        if($this->script)
          {
            $left .= " $this->scrNoFoldBeg$this->scrNoFoldCloseClass";
            $left .= "$idhack&$extra$this->scrBefore"."postBlock$idhack$this->scrAfter";
            $left .= "<- ".$arrLeft.$this->scrEnd." ";
          }
        else
          {
            $left .= " <a href='$_SERVER[PHP_SELF]?".$threadPost.$idShow.$extra."$posts'><- $arrLeft</a> ";
          }
      }
		
    if($page < ($iPage - 1))
      {
        if($this->script)
          {
            $right .= " $this->scrNoFoldBeg$this->scrNoFoldCloseClass";
            $right .= "$idhack&page=".($page+1)."$this->scrBefore"."postBlock$idhack$this->scrAfter";
            $right .= "$arrRight ->".$this->scrEnd." ";
          }
        else
          {
            $right .= " <a href='$_SERVER[PHP_SELF]?".$threadPost.$idShow;
            $right .= "page=".($page+1)."$posts";
            $right .= "'>$arrRight -></a> ";
          }
      }
			
    if ($page < 2)
      $offset = 0;
    else
      $offset = ($page - 1) * $this->iPageSize;
		
    $range = $this->iPageSize;
		
    $sSql = "SELECT * FROM ".$this->sTable." WHERE 
													$searchTerm=$id
												$typeAdd
												$deleted
												$addLang
												$type
												ORDER BY
													dateTime
												$ascdesc
												LIMIT
$offset,$range";

    $items = $this->sqlGetArray($sSql);
		
    if($iPage - 1 < 2)
      $pages = "";
		
    $pages = $left.$pages.$right;
    $returnStuff[items] = $items;
    $returnStuff[pages] = $pages;
		
    return $returnStuff;
  }
		
  function lastPage($id)
  {
		
		
    if($id==0)
      {
        if($this->lang)
          {
            $addLang = "AND lang='$this->lang'";
          }
			
        $iPageSize = $this->iThreadPageSize;
      }
    else
      $iPageSize = $this->iPostPageSize;
		
    $sSql = "select count(*) FROM ".$this->sTable." WHERE
													idRefer=$id
													$addLang
													$deleted";
		
    $iCount = $this->sqlGetRow($sSql);
		


    $page = 0;
    for($i=0; $i<$iCount[0]; $i+=$iPageSize)
      {
        $page++;
      }
			
    return $page;
  }
		
  function properPage($id,$dateTime)
  {
		
    if($id==0)
      {
        $iPageSize = $this->iThreadPageSize;
			
        if($this->lang)
          {
            $addLang = "AND lang='$this->lang'";
          }
      }
    else
      $iPageSize = $this->iPostPageSize;
		

				
    $sSql = "select count(*) FROM ".$this->sTable." WHERE
													idRefer=$id
													AND
													dateTime
<='$dateTime'
													$addLang
													$deleted";
		
    $iCount = $this->sqlGetRow($sSql);

    $page = 0;
    for($i=0; $i<$iCount[0]; $i+=$iPageSize)
      {
        $page++;
      }
			
    return $page;
  }
		
  function postHeader()
  {
    print "
		<table class=postHeaderTable cellspacing='0' cellpadding='0'>
				<tr>
				<td class=postHeaderLeft>
					author
				<td class=postHeaderRight>
					message
				</tr>
			</table>";
  }
		 		
  function addStuffAndDo($aThreads)
  {
    $y = sizeof($aThreads);
    for($x=0; $x < $y; $x++)
      {
        //$aThreads[$x][postDT] = "00$x"; //trucje om usort te misleiden //is hier niet nodig
        $aThreads[$x] = $this->addLastPostData($aThreads[$x]);
      }
		
    // 		usort($aThreads, array("zenBlog","cmp"));

    return $aThreads;
  }
		
  function cmp($a, $b)
  {
    //the b,a order below gives reverse result
    return strcmp($b[compare], $a[compare]);
  }
		
  function addLastPostData($aThread)
  {
    $sSql = "SELECT 
					submitter, 
					dateTime
				FROM ".$this->sTable." 
												WHERE 
													langRefer=$aThread[langRefer]
												AND
													type=2
												AND
													deleted=0
												ORDER BY
													dateTime
												DESC
												LIMIT 1";
		
    $aThreadTemp = $this->sqlGetRow($sSql);
		
    if($aThreadTemp[dateTime])
      {
        $aThread[postDT] = $aThreadTemp[dateTime];
        $aThread[compare] = $aThreadTemp[dateTime];
        if($aThreadTemp[submitter])
          $aThread[postSubmitter] = $aThreadTemp[submitter];
        else
          $aThread[postSubmitter] = $this->anonymous[$this->lang];
      }
    else
      $aThread[compare] = $aThread[dateTime];
				
    return $aThread;
  }
		
  function showLastPosts()
  {
    if($this->use=="admin")
      $deleted = "";
    else
      $deleted = "AND deleted=0";
			
    $sSql = "SELECT * FROM ".$this->sTable." WHERE 
													type=2
                                                                                                                    
AND
											                   deleted=0
												ORDER BY
													dateTime
												DESC
												LIMIT 5";
		
    $aRow = $this->sqlGetArray($sSql);
			
		
    for($x=0; $x<5; $x++)
      {
        $idRefer = $aRow[$x][idRefer];
        $sSql = "SELECT title FROM ".$this->sTable." WHERE 
													
						id=$idRefer
					
															";
        $title = $this->sqlGetRow($sSql);
		
				
        $title = $title[title];
			
        if(!$aRow[$x][submitter])
          $aRow[$x][submitter] = $this->anonymous[$this->lang];
			
        if(strlen($title)>25)
          $title = substr($title,0,12)."..";
        $page = '&page='.$this->properPage($aRow[$x][idRefer],$aRow[$x][dateTime]);
        $lastPosts .= '<div class=" newsTop"><span class="trigger" onmouseover="this.style.color=\'black\'"
onmouseout="this.style.color=\'#998377\'"
onClick="loadPostLink(\''.$_SERVER[PHP_SELF].'?state=req&id='.$aRow[$x][idRefer].$page.'\',\'postBlock0\')">'.
          $aRow[$x][submitter].'</br>on: '.$title.',<br
/>'.$this->pretty_datetime_small($aRow[$x][dateTime]).'</span></div>';
      }
			
			
    print $lastPosts;
    //		print '<span class="trigger" onmouseover="this.style.color=\'black\'"
    //onmouseout="this.style.color=\'#998377\'"
    // onClick="last()">update last posts</span><br /><br />';
  }
		
  function showLastThreads()
  {
    if($this->use=="admin")
      $deleted = "";
    else
      $deleted = "AND deleted=0";
			
    $sSql = "SELECT * FROM ".$this->sTable." WHERE 
													type=2
												
												
												$deleted
												ORDER BY
													dateTime
				 								DESC
												LIMIT 5";

    $aRow = $this->sqlGetArray($sSql);
    print_r ( $aRow);
		
    for($x=0; $x<sizeof($aRow); $x++)
      {
        $title = $aRow[$x][title];
				
        $lastPosts .= '<div class=" newsTop"><span class="news">'
          .$this->pretty_datetime_small($aRow[$x][dateTime]). 
          ':</span><br /><a href="news.php?state=thread&id='.$aRow[$x][id].$page.'">'.$title.'</a></div>';
				
        /*				$lastPosts .= '<div class="trigger-diff newsTop"
onmouseover="this.style.color=\'black\'"
         onmouseout="this.style.color=\'black\'"
         onClick="loadPostLink(\''.$_SERVER[PHP_SELF].'?state=req&id='.$aRow[$x][id].$page.'\',\'postBlock0\')">'.$this->pretty_datetime_small($aRow[$x][dateTime]).':</div>';
				
         $lastPosts .= '<span class="trigger" onmouseover="this.style.color=\'black\'"
         onmouseout="this.style.color=\'#998377\'"onClick="loadPostLink(\''.$_SERVER[PHP_SELF].'?state=req&id='.$aRow[$x][id].$page.'\',\'postBlock0\')">'.$title.'</span><br/>';
        */			
      }
    print $lastPosts;
  }
		
  function pretty_datetime($date) 
  {
    $break = explode(" ", $date);
    $datebreak = explode("-", $break[0]);
    $time = explode(":", $break[1]);
    $datetime = date("j-m-y, H:i", mktime($time[0],$time[1],$time[2],$datebreak[1],$datebreak[2],$datebreak[0]));
    return $datetime;
  }
		
  function pretty_datetime_small($date) 
  {
    $break = explode(" ", $date);
    $datebreak = explode("-", $break[0]);
    $time = explode(":", $break[1]);
    $datetime = date("j-m-Y", mktime($time[0],$time[1],$time[2],$datebreak[1],$datebreak[2],$datebreak[0]));

    return $datetime;
  }

  function pretty_datetime_rss($date) 
  {
    $break = explode(" ", $date);
    $datebreak = explode("-", $break[0]);
    $time = explode(":", $break[1]);
    $datetime = date("D, d M Y H:i:s",
mktime($time[0],$time[1],$time[2],$datebreak[1],$datebreak[2],$datebreak[0]))." +0100";
		
    return $datetime;
  }
	
  function setDaCookie()
  {
    if($_POST[submitter])
      setcookie("submitter", $_POST[submitter], time()+60*60*24*60);
		
    if($_GET[submitter])
      setcookie("submitter", $_GET[submitter], time()+60*60*24*60);
  }
		
  //stringhandlers: sanitation and formatting

  function afterEffects($sString, $type)
  {
    if($type == "post")
      $sString = $this->addLinks2($sString);
    else
      $sString = $this->addLinks($sString);
		
    $sString = $this->wordWrapNice($sString,65," ");
    $sString = nl2br($sString);
    $sString = preg_replace("/(<pre>)([\w\W]*)(<\/pre>)/e","'\\1'.str_replace('<br />','','\\2').'\\3'",$sString);
    return $sString;
  }
		
  function sanitizeIncoming($value)
  {
    $value = $this->sanitize_html($value);
    $value = $this->quoteSmart($value);
		
    return $value;
  }
  function addLinks2($sStr) 
  {
    $return =
      preg_replace("/((((http(s?):\/\/)|(www\.))([\w\.\?\=\&;\&#45;\]+)([\w\.\]+)([\w|\/]+)))|((&lt;\s*a\s+href=&quot;)(((http(s?):\/\/)|(www\.))([\w\.\?\=\&;\&#45;\]+)([\w\.\]+)([\w|\/]+))(&quot;&gt;)([\s\w\,\.\-\_\"\'\:]+)&lt;\/a&gt;)|((&lt;\s*a\s+href=&#39;)(((http(s?):\/\/)|(www\.))([\w\.\?\=\&;\&#45;\]+)([\w\.\]+)([\w|\/]+))(&#39;&gt;)([\s\w\,\.\-\_\"\'\:]+)&lt;\/a&gt;)/i",
                   "<a class='postLink' href='http$5$13$23://$6$7$14$15$24$25'>$6$7$17$27</a>", $sStr);
	        
    #		$return = preg_replace("/&lt;\/a&gt;/i", "</a>", $sStr);
    #		$return = preg_replace("/((&lt;a
    #           href=(&#39;|&quot;|')))(http(s?):\/\/)?(www\.)?(\S+\.\S+)($#39;|&quot;|')&gt;[^<]+(<\/a>)/", "<a
    # class=postLink
    #           href='http$5://$6$7'>$8</a>", $return);		
                                                 $return = preg_replace("/([\w|\.|\-|_]+)(@)([\w\.]+)([\w]+)/i", "<a
href=\"mailto:$0\">$0</a>", $return);
                                                 $return = preg_replace("/&lt;pre&gt;/i", "<pre>", $return);
                                                 $return = preg_replace("/&lt;\/pre&gt;/i", "</pre>", $return);
                                                 return $return;
  }

  function addLinks($sStr) 
  {
    $return =
      preg_replace("/((((http(s?):\/\/)|(www\.))([\w\.\?\=\&;\&#45;\]+)([\w\.\]+)([\w|\/]+)))|((&lt;\s*a\s+href=&quot;)(((http(s?):\/\/)|([\w\-\.]+))([\w\.\?\=\~\&;\&#45;\]+)([\w\.\]+)([\w|\/]+))(&quot;&gt;)([\s\w\,\.\-\_\"\'\:]+)&lt;\/a&gt;)|((&lt;\s*a\s+href=&#39;)(((http(s?):\/\/)|([\w\-\.]+))([\w\.\~\?\=\&;\&#45;\]+)([\w\.\]+)([\w|\/]+))(&#39;&gt;)([\s\w\,\.\-\_\"\'\:]+)&lt;\/a&gt;)/i",
                   "<a class='body' href='http$5$13$23://$6$7$14$15$24$25'>$6$7$17$27</a>", $sStr);

    $return = preg_replace("/&lt;pre&gt;/i", "<pre>", $return);
    $return = preg_replace("/&lt;\/pre&gt;/i", "</pre>", $return);
    $return = preg_replace("/([\w|\.|\-|_]+)(@)([\w\.]+)([\w]+)/i", "<a href=\"mailto:$0\">$0</a>", $return);
    return $return;
  }
			
  function quoteSmart($value)
  {
    // Stripslashes
    if (get_magic_quotes_gpc())
      $value = stripslashes($value);

   
    // Quote if not integer
    if (!is_numeric($value))
      $value = "'" . mysql_real_escape_string($value) . "'";
		
    return $value;
  }
		
  function wordWrapNice($str,$cols,$cut) 
  {
    $len=strlen($str);
	
    $tag=0;
	
    for ($i=0;$i<$len;$i++) 
      {
        $chr = substr($str,$i,1);
        if ($chr=="<")
          $tag++;
        elseif ($chr==">")
          $tag--;
        elseif (!$tag && $chr==" ")
          $wordlen=0;
        elseif (!$tag)
          $wordlen++;
			
        if (!$tag && ($wordlen>$cols))
          {
            $chr .= $cut;
            $wordlen = 0;
          }
        $result .= $chr;
      }
  
    return $result;
  }
	
  function sanitize_html($string)
  {
    $pattern[0] = '/\&/';
    $pattern[1] = '/</';
    $pattern[2] = "/>/";
    //$pattern[3] = '/\n/';
    $pattern[4] = '/"/';
    $pattern[5] = "/'/";
    $pattern[6] = "/%/";
    $pattern[7] = '/\(/';
    $pattern[8] = '/\)/';
    $pattern[9] = '/\+/';
    $pattern[10] = '/-/';
    $replacement[0] = '&amp;';
    $replacement[1] = '&lt;';
    $replacement[2] = '&gt;';
    //$replacement[3] = '<br>';
    $replacement[4] = '&quot;';
    $replacement[5] = '&#39;';
    $replacement[6] = '&#37;';
    $replacement[7] = '&#40;';
    $replacement[8] = '&#41;';
    $replacement[9] = '&#43;';
    $replacement[10] = '&#45;';
    return preg_replace($pattern, $replacement, $string);
  }

  function checkPrime($multiplier)
  {
    if($multiplier == 1)
      return ($multiplier);

    $is_prime = 1;
    for($i=1; $i < $multiplier; $i++)
      {
        if($multiplier / $i == round($multiplier / $i)  && $i != 1 && $i != $multiplier)
          {
            $prime=0;
            return($multiplier);
            break;
          }
      }
      
    if($is_prime)
      return(0);
  }
    
  function checkPs($ps)
  {
    if($this->checkPrime($ps[0]))
      {
        
        return $ps[0];
      }
    elseif($this->checkPrime($ps[1]))
      {
        return $ps[1];
      }
    elseif($this->checkPrime($ps[2]))
      {
        return $ps[2];
      }
    else
      return(0);
  }
    
  function validate_prime_question()
  {
    $p1 = $_GET[p1];
    $p2 = $_GET[p2];
    $p3 = $_GET[p3];
    
    if(!$p1 || !$p2 || !p3)
      {
        print "one of values not present";
        return(0);
      }
    
    $prime_array = array($p1,$p2,$p3);
    
    $failed_prime = $this->checkPs($prime_array);
    
    if(!$failed_prime)
      {
        print "primes were primes";
          
        if($p1 + $p2 + $p3 == $_GET[nr])
          return(1); //sorry about the negation crap
        else
          print "primes didn't add up to required nr";
        return(0);
      }
    else
      {
         if($p1 + $p2 + $p3 == $_GET[nr])
          return(2); //sorry about the negation crap
        else
          print "nr's didn't add up to required nr";
        return(0);
      }
  }

		
  // sql query abstraction to swich databases easily
	
  function sqlQuery($sQuery)
  {
    $iResult = mysql_query($sQuery) or die("query query werkt nie");
    return $iResult;
  }

  function sqlGetRow($sQuery)
  {
    $iResult = mysql_query($sQuery) or die("<br><br>getrow query werkt nie");
    $aRow = mysql_fetch_array($iResult);
    return $aRow;
  }
		
  function sqlGetArray($sQuery)
  {
    $iResult = mysql_query($sQuery) or die("getarray querie werktnie");
		
    $aResArr;
    $count = 0;
    while($aRow = mysql_fetch_array($iResult))
      {
        $aResArr[$count] = $aRow;
        $count++;
      }

    return $aResArr;
  }
		
  //other sql functions
	
		
  function showTables()
  {
    $sSql = "show tables";
    $result = $this->sqlGetRow($sSql);
    print_r($result);
  }
	
  function makeTable()
  {
    print "bla";
    $sSql = "CREATE TABLE ".$this->sTable." (
                                             id INT(5) NOT NULL AUTO_INCREMENT,
                                             title VARCHAR(120) default NULL,
                                             body TEXT default NULL,
                                             submitter VARCHAR(60) default NULL,
                                             idRefer INT(5) NOT NULL default '0',
                                             type VARCHAR(2) default NULL,
                                             deleted INT(1) default '0',
                                             dateTime DATETIME,
                                             PRIMARY KEY (id),
                                             UNIQUE id (id)
                                             )";
		
    print $sSql;
		
    $return = $this->sqlQuery($sSql);
    print "return = $return";
  }

  //rss related

  function outputRSS()
  {
    $this->type = 1;
		
    //		$pagerStuff = $this->pagerReturn(0);	
    //		$aThreads = $pagerStuff[items];

    $deleted = "AND deleted=0";
		
    $sSql = "SELECT * FROM ".$this->sTable." WHERE 
                                                                                                                                                                                                                                                                           
idRefer=0 AND type=1
                                                                                                                                                                                                                                                                           
$deleted
                                                                                                                                                                                                                                                                           
ORDER BY
                                                                                                                                                                                                                                                                           
dateTime
                                                                                                                                                                                                                                                                           
DESC
                                                                                                                                                                                                                                                                           
";
    $aThreads = $this->sqlGetArray($sSql);

    header("Content-Type: application/rss+xml");
    header("Content-Encoding: iso-8859-1");
    print "<?xml version=\"1.0\" encoding=\"iso-8859-1\" ?>
                                                                                                                                                                                                                                                                           
<rss version=\"2.0\">

                                                                                                                                                                                                                                                                           
<channel>
                                                                                                                                                                                                                                                                           
<title>fallen frukt</title>
                                                                                                                                                                                                                                                                           
<link>http://www.fallenfrukt.com/blog.php</link>
                                                                                                                                                                                                                                                                           
<description>Fallen Frukt</description>
                                                                                                                                                                                                                                                                           
<language>en</language>
                                                                                                                                                                                                                                                                           
";
    /*
     <image>
     <title>Jaded puppy</title>
     <url>http://www.jadedpuppy.nl/_img/rss.gif</url>
     <link>http://www.jadedpuppy.nl</link>
     <width>92</width>
     <height>62</height>
     </image>";
    */	
    while(list($key, $val) = each($aThreads))
      {
        $this->printSingleRSS($val,"all");
      }

    print "</channel>
		</rss>";
  }

  function printSingleRSS($aRow)
  {
    //		$body = $this->leadCut(nl2br(trim($aRow[body])));
    $body = $this->afterEffects($aRow[body],"body");
    $date = $this->pretty_datetime_rss($aRow[dateTime]);
    if(!$aRow[title])
      $title = "[no title]";
    else 
      $title = $aRow[title]; 
			
    $link = "http://www.fallenfrukt.com/blog.php?id=$aRow[id]";	
    // &#38;		
    if($aRow[picture])
      $picture = "<a href='http://www.fallenfrukt.com/$this->picdir$aRow[picture]' target='_blank'> <img
src='http://www.fallenfrukt.com/".$this->makeThumbPage."?img=$aRow[picture]&width=$this->maxWidth&height=$this->maxHeight'
border=0
align='right' ></a>";
	
    print "
		<item>
		<title>$title</title>
		<guid isPermaLink=\"true\">$link</guid>
		<link>$link</link>
		<description>$picture$body</description>
		<pubDate>$date</pubDate>
		</item>
		";
  }
		
		
  function selLastThread()
  {
    $sSql = "SELECT id FROM ".$this->sTable." WHERE 
													type=2
												ORDER BY
													dateTime
												desc
												LIMIT 1";

    $aRow = $this->sqlGetRow($sSql);
		
    return $aRow;
  }
  function breakBody($str) 
  {
    $len=strlen($str);
    $tag;
    $done=0;
    $i=0;
		
    while (!$done) 
      {
        $chr = substr($str,$i,1);
        if ($chr=="<")
          $tag = $chr;
        if ($chr=="b" && $tag=='<')
          $tag .= $chr;
        if ($chr=="r" && $tag=='<b')
          $tag .= $chr;
        if ($chr==" " && $tag=='<br')
          $tag .= $chr;
        // 		if ($chr!="b" && $tag=="<")
        // 			$tag='';
		
        if ($tag == "<br ")
          {
            $result[] = substr($str, 0,$i-3);
            $result[] = substr($str, $i + 4, strlen($str));
            $done = 1;
          }
				
        if ($i > strlen($str))
          {
            $result[] = substr($str, 0,$i);
            $done = 1;
          }

        $i++;
      }
			
    return $result;
  }
			
  function leadCut($str) 
  {
    $len=strlen($str);
    $tag;
    $done=0;
    $i=0;
    while (!$done) 
      {
        $chr = substr($str,$i,1);
        if ($chr=="<")
          $tag = $chr;
        if ($chr=="b" && $tag=='<')
          $tag .= $chr;
        if ($chr=="r" && $tag=='<b')
          $tag .= $chr;
        // 		if ($chr!="b" && $tag=="<")
        // 			$tag='';
		
        if ($tag == "<br")
          {
            $result = substr($str, 0,$i-2);
            $done = 1;
          }
				
        if ($i > strlen($str))
          {
            $result = substr($str, 0,$i);
            $done = 1;
          }

        $i++;
      }
  
    return $result;
  }
		
  //da masterfunction

  function showForum($type)
  {
    $this->type = $type;
		
    if ($_POST[state])
      $state = $_POST[state];
    else
      $state = $_GET[state];
		
    if(! $state)
      {
        if($_GET[id])
          $this->showThreadAndHisPosts($_GET);
        else
          $this->showAllThreads(0);
      }
    else
      {
        switch ($state) :
        case('insertThread') :
          $this->insertItem($_POST);
        if($_GET[count])
          $extra .= "&count=$_GET[count]";
					
        if($_POST[lang])
          $extra .= "&lang=$_POST[lang]";
				
        print "<head>
				<META HTTP-EQUIV=Refresh CONTENT='0; URL=$_SERVER[PHP_SELF]?id=$_POST[id]$extra'>
				</head>";
        break;

      case('thread') :
        $this->showThreadAndHisPosts($_GET);
        break;
				
      case('insertPost') :
        if($this->validate_prime_question())
          {
            $this->insertItem($_POST);
          }
        else
          {
            print "summ. went wrong with post validation. bug me about it unless you're a spammer, thanks.";
          }
				
        if($_POST[idRefer])
          $result = $this->lastPage($_POST[idRefer]);
				
        if ($result)
          $extra .= "&page=$result";
				
        print "<head>
				<META HTTP-EQUIV=Refresh CONTENT='0;
URL=$_SERVER[PHP_SELF]?id=$_POST[idRefer]&state=thread$extra#posts'>
				</head>";
        break;
	
      case('newTopic') :
        if ($this->use == "admin")
          $this->showPostThread(0,$this->lang,1,0,'');
        else
          print "sorry mr, no permission!";
        break;

      case('newTextBlock') :
        if ($this->use == "admin")
          $this->showPostThread(0,$this->lang,3,0,'');
        else
          print "sorry mr, no permission!";
        break;
				

      case('newArticle') :
        if ($this->use == "admin")
          $this->showPostThread(0,$this->lang,4,0,'');
        else
          print "sorry mr, no permission!";
        break;
				
      case('toTranslate') :
        if ($this->use == "admin")
          $this->getToDoLangThreads();
        else
          print "sorry mr, no permission!";
        break;
				
      case('getTextBlocks') :
        if ($this->use == "admin")
          $this->getTextBlocks();
        else
          print "sorry mr, no permission!";
        break;
				
      case('updateItem') :
        if($this->use == 'admin')
          {
            $this->updateItem($_POST);
					
            if($_POST[lang])
              $extra .= "&lang=$_POST[lang]";
					
            if($_GET[thread] == 'yes')
              {
                $extra .= "&state=thread";
                $extra .= "&id=$_POST[thread]";
                $extra .= "#posts";
              }
            if($_GET[page])
              $extra .= "&page=$_GET[page]";
				
            print "<head>
					<META HTTP-EQUIV=Refresh CONTENT='0; URL=$_SERVER[PHP_SELF]?$extra'>
					</head>";
          }
        else
          print "uve got no permission";
        break;
				
      case('editPost') :
        if($this->use == 'admin')
          {
            $this->showAdminPost();
          }
        break;

      case('editThread') :
        if($this->use == 'admin')
          {
					
            $this->showAdminThread($_GET[type]);
          }
        break;
				
      case('editBlock') :
        if($this->use == 'admin')
          {
            $this->showAdminThread(3);
          }
        break;
				
      case('req') :
        if($_GET[justPosts] == 'yes')
          {
            if($_GET[id] == 0)
              {
                $this->showAllThreads($_GET[id]);
              }
            else
              {
                $this->showAllPosts($_GET[id]);
              }
          }
        elseif($_GET[type] == '2')
          {
            if($this->validate_prime_question())
              {
                $this->insertItem($_GET);
                $_GET[page] = $this->lastPage($_GET[idRefer]);
                $this->showAllPosts($_GET[idRefer]);
              }
            else
              {
                print "summ. went wrong with post validation. bug me about it unless you're a spammer, thanks.";
              }
          }
        elseif($_GET[lastPosts] == 'yes')
          {
            $this->showLastPosts();
          }
        else
          $this->showThreadAndHisPosts($_GET);
        break;
				
      default:
        $this->showAllThreads(0);
        break;
        endswitch;
      }


  }

  //testfunctie
  function testMe()
  {
  }
  }


			
?>