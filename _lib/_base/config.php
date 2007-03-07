<?php
 
/* assign base system constants */
define("SITE_URL", "http://www.yoursite.com/");     // base site url
define("SITE_DIR", "");                               // base site directory
define("BASE_DIR", "http://www.yoursite.com");         // base file directory
define("DSN", "mysql://user:passwd@localhost/dbase"); // DSN for PEAR usage

// config for main sql table
define("SQLHOST","localhost"); 
define("SQLUSER","user");
define("SQLPASS","yourpass");
define("SQLTABLE","your_table_name");
define("SQLDATABASE","dbase");

define("PREFIX","yourname"); // sql table prefix for zenSession.php

define("SU_NAME","your name"); // initial admin name
define("SU_PASS","your password"); // initial admin password
define("SU_EMAIL","your email"); // initial admin email

// picpage config
define("THUMB_PAGE","thumbs.php"); //name of the page that handles the dynamic picture conversion
define("MAX_THUMB_WIDTH", 250);
define("MAX_THUMB_HEIGHT", 250);
define("PICS_DISPLAYED", 12);
define("PICS_COLUMNS", 4);
define("NR_PICS_UPLOADS", 10);
define("IMG_BASE", "_img/public/");


define("INDEX", 3); // the number is row id in the table above that corresponds with your entry. Is for static          
// pages. See index.php 

define("EDIT_TOKEN", "edit"); // how you want your edit token to look like in the edit-in-place stuff. all of html
is valid
?>