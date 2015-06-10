<?php

$connect = mysql_connect("localhost","root","");
mysql_select_db("parts_pros",$connect);

require_once('class.queryDB.php');

$qdb = new queryDB();
//$items = queryDB::searchVariations('1000001');

var_dump ($qdb->years);

?>

