<?php
	// already connected to db

	$qry = 	"select distinct stock_notification_requests.*, searchdata.orderable " .
		"FROM stock_notification_requests, searchdata ".
		"WHERE stock_notification_requests.ID = searchdata.id ".
		"AND stock_notification_requests.notification_date = \"\"".
		"AND searchdata.orderable = \"T\"";

	$results = mysql_db_query ($dbname, $qry);

	while($row = mysql_fetch_object ($results))
	{
		$message = 	"Dear $row->name,\r\n\r\n".
				"You requested that we notify you when our $row->product was again available.  ".
				"This item is can now be ordered.\r\n\r\n".
				"To purchase, please visit http://www.partspros.com/" . strtolower($row->id) . ".html\r\n\r\n".
				"Thank you for your business!\r\n\r\n".
				"Sincerely,\r\n\r\n".
				"The Parts Pros Team\r\n".
				"http://www.partspros.com";
		$to       = "$row->email";
		$subject  =  "$row->product is back in stock at partspros.com!";
		$headers  = 'From: PartsPros.com Stock Notifications <info@partspros.com>' . "\r\n" .
		    'Reply-To: info@partspros.com' . "\r\n" .
		    'X-Mailer: PHP/' . phpversion();

		mail($to, $subject, $message, $headers);


		// clear out requests we just sent email for

		$now = date("M j Y");

		$qry2 = "update stock_notification_requests ".
			"set notification_date=\"$now\" ".
			"WHERE email=\"$row->email\" AND id=\"$row->id\"";
		mysql_db_query ($dbname, $qry2);
		
		
		print "<p>Sent notification to $row->name ($row->email) for product [$row->product]\r\n";
	}


	mysql_close($link);

?>
