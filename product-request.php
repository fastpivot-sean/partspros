<?php
require_once('config.php');
require_once(CLASS_DIRECTORY . 'class.queryDB.php');
?>

<html>
 <head>
  <title>Request to be notified when <? print $product; ?> is back in stock</title>
  <link href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700|Bevan|Open+Sans:400,600,600italic,700,700italic,400italic|Cabin:400,400italic,700,700italic" rel="stylesheet" type="text/css" />
  <style type="text/css">
    body {font: 16px 'Open Sans', arial, sans-serif;}
    td {padding: 3px 10px 3px 0;}
    input.itext {border: 1px solid #ccc; font: 14px 'Open Sans', arial, sans-serif; padding: 3px 5px; width: 200px;}
    p#error {background: #f89f9f; border: 1px solid #f00; border-radius: 10px; padding: 10px;}
  </style>
 </head>
 <body style="margin:0px;margin-top:10px;">

<?

$email = $_GET['email'];
if(!$email)	{	$email = $_POST['email'];	}

$name = $_GET['name'];
if(!$name)	{	$name = $_POST['name'];	}

$posted = $_GET['posted'];
if(!$posted)	{	$posted = $_POST['posted'];	}

$product = $_GET['product'];
if(!$product)	{	$product = $_POST['product'];	}

$ID = $_GET['id'];
if(!$ID)	{	$ID = $_POST['id'];	}



$errors = '';


if(($email) and ($name))
{
	if(!ereg(".+\@.+\..+",$email))
	{	$errors = "Your email address does not appear to be valid. <br>Please try again.";	
  		$posted = '';	}
}
else if (($email) OR ($name))
{
 	$errors = "Please provide your name AND email address so that we can contact you when this item is available.";
	$posted = '';
}


if($posted)
{
	print "<br><b>Thank you - we will notify you via email as soon as this item is available again!";
    
    $connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
    mysqli_select_db($connect, DB_NAME);
    
    $now = date("M j Y");
	$query = "insert into stock_notification_requests values(\"$name\",\"$email\",\"$ID\",\"$product\",\"$now\",\"\")";

	mysqli_query($connect, $query);
	mysqli_close($connect);
    
}
else
{
	if($errors)
	{	print "<p id='error'>$errors</p>";	}

	print 	"Please just supply your name and email address,<br> and we will notify you as soon as our<br> <b>$product</b> is back in stock!";

	print 	"<form style=\"margin:20px 0 0 0\"><table><tr><td>Name:</td><td><input class='itext' type=text name=name value=\"$name\"></td></tr>\r\n".
		"<tr><td>Email:</td><td><input class='itext' type=text name=email value=\"$email\"></td></tr>\r\n".
		"<tr><td /><td><input type=submit value=\"Notify Me>\"></td></tr></table>\r\n".
		"<input type=hidden name=posted value=\"YES\">\r\n".
		"<input type=hidden name=product value=\"$product\">\r\n".
		"<input type=hidden name=id value=\"$ID\"></form>\r\n";


}


?>

  </font>
 </body>
</html>