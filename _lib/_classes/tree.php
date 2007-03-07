<?php
/*  voordat je verderbegrijpt:
 
geinstalleerd: php natuurlijk, maar ook PEAR en een database die met PEAR kan werken

aan css toevoegen:
.trigger {
	cursor: pointer;
	cursor: hand;
	font: 12px verdana, geneva, arial, sans-serif;
}

.branch {
	display: none;
	font: 12px verdana, geneva, arial, sans-serif;
	margin-left: 16px;
}

aan sql toevoegen voor sql-bomen:

CREATE TABLE tree (
tree_id INT(5) NOT NULL auto_increment,
text VARCHAR(50) default NULL,
href VARCHAR(100) default NULL,
branche INT(5) NOT NULL default '0',
branche_refer INT(5) default NULL,
no_in_line INT(5) NOT NULL default '0',
deleted INT(1) default '0',
deleted_dt DATETIME default NULL,
type INT(3) default '2',
PRIMARY KEY (tree_id)
);

te definieren: de macro DSN en de tabel $table

*/

require_once("DB.php");

class tree {

var $aTree; //array die info van tree-sql-tabel vast houdt
var $oConn; //pear connectie met sql-dbase of welke dbase dan ook
var $type; //wat-voor-tree-href-je-toont-type
var $max_branche_refer; //spreekt voor zich, toch?
var $tree_id = 0; //om te bepalen welke pagina nu wordt aangeroepen
var $no_leaf = 0;

	function tree () 
		{
		$this->type = 0;
		$this->oConn =& DB::connect(DSN);
	
		if (DB::isError($this->oConn)) 
	    	die($this->oConn->getMessage());

		$this->sqlRead();
		$this->brancheBackCount();
		}

	function setHeader() //zet deze in de....header. ja heel goed 
		{
		print '
		<script language="JavaScript">
		var openImg = new Image();
		openImg.src = "open.gif";
		var closedImg = new Image();
		closedImg.src = "closed.gif";

		function showBranch(branch)
			{
			var objBranch = document.getElementById(branch).style;
			if(objBranch.display=="block")
				objBranch.display="none";
			else
				objBranch.display="block";
			}

		
		function swapFolder(img)
			{
			objImg = document.getElementById(img);
			if(objImg.src.indexOf(\'closed.gif\')>-1)
				objImg.src = openImg.src;
			else
				objImg.src = closedImg.src;
			}
		
			function formHandler(form)
			{
			var URL = document.form.site.options[document.form.site.selectedIndex].value;
			window.location.href = URL;
			}

		</script>';
		}

	function InitialInsert() 
		{
		$sql = "INSERT INTO tree (
					text, 
					href,
					branche,
					branche_refer,
					type,
					no_in_line
				) VALUES (
					'blad',
					'cybertiesje.is-a-geek.org/vandoorn',
					'1',
					'1',
					'2',
					'1'
				)";
			
		if (DB::isError($res =& $this->oConn->query($sql) ))
			die($res->getMessage());
		
		$this->sqlRead();
		}
		
	function sqlRead()
		{
		$sql = "SELECT * FROM tree ORDER BY no_in_line";
		$res =& $this->oConn->getAll($sql, DB_FETCHMODE_ASSOC);

		if (DB::isError($res))
    		die($res->getMessage());
				
		$this->aTree = $res;
	
		$sql = "SELECT MAX(branche_refer) FROM tree";
		if (DB::isError($rsTmp =& $this->oConn->query($sql) ))
			die($rsTmp->getMessage());

		$id_ref = $rsTmp->fetchRow(DB_FETCHMODE_ASSOC);
		$this->max_branche_refer = $id_ref["MAX(branche_refer)"];
		}

	function showTree($count) 
		{
		if(!($this->aTree[0][type]))
			$this->InitialInsert();

		if (!$count)
			$count = 0;
		
		$branche = $this->aTree[$count]["branche"];
		
		while ($branche == $this->aTree[$count]["branche"])
			{
			if (($this->aTree[$count][tree_id] == $this->tree_id) || $this->aTree[$count][fat])
					{
					$b_open = "<b>";
					$b_close = "</b>";
					}
				else
					{
					$b_open = "";
					$b_close = "";
					}
		
			//als het weer te geven onderdeel een map is, doe het volgende... kijk als eerste of backBrancheCount hem open of dicht wilde hebben
			if ($this->aTree[$count]["type"] == 1) 
				{
				$branche_refer = $this->aTree[$count]["branche_refer"];
				$state = $this->aTree[$count][state];
				$show = $this->aTree[$count][show];
			
				if ($this->type == 0)
					$href = "http://".$this->aTree[$count]["href"];
				else
					$href = $_SERVER[PHP_SELF]."?tree_id=".$this->aTree[$count]["tree_id"]."&state=".$this->type;
			
				//branche niet vet maken als je in cms bestandje toevoegt en de branche heeft nog geen loot. maak in dit geval wel de verwijzing naar lege pagina vet. althans bereidt een stel variabele daar alvast op voor. is bolean, gedefinieerd door post invul scherm:
				if ($this->no_leaf && $this->aTree[$count][tree_id] == $this->tree_id)
					{
					$b_open = "";
					$b_close = "";
				
					$b_open_n = "<b>";
					$b_close_n = "</b>";
					}
			
				//print da branche/map
				print "
				<span class=\"trigger\" onClick=\"showBranch('branch$branche_refer');swapFolder('folder$branche_refer')\">
				<img src=\"$state\" border=\"0\" id=\"folder$branche_refer\">
				</span>
				<a href='$href'>$b_open".$this->aTree[$count]["text"]."$b_close</a><br>
				";
			
				print "<span style='display: $show;' class='branch' id='branch$branche_refer'>";
			
				//als op/in branche/map een blaadje/documentje zit, herhaal deze functie voor die branche, laat anders blijken dat er niks aan/in branche/map zit en ga rustig door met printen van evt andere blaadjes op deze tak
				$countPlus = $count + 1;
				if ($this->aTree[$countPlus]["branche"] == $branche_refer)
					{
					$count = $this->showTree($countPlus);
					$count--;
					}
				else
					{
//					if (!($this->type == 1))
//						{
						print "<img src='doc.gif'><a href='".$_SERVER[PHP_SELF]."?tree_id=".$this->aTree[$count]["tree_id"]."&state=5&entry=none'>".$b_open_n."[no entry yet]".$b_close_n."</a><br>";
//						}
					}
				
				print "</span>";	
				}
		
			//als t geen tak is hebben we het een stuk makkelijker, print het blaadje, increment en pak de volgende loot uit de while loop
			else
				{
				if ($this->type == 0)
					$href = "http://".$this->aTree[$count]["href"];
				else
					$href = $_SERVER[PHP_SELF]."?tree_id=".$this->aTree[$count]["tree_id"]."&state=".$this->type;
				
				print "<img src='doc.gif'><a href='$href'>$b_open".$this->aTree[$count]["text"]."$b_close</a><br>";
				}
			
			$count++;
			}
			//geef je positie door aan tak waar je uit kwam
			return $count;
		}

	function showExtraTree($type)
		{
		$this->sqlRead();
		$this->type = $type;
		$this->brancheBackCount();
			
		$count = 0;
		$end = sizeof($this->aTree);
			
		for ($count = 0; $count < $end; $count++)
			{
			$this->aTree[$count][branche] = $this->aTree[$count][branche] + $this->max_branche_refer;
				
			if ($this->aTree[$count][branche_refer])
				$this->aTree[$count][branche_refer] = $this->aTree[$count][branche_refer] + $this->max_branche_refer;
			}

		$this->showTree(0);
		}
			
	function brancheBackCount()
		{
		if ($this->type == 0)
			{
			$href = $_SERVER[SERVER_NAME].$_SERVER[PHP_SELF];
			
			$aIndexHref = explode("/",$_SERVER[PHP_SELF]);
			for ($x = 0; $x < sizeof($aIndexHref) - 1; $x++)
				{
				if (!($x == (sizeof($aIndexHref) - 2)))
					{
					$indexHref .= $aIndexHref[$x]."/";
					}
				else
					{
					$indexHrefMin = $indexHref.$aIndexHref[$x];
					$indexHref = $indexHrefMin."/";
					}
				}
				
			$hrefDir = $_SERVER[SERVER_NAME].$indexHref;

			$hrefDirMin = $_SERVER[SERVER_NAME].$indexHrefMin;
						
			for ($x = sizeof($this->aTree); $x >= 0; $x--)
				{
				if (($href == $this->aTree[$x][href]) || ($href == "www.".$this->aTree[$x][href]) || ($hrefDirMin == $this->aTree[$x][href]) || ($hrefDirMin == "www.".$this->aTree[$x][href]) || ($hrefDir == $this->aTree[$x][href]) || ($hrefDir == "www.".$this->aTree[$x][href]))
					{
					if (!($branche == $this->aTree[$x][branche_refer]))
						$branche = $this->aTree[$x][branche];
					$this->aTree[$x][fat] = true;
					}
					
				if($this->aTree[$x][branche_refer])
					{
					if ($branche == $this->aTree[$x][branche_refer])
						{
						$this->aTree[$x][state] = 'open.gif';
						$this->aTree[$x][show] = 'block';
						$branche = $this->aTree[$x][branche];
						}
					else
						{
						$this->aTree[$x][state] = 'closed.gif';
						$this->aTree[$x][show] = 'none';
						}
					}
				}
			}
		else
			{
			$sql = "SELECT * FROM tree WHERE tree_id = ".$this->tree_id;

			if (DB::isError($rsTmp =& $this->oConn->query($sql) ))
	    			die($rsTmp->getMessage());

			$id_ref = $rsTmp->fetchRow(DB_FETCHMODE_ASSOC);

			$branche = $id_ref[branche];
		
			for ($x = sizeof($this->aTree); $x > 0; $x--)
				{
			
				if($this->aTree[$x][branche_refer])
					{
					if ($branche == $this->aTree[$x][branche_refer])
						{
						$this->aTree[$x][state] = 'open.gif';
						$this->aTree[$x][show] = 'block';
						$branche = $this->aTree[$x][branche];
						}
					else
						{
						$this->aTree[$x][state] = 'closed.gif';
						$this->aTree[$x][show] = 'none';
						}
				
					}
				}
				
				
			if ($this->no_leaf)
				{
				
				for ($x = sizeof($this->aTree); $x > 0; $x--)
					{
					if ($this->aTree[$x][tree_id] == $this->tree_id)
						{
						$this->aTree[$x][state] = 'open.gif';
						$this->aTree[$x][show] = 'block';
						}
					}
				}
				
			}	
		}
		
	function showSelectForm()
		{
		if($this->tree_id)
			{
			$tree_id = "&tree_id=".$this->tree_id;
			$new = " new";
			}
		else
			{
			$tree_id = "";
			$new = "";
			}
			
		print '
		<br>
		<form name="form">
			<select name="site" size=1 onChange="javascript:formHandler()">
				<option value="">select tree action:
				<option value="'.$_SERVER[PHP_SELF].'?state=1">insert'.$new.' entry
				<option value="'.$_SERVER[PHP_SELF].'?state=2">update'.$new.' entry
				<option value="'.$_SERVER[PHP_SELF].'?state=3">delete'.$new.' entry
				<option value="'.$_SERVER[PHP_SELF].'?state=4">move'.$new.' entry';
		if($this->tree_id)
			{
			print '
				<option value="'.$_SERVER[PHP_SELF].'?state=5'.$tree_id.'">insert after/before this entry
				<option value="'.$_SERVER[PHP_SELF].'?state=6'.$tree_id.'">update this entry
				<option value="'.$_SERVER[PHP_SELF].'?state=7'.$tree_id.'">delete this entry
				<option value="'.$_SERVER[PHP_SELF].'?state=8'.$tree_id.'">move this entry';
			}
		print '
			</select>
		</form>';
		}
	
	function showInsertForm($get)
		{
		print "
		<form method='post' action='".$_SERVER[PHP_SELF]."'>
		<table>";
		
		print "
			<input type='hidden' name='entry' value='".$get[entry]."'>
			<input type='hidden' name='state' value='9'>
			<input type='hidden' name='tree_id' value='".$get[tree_id]."'>
			<tr><td>text :<td><input type='Text' name='text' size='50' maxlength='50'></tr>
			<tr><td>href: <td><input type='Text' name='href' size='50' maxlength='100'></tr>
			<tr><td>type: <td><select SIZE='1' NAME='type'>
			<option value='2' SELECTED='true' >document</option>
			<option value='1'>branche</option>
			</select></td></tr>";
		
		if (!$get[entry])
			{
			print "
			<tr><td>before/after: <td><select SIZE='1' NAME='after'>
			<option value='1' SELECTED='true' >after</option>
			<option value='0'>before</option>
			</select></td></tr>";
			}
		else
			{
			print "<input type='hidden' name='after' value='1'>";
			$this->no_leaf = true;
			}
			
		print "
		</table>
		
		<br>
		<input type='Submit' name='submit' value='zet da tree'>
		<input type='reset' value='wis da velden'>
		<br>
		</form>
		";
		}
	
	function brancheCheck($id)
		{
		$sql = "SELECT * FROM tree WHERE type = 1 AND
									branche = ".$id[branche_refer]."
									ORDER BY no_in_line DESC LIMIT 1";

		if (DB::isError($rsTmp =& $this->oConn->query($sql) ))
	    	die($rsTmp->getMessage());

		$aID = $rsTmp->fetchRow(DB_FETCHMODE_ASSOC);
		print_r ($aID);
		print "<br><br>";
		
		if ($aID[branche])
			$id_fin = $this->brancheCheck($aID);
		else
			$id_fin = $id;
			
		return $id_fin;
		}
		
	function brancheToId($id)
		{
		$id_res = $this->brancheCheck($id);
		print $id_res;

		$sql = "SELECT MAX(no_in_line) FROM tree WHERE branche = ".$id_res[branche_refer];

		if (DB::isError($rsTmp =& $this->oConn->query($sql) ))
	   		die($rsTmp->getMessage());

		$aID = $rsTmp->fetchRow(DB_FETCHMODE_ASSOC);
			
		if (($aID["MAX(no_in_line)"] == 0) && ($id_res[branche_refer] != $id[branche_refer]))
			{
			$sql = "SELECT MAX(no_in_line) FROM tree WHERE branche = ".$id_res[branche];

			if (DB::isError($rsTmp =& $this->oConn->query($sql) ))
			    die($rsTmp->getMessage());

			$aID = $rsTmp->fetchRow(DB_FETCHMODE_ASSOC);
			}
			
			return $aID["MAX(no_in_line)"];
		}

	function insertLeaf($post)
		{
		$sql = "SELECT * FROM tree WHERE tree_id = $post[tree_id]";
		if (DB::isError($rsTmp =& $this->oConn->query($sql) ))
    		die($rsTmp->getMessage());

		$id_ref = $rsTmp->fetchRow(DB_FETCHMODE_ASSOC);
		
		if($id_ref[type] == 1)
			{
			if($post[entry] == 'none')
				$id_ref[branche] = $id_ref[branche_refer];
				
			else
				{
				if($post[after])
					{
					$res_no_in_line = $this->brancheToId($id_ref);
					print "<br><br>de res_no_in_line: ";
					print_r ($res_no_in_line);
					print "<br><br>de branche refer: ";
					print_r ($id_ref[branche_refer]);
					print "<br><br>";
				
					//check of er loten aan de branche hangen en wat de laatste id daarvan is. laat dat de id zijn waarna er een bestandje wordt ingevoerd.
					if($res_no_in_line)
						$id_ref[no_in_line] = $res_no_in_line;
					}
				}
			}
		
		if($post[after])
			$bef_aft = '>';
		else
			$bef_aft = '>=';
			
		$sql = "UPDATE tree
				SET
					no_in_line = no_in_line + 1
				WHERE
					no_in_line $bef_aft ".$id_ref[no_in_line];
			
					
		if (DB::isError($rsTmp =& $this->oConn->query($sql) ))
    		die($rsTmp->getMessage());
					
		if($post[type] == 1)
			{
			$sql = "SELECT MAX(branche_refer) FROM tree";
			if (DB::isError($rsTmp =& $this->oConn->query($sql) ))
    			die($rsTmp->getMessage());

			$id_max = $rsTmp->fetchRow(DB_FETCHMODE_ASSOC);
			$id_ref[branche_refer] = $id_max["MAX(branche_refer)"] +1;
			}
		else
			$id_ref[branche_refer] = "";
			
		if (!$post[text])
			$post[text] = "[empty]";
			
		$sql = "INSERT INTO tree (
					text,
					href,
					branche,
					branche_refer,
					type,
					no_in_line
				) VALUES (
					'".$post[text]."',
					'".$post[href]."',
					'".$id_ref[branche]."',
					'".$id_ref[branche_refer]."',
					'".$post[type]."',
					'".($id_ref[no_in_line] + $post[after])."'
				)";
			
		if (DB::isError($res =& $this->oConn->query($sql) ))
    		die($res->getMessage());
		}
				
	function showUpdateForm($get)
		{
		$sql = "SELECT * FROM tree WHERE tree_id = ".$get[tree_id];

		if (DB::isError($rsTmp =& $this->oConn->query($sql) ))
	    	die($rsTmp->getMessage());

		$aID = $rsTmp->fetchRow(DB_FETCHMODE_ASSOC);
		
		if ($aID[type] == 2)
			{
			$type[0] = "SELECTED='true'";
			$type[1] = "";
			}
		else
			{
			$type[0] = "";
			$type[1] = "SELECTED='true'";
			}
		
		print "
		<form method='post' action='$_SERVER[PHP_SELF]'>
		<input type='hidden' name='tree_id' value='$aID[tree_id]'>
		<input type='hidden' name='state' value='10'>
		<table>
			<tr><td>text :<td><input type='Text' name='text' value='$aID[text]' size='50' maxlength='50'></tr>
			<tr><td>href: <td><input type='Text' name='href' value='$aID[href]' size='50' maxlength='100'></tr>
			<tr><td>type: <td><select SIZE='1' NAME='type'>
			<option value='2' $type[0]>document</option>
			<option value='1' $type[1]>branche</option>
			</select></td></tr>
		</table>
		<br>
		<input type='Submit' name='submit' value='opdateer'>
		<input type='reset' value='reset da velden'>
		<br>
		</form>
		";	
		}
		
	function updateLeaf($post)
		{
		
		$sql = "SELECT * FROM tree WHERE tree_id = ".$post[tree_id];

		if (DB::isError($rsTmp =& $this->oConn->query($sql) ))
	    	die($rsTmp->getMessage());

		$aID = $rsTmp->fetchRow(DB_FETCHMODE_ASSOC);
		
		if (($post[type] == 1) && ($aID[type] != 1))
			{
			$sql = "SELECT MAX(branche_refer) FROM tree";
			if (DB::isError($rsTmp =& $this->oConn->query($sql) ))
    			die($rsTmp->getMessage());

			$id_max = $rsTmp->fetchRow(DB_FETCHMODE_ASSOC);
			$max_branche = $id_max["MAX(branche_refer)"] +1;
			$branche_refer = ", branche_refer='$max_branche'";
			}
		else
			$branche_refer= "";
		
		if (($post[type] == 2) && ($aID[type] != 2))
			die("no branche-downgrade available yet, mister/mrs");
		
		
		$sql = "UPDATE tree SET text='$post[text]', 
								href='$post[href]',
								type='$post[type]'
								$branche_refer
							WHERE 
								tree_id='$post[tree_id]'";
		
		if (DB::isError($res =& $this->oConn->query($sql) ))
    		die($res->getMessage());
		}
		
	function showCMS()
		{
		if ($_POST[state])
			$state = $_POST[state];
		else
			$state = $_GET[state];
			
		if ($_GET[tree_id])
			$this->tree_id = $_GET[tree_id];
		
		if ($_POST[tree_id])
			$this->tree_id = $_POST[tree_id];
		
		$this->showSelectForm();
		
		print '<br><br>';
					
		switch ($state) :
		
			//eerste vier zijn om bestandje te kiezen om iets mee te doen
			case(1) :
				print "nieuw bestandje invoeren?<br>";
				print "klik op de positie waarna/voor je een documentje wilt invoeren. klik mapjes desgewenst uit:<br>";
				$this->showExtraTree(5);
				break;
			
			case(2) :
				print "bestandje updaten?<br>";
				print "klik op de positie waarna/voor je een documentje wilt invoeren. klik mapjes desgewenst uit:<br>";
				$this->showExtraTree(6);
				break;
			
			case(3) :
				print "delete something?<br>";
				print "click the document or folder below which u wish to see deleted:<br>";
				$this->showExtraTree(7);
				break;
			
			case(4) :
				$this->showExtraTree(8);
				break;
			
			//tweede vier zijn voor forms om diverse invuloefeningen te volbrengen
			case(5) :
				$this->showInsertForm($_GET);
				print "voor of na de vet gedrukte tekst zal je link worden ingevoegd (tenzij er in de map nog geen bestandje staat, in welk geval die precies op de vet gedrukte tekst zal worden gedumpt)<br><br> op andere plaats bestandje invoegen? click da tree<br><br>";
				$this->showExtraTree(5);
				break;
				
			case(6) :
				$this->showUpdateForm($_GET);
				print "de vet gedrukte tekst en bijbehorende link kun je hierboven bijwerken.<br><br> een ander bestandje bijwerken? click da tree<br>";
				$this->showExtraTree(6);
				break;
				
			case(7) :
				$this->showDeleteForm($_GET);
				$this->showExtraTree(7);
				break;
				
			//derde vier zijn om te laten zien dat alles dope gelukt is en geeft de mogelijkheid om net gedane soort actie nog eens te doen
			case(9) : 
				$this->insertLeaf($_POST);
				print "bestand hopelijk correct ingevoerd. nog een bestandje invoeren? click da tree<br>";
				$this->showExtraTree(5);
				break;
				
			case(10) : 
				$this->updateLeaf($_POST);
				print "bestand hopelijk correct upgedate. nog een bestandje updaten? click da tree<br>";
				$this->showExtraTree(6);
				break;
			
			case(11) : 
				$this->deleteLeaf($_POST);
				print "bestand hopelijk correct weggehaald. nog een bestandje deleten? click da tree<br>";
				$this->showExtraTree(7);
				break;
			
			default:
				print "standaardoptie: nieuw bestandje invoeren:<br>";
				print "klik op de positie waarna/voor je een documentje wilt invoeren. klik mapjes desgewenst uit:<br>";
				$this->showExtraTree(5);
				break; 
		endswitch;
		print "<br><br>";
		print_r ($this->aTree);
		}
		
	function deleteLeaf($post)
		{
		$sql = "SELECT * FROM tree WHERE tree_id = ".$post[tree_id];

		if (DB::isError($rsTmp =& $this->oConn->query($sql) ))
	    	die($rsTmp->getMessage());

		$aID = $rsTmp->fetchRow(DB_FETCHMODE_ASSOC);
		
		$first_no_in_line = $aID[no_in_line];
		
		if($aID[type] == 1)
			{
			$last_no_in_line = $this->brancheToId($aID);
			}
			
		if(!$last_no_in_line)
			$last_no_in_line = $first_no_in_line;
			
		$this->deleteRange($first_no_in_line, $last_no_in_line);
		}
		
	function deleteRange($first_no_in_line, $last_no_in_line)
		{
		$sql = "DELETE FROM tree WHERE no_in_line >= $first_no_in_line
									 AND no_in_line <= $last_no_in_line";
									 
		if (DB::isError($rsTmp =& $this->oConn->query($sql) ))
    		die($rsTmp->getMessage());
			
		
		$res = $last_no_in_line - $first_no_in_line + 1;
			
		$sql = "UPDATE tree
				SET
					no_in_line = no_in_line - $res
				WHERE
					no_in_line > $last_no_in_line";
					
		if (DB::isError($rsTmp =& $this->oConn->query($sql) ))
    		die($rsTmp->getMessage());
		}
		
	function showDeleteForm($get)
		{
		print "
		<form method='post' action='$_SERVER[PHP_SELF]'>
		<input type='hidden' name='tree_id' value='$get[tree_id]'>
		<input type='hidden' name='state' value='11'>
		delete the document or folder marked below, as well as any document or folder that may be contained within!?!?<br>
		<input type='Submit' name='submit' value='yes, delete it!!'>
		</form>
		";
		}
}
?>                     