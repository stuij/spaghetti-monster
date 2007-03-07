<?php

class picPage {
  
var $imageBase;
var $makeThumbPage = THUMB_PAGE;
var $maxWidth = MAX_THUMB_WIDTH;
var $maxHeight = MAX_THUMB_HEIGHT;
var $count;

var $picsDisplayed = PICS_DISPLAYED;
var $columns = PICS_COLUMNS;

var $nrUploads = NR_PICS_UPLOADS;

function picPage()
	{
	$this->imageBase = IMG_BASE;
	$this->count = $_GET['count'];
	}

function displayPage()
	{
	$count = $_GET['count'];
	
	if(!$count)
		$count=1;

	$picsdisplayed= $this->picsDisplayed;

	$dh  = opendir($this->imageBase);
	
	while (false !== ($filename = readdir($dh))) 
	   $plaatjes[] = $filename;
		

	$blacount = 1;
	
	if($blacount == $count)
		{
		$thisPage = "class='thisPage'";
		}
	
	print "<div class='blockyFont blockyPager'>";
	echo "| ";
	echo "<a $thisPage href=\"$_SERVER[PHP_SELF]\">".$blacount."</a> | ";

	for($i=$picsdisplayed; $i<(sizeof($plaatjes) - 2); $i+=$picsdisplayed)
		{
		$blacount++;
		
		if($blacount == $count)
			{
			$thisPage = "class='thisPage'";
			}
		else
			{
			$thisPage = '';
			}
		
		echo "<a $thisPage href=\"$_SERVER[PHP_SELF]?count=".$blacount."\">".$blacount."</a> | ";
		}

//	echo "<p>u have positioned your ass on page ".$count;

	if ($count>$blacount)
		echo ", but there are no pictures here cause u have typed a number in the url-field which is higher
than u can click, so as
a punishment keep on clicking on the 'previous page' button below untill u get back to where the pictures are.";

	if ($count<0)
		echo ", but there are no pictures here cause u have typed a number in the url-field which is lower
than u can click, aka a
negative number!! I must admire u're will to hack this page. As a punishment keep on clicking on the 'next page'
button below untill u get
back to where the pictures are.";

	echo "<p>";
	
	if($count>1)
		{
		echo "<a href=\"$_SERVER[PHP_SELF]";
		if($count>2)
		echo "?count=".($count-1);
		echo "\">previous page</a> |";
		}


	if($count < $blacount)
	{
	echo "| <a href=\"$_SERVER[PHP_SELF]";
	echo "?count=".($count+1);
	echo "\">next page</a>";
	}

	echo "<p>";
	
	$end = 0;
	
	if ($count == $blacount)
		{
		$over = (sizeof($plaatjes)-2) % $picsdisplayed;
		$end = $picsdisplayed - $over;
		
		if ($end == $picsdisplayed)
			$end = 0;
		}
		
		
	print "<table align='center' valign='bottom' cellpadding='8px'><tr>";
	$column = 1;
	
	for ($i= ($count -1)* $picsdisplayed; $i< $count * $picsdisplayed - $end; $i++)
		{
		$plaatje = explode(".",$plaatjes[$i+2]);
		
		if($column > $this->columns)
			{
			print "</tr>
					<tr valign='bottom'>";
			$column = 1;
			}
					
		echo "<td valign='bottom'/> <a href=".$this->imageBase.$plaatjes[$i+2]."> <img
src='".$this->makeThumbPage."?img=".$plaatjes[$i+2]."&width=$this->maxWidth&height=$this->maxHeight' border=0><br />
$plaatje[0] </a>";
		
		$column++;
		}
		
	print "</tr></table>";

	echo "<p>";

	if($count>1)
		{
		echo "<a href=\"$_SERVER[PHP_SELF]";
		echo "?count=".($count-1);
		echo "\">previous page</a> |";
		}


	if($count < $blacount)
		{
		echo "| <a href=\"$_SERVER[PHP_SELF]";
		echo "?count=".($count+1);
		echo "\">next page</a>";
		}

	echo "<br><br><br><br>";
	print "</div>";
}

function convertImage()
	{
	if ($_GET[width])
		$this->maxWidth = $_GET[width];
	if ($_GET[height])
		$this->maxHeight = $_GET[height];
	
	# Get image location
	$image_file = $_GET[img];
	$image_path = $this->imageBase . $image_file;

	# Load image
	$img = null;
	$ext = strtolower(end(explode('.', $image_path)));
	if ($ext == 'jpg' || $ext == 'jpeg')
	    $img = @imagecreatefromjpeg($image_path);
	else if ($ext == 'png')
	    $img = @imagecreatefrompng($image_path);
	# Only if your version of GD includes GIF support
	else if ($ext == 'gif')
	    $img = @imagecreatefrompng($image_path);

	# If an image was successfully loaded, test the image for size
	if ($img) 
		{

	    # Get image size and scale ratio
    	$width = imagesx($img);
	    $height = imagesy($img);
    	$scale = min($this->maxWidth/$width, $this->maxHeight/$height);

	    # If the image is larger than the max shrink it
	    if ($scale < 1) 
			{
	        $new_width = floor($scale*$width);
	        $new_height = floor($scale*$height);

	        # Create a new temporary image
	        $tmp_img = imagecreatetruecolor($new_width, $new_height);

	        # Copy and resize old image into new image
    	    imagecopyresized($tmp_img, $img, 0, 0, 0, 0,
        	                 $new_width, $new_height, $width, $height);
    	    imagedestroy($img);
    	    $img = $tmp_img;
			}
		}

	# Create error image if necessary
	header("Content-type: image/jpeg");
	if (!$img) 
		{
	    $img = imagecreate($this->maxWidth, $this->maxHeight);
	    imagecolorallocate($img,0,0,0);
	    $c = imagecolorallocate($img,70,70,70);
	    imageline($img,0,0,$this->maxWidth,$this->maxHeight,$c2);
	    imageline($img,$this->maxWidth,0,0,$this->maxHeight,$c2);
		}

	# Display the image
	header("Content-type: image/jpeg");
	imagejpeg($img);
	}
	
	function uploadPicsForm()
		{
		print "<form enctype='multipart/form-data' action='$_SERVER[PHP_SELF]' method='post'>";

		for ($x=1;$x<$this->nrUploads;$x++)
			{
			print "<input type='hidden' name='MAX_FILE_SIZE' value='9000000' />
			Send this file: <input name='userfile$x' type='file' /><br>";
			}
			
		print"<input type='hidden' name='state' value='uploadPics'>
		<input type='submit' value='Send File' />
		</form>";
		}
		
	function uploadPicsUpload()
		{
		
		foreach($_FILES as $img)
			{
			if($img['name'])
				{
				$uploadfile = $this->imageBase . $img['name'];
				if (!move_uploaded_file($img['tmp_name'], $uploadfile))
					die("well...stuff went wrong, sorry brother");
				}
			}
		}
		
	function showUpload()
		{
		$state = $_POST[state];
		
		print "<div class='item_body'>";
		
		switch ($state) :
			case('uploadPics') :
				$this->uploadPicsUpload();
				print "<head>
						<META HTTP-EQUIV=Refresh CONTENT='0; URL=$_SERVER[PHP_SELF]'>
						</head>";
				break;
				
			default:
				$this->uploadPicsForm();
				break;
		endswitch;
		
		print '</div>';
		}
}