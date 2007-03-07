<?php

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

function addLinks($sStr) {
    
    $return = preg_replace("/((http(s?):\/\/)|(www\.))([\w\.]+)([\w|\/]+)/i", "<a href=\"http$3://$4$5$6\" target=\"_blank\">$4$5$6</a>", $sStr);
    $return = preg_replace("/([\w|\.|\-|_]+)(@)([\w\.]+)([\w]+)/i", "<a href=\"mailto:$0\">$0</a>", $return);
    return $return;
}

/* mysql datum leesbaar maken voor t web */
function pretty_datetime($date) 
	{
	$break = explode(" ", $date);
	$datebreak = explode("-", $break[0]);
	$time = explode(":", $break[1]);
	$datetime = date("j M y, H:m", mktime($time[0],$time[1],$time[2],$datebreak[1],$datebreak[2],$datebreak[0]));
	return $datetime;
	}


// datum ophakken in stukjes en in array plaatsen. is handig om elegant in form te zetten
function slice_datetime($date)
	{
	$break = explode(" ", $date);
	$datebreak = explode("-", $break[0]);
	$time = explode(":", $break[1]);
	$datetime = array($datebreak[0],$datebreak[1],$datebreak[2],$time[0],$time[1],$time[2]);
	return $datetime;
	}

// catch system exception
function catchExc($sMsg) 
	{
    global $EXCEPTS;
    array_push($EXCEPTS, $sMsg);
	}

function catchErr($sMsg)
	{
    global $ERRORS;
    array_push($ERRORS, $sMsg);
	}
?>