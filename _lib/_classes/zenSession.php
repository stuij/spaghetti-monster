<?php

/*
CREATE TABLE yourname_users (
  user_id int(10) NOT NULL auto_increment,
  user_name varchar(50) NOT NULL default '',
  user_pass varchar(50) NOT NULL default '',
  user_email varchar(100) NOT NULL default '',
  user_level int(1) NOT NULL default '1',
  last_login_ip varchar(15) default NULL,
  last_login_host varchar(100) default NULL,
  last_login_dt datetime default NULL,
  last_login_count varchar(2) default NULL,
  session varchar(32) default '',
  status int(1) NOT NULL default '2',
  created_dt datetime NOT NULL default '0000-00-00 00:00:00',
  modified_dt datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (user_id),
  KEY user_name_ref (user_name),
  KEY user_pass_ref (user_pass)
) TYPE=MyISAM;

CREATE TABLE yourname_apps (
  app_id int(2) NOT NULL auto_increment,
  app_name varchar(50) NOT NULL default '',
  PRIMARY KEY  (app_id)
) TYPE=MyISAM;

CREATE TABLE yourname_perms (
  perm_id int(10) NOT NULL auto_increment,
  app_id int(10) NOT NULL default '0',
  user_id int(10) NOT NULL default '0',
  perm int(1) NOT NULL default '1',
  PRIMARY KEY  (perm_id)
) TYPE=MyISAM;

*/

class session {

	var $user;
	var $sessId;
	var $conn;
	var $verbose;
    
	function session() 
		{
		$this->conn =& DB::connect(DSN);
        
		if (DB::isError($this->conn) ) 
			$this->getMsg($this->conn->getMessage());
		}
    
		//session functions

	function showLogin()
		{
		if(!$this->user[user_id])
			{
			print "<form method='post' action='$_SERVER[PHP_SELF]'>
					username : <input type='Text' name='user' maxlength='15' size='16'><br />
					password : <input type='password' name='pass' maxlength='30' size='16'><br
/>
					<input type='Submit' name='submit' value='log me in'>
					</form>";
			}
		else
			{
			print "<form method='post' action='$_SERVER[PHP_SELF]'>
					<input type='Submit' name='logout' value='log me out'>
					</form>";
			}
		}

	function setDaCookie()
		{
		$login = setCookie(PREFIX."sess",$this->sessId,time()+60*60*24);
		return $login;
		}

	function evalSession()
		{
		$login = false;

		if (isset($_COOKIE[PREFIX."sess"]))
			{
			$this->sessId = $_COOKIE[PREFIX."sess"];
			$login = $this->evalCookie();
			}

		if (!$login)
			$login = $this->evalAndDoPost();
		
		return $login;
		}

	function evalAndDoPost()
		{
		if ($_POST[user] && $_POST[pass])
			{
			$login = $this->evalLogin();
			}
		else
			$login = false;

		if ($login)
			{
			$login = $this->setSess();
			return $login;
			}
		else
			{
			$this->showLogin;
			return $login;
			}
		}

	function evalCookie()
		{
		if($_POST[logout])
			$this->logOut();
		else
			{
			$sql = sprintf("SELECT * FROM ".PREFIX."_users 
					WHERE 
						session=%s
					AND 
						status>0",
					$this->sanitizeIncoming($_COOKIE[PREFIX."sess"]));

			$userInfo = $this->sqlGetRow($sql);

			if($userInfo[user_id])
				{
				$this->user = $userInfo;
				$this->setDaCookie();
				return true;
				}
			else
				return false;


			}
		}

	function evalLogin()
		{
		$sql = sprintf("SELECT * FROM ".PREFIX."_users 
				WHERE 
					user_name=%s
				AND 
					user_pass=%s
				AND
					status>0",
				$this->sanitizeIncoming($_POST[user]),
				$this->sanitizeIncoming($_POST[pass]));

		$userInfo = $this->sqlGetRow($sql);

		if($userInfo)
			{
			$this->user = $userInfo;
			return true;
			}
		else
			return false;
		}

	function makeSessId()
		{
		$sessId = md5(rand());

		$sql = "SELECT * FROM ".PREFIX."_users 
				WHERE 
					session='$sessId'";

		if($this->sqlGetRow($sql))
			return $this->makeSessId();
		else
			return $sessId;
		}

	function SetSess()
		{
		$this->sessId = $this->makeSessId();
 
		$user_id = $this->user[user_id];

		if ($this->sessId)
			{
			$sql = "UPDATE ".PREFIX."_users 
						SET 
							session='$this->sessId' 
						WHERE 
							user_id=$user_id";
			
			$login = $this->sqlQuery($sql);
			}
		else
			$login = false;

		if ($login)
			$login = $this->setDaCookie();

		return $login;
		}

	function logOut()
		{
		$session = $_COOKIE[PREFIX."sess"];

		$sql = "UPDATE ".PREFIX."_users 
					SET 
						session='' 
					WHERE 
						session='$session'";

		$logout = $this->sqlQuery($sql);
		
		$logout = setCookie(PREFIX."sess",'');

		return $logout;
		}

		//sql queries

	function insertInitialUser()
		{
		$sql = "INSERT INTO ".PREFIX."_users (
						user_name, 
						user_pass,  
						user_email,
						user_level, 
						status, 
						created_dt,
						modified_dt
					) values (
						SU_NAME,
						SU_PASS',
						SU_EMAIL,
						1,
						2,
						(NOW()),
						(NOW())
					)";

		$result = $this->sqlQuery($sql);
		print $result;
		}

	function insertUser($userInfo)
		{
		$sql = sprintf("INSERT INTO ".PREFIX."_users (
						user_name, 
						user_pass,  
						user_email,
						user_level, 
						status, 
						created_dt,
						modified_dt
					) values (
						%s, 
						%s, 
						%s, 
						$userInfo[userLevel], 
						$userInfo[status], 
						(NOW()), 
						(NOW())
					)",
		$this->sanitizeIncoming($userInfo[userName]),
		$this->sanitizeIncoming($userInfo[userPass]),
		$this->sanitizeIncoming($userInfo[userEmail]));

		
		$result = $this->sqlQuery($sql);
		return $result;
		}

		// sql abstraction

	function sqlQuery($sql)
		{
		if (DB::isError($rsTmp = $this->conn->query($sql)))
			{
			print $rsTmp->getMessage();
			return false;
			}
		else
			return true;
		}

	function sqlGetRow($sQuery)
		{
		if (DB::isError($rsTmp =& $this->conn->query($sQuery) ))
			{
			print $sQuery;
            print $rsTmp->getMessage();
            return false;
			}
		else
			return $rsTmp->fetchRow(DB_FETCHMODE_ASSOC);
		}

	function sqlGetArray($sQuery)
		{
		}
		// misc functions

	function showVerbose()
		{
		print_r($this->verbose);
		}
		
	function getMsg($string)
		{
		$this->verbose[] = $string;
		}

	function pretty_datetime($date) 
		{
		$break = explode(" ", $date);
		$datebreak = explode("-", $break[0]);
		$time = explode(":", $break[1]);
		$datetime = date("j M y, H:i",
mktime($time[0],$time[1],$time[2],$datebreak[1],$datebreak[2],$datebreak[0]));
		return $datetime;
		}

		// sanitation

	function sanitizeIncoming($value)
		{
		$value = $this->sanitize_html($value);
		$value = $this->quoteSmart($value);

		return $value;
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
}

?>