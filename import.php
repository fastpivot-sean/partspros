<?php 

require_once('config.php');

ini_set('memory_limit', '200M');
ignore_user_abort(true);
set_time_limit(0);
ini_set("max_execution_time",0);

$timestamp = time();
$errors = array();

// connect to db
if (!$connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS)) {
    throw new Exception('Unable to connect to database: '. mysqli_error($connect));
}
mysqli_select_db($connect, DB_NAME);

// import item data
$url = "http://yhst-141660816666872.stores.yahoo.net/item-data-feed.html";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

$result = preg_match("/\[[^>]*\]/", $result, $matches);
$result = preg_replace(array("/\[STARTDATA\]/", "/\[ENDDATA\]/"), array("", ""), $matches[0]);

$file = '/var/www/html/custom/partspros/partspros2015/itemdata.csv';
$fh = fopen($file, 'a');
ftruncate($fh, 1);
fwrite($fh, $result);
fclose($fh);

$fh = fopen($file, 'r');
rewind($fh);
$flag = true;
while (($data = fgetcsv($fh, 2000, ',', '"')) !== FALSE) {
    
    $count = count($data);
    $query = 'INSERT INTO items (code, item_id, path, name, price, sale_price,
                                 orderable, caption, ship_weight,
                                 mid_description, long_description, warranty, usa_made, 
                                 options, fp_cross_sell_ids,
                                 image, height, width, inset_00, inset_00_thumb,
                                 inset_01, inset_01_thumb, inset_02, inset_02_thumb,
                                 inset_03, inset_03_thumb, inset_04, inset_04_thumb, 
                                 inset_05, inset_05_thumb, inset_06, inset_06_thumb, 
                                 inset_07, inset_07_thumb, inset_08, inset_08_thumb, 
                                 inset_09, inset_09_thumb, inset_10, inset_10_thumb, 
                                 inset_11, inset_11_thumb, inset_12, inset_12_thumb, 
                                 bullet_point_01, bullet_point_02, bullet_point_03, bullet_point_04,
                                 bullet_point_05, bullet_point_06, bullet_point_07, bullet_point_08,
                                 bullet_point_09, bullet_point_10, bullet_point_11, bullet_point_12
                                ) VALUES (';
                                
    for ($j = 0; $j < $count; $j++) {
        $query .=  '"' . mysqli_real_escape_string($connect, $data[$j]) . '"';
        if ($j != $count - 1) {
            $query .= ",";
        }
    }
    $query .= ') ON DUPLICATE KEY UPDATE ';
    $query .= 'item_id=[1], path=[2], name=[3], price=[4], sale_price=[5],
               orderable=[6], caption=[7], ship_weight=[8],
               mid_description=[9], long_description=[10], warranty=[11], usa_made=[12], 
               options=[13], fp_cross_sell_ids=[14],
               image=[15], height=[16], width=[17], inset_00=[18], inset_00_thumb=[19],
               inset_01=[20], inset_01_thumb=[21], inset_02=[22], inset_02_thumb=[23],
               inset_03=[24], inset_03_thumb=[25], inset_04=[26], inset_04_thumb=[27],
               inset_05=[28], inset_05_thumb=[29], inset_06=[30], inset_06_thumb=[31],
               inset_07=[32], inset_07_thumb=[33], inset_08=[34], inset_08_thumb=[35],
               inset_09=[36], inset_09_thumb=[37], inset_10=[38], inset_10_thumb=[39],
               inset_11=[40], inset_11_thumb=[41], inset_12=[42], inset_12_thumb=[43],
               bullet_point_01=[44], bullet_point_02=[45], bullet_point_03=[46], bullet_point_04=[47],
               bullet_point_05=[48], bullet_point_06=[49], bullet_point_07=[50], bullet_point_08=[51],
               bullet_point_09=[52], bullet_point_10=[53], bullet_point_11=[54], bullet_point_12=[55]';
    
    for ($i = 1; $i < $count; $i++) {
        $reg = '/\[' . $i . '\]/';
        $query = preg_replace($reg, '"' . mysqli_real_escape_string($connect, $data[$i]) . '"', $query);
    }
    $query = preg_replace('/\\\\{2,}/', '\\', $query);
    $query .= ';';
    

        //echo '<code>' . $query . '</code><br><br>';

    if (!mysqli_query($connect, $query)) {
        array_push($errors, mysqli_error($connect));
    }

}

fclose($fh);
unset($data, $result);

$url2 = "http://yhst-141660816666872.stores.yahoo.net/item-var-feed-2.html";

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $url2);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
$result2 = curl_exec($ch2);
curl_close($ch2);

$result2 = preg_match("/\[[^>]*\]/", $result2, $matches2);
$result2 = preg_replace(array("/\[STARTDATA\]/", "/\[ENDDATA\]/"), array("", ""), $matches2[0]);

$file2 = '/var/www/html/custom/partspros/partspros2015/vardata.csv';
$fh2 = fopen($file2, 'a');
ftruncate($fh2, 1);
fwrite($fh2, $result2);
fclose($fh2);

$fh2 = fopen($file2, 'r');

if (fgetcsv($fh2, 2000, ',', '"') !== FALSE) {
    
    mysqli_query($connect, 'TRUNCATE TABLE variations');
    rewind($fh2);
    while (($data2 = fgetcsv($fh2, 2000, ',', '"')) !== FALSE) {
    
        $count2 = count($data2);
        $query2 = "INSERT INTO variations (code, make, model, start_year, end_year, note, category, sub_category) VALUES (";
        
        for ($k = 0; $k < count($data2); $k++) {
            $query2 .= '"';
            $query2 .= mysqli_real_escape_string($connect, $data2[$k]);
            $query2 .= '"';
            if ($k != count($data2) - 1) {
                $query2 .= ",";
            }
        }
        
        $query2 .= ');';
        $query2 = preg_replace('/\\\\{2,}/', '\\', $query2);
        
        //echo '<code>' . $query2 . '</code><br><br>';
        if (!mysqli_query($connect, $query2)) {
            array_push($errors, mysqli_error($connect));
        }
    }
} else {
    array_push($errors, 'Could not read item variations feed. Table not updated.');
}

fclose($fh2);
unset($data2, $result2);

$url3 = "http://yhst-141660816666872.stores.yahoo.net/img-var-feed.html";

$ch3 = curl_init();
curl_setopt($ch3, CURLOPT_URL, $url3);
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
$result3 = curl_exec($ch3);
curl_close($ch3);

$result3 = preg_match("/\[[^>]*\]/", $result3, $matches3);
$result3 = preg_replace(array("/\[STARTDATA\]/", "/\[ENDDATA\]/"), array("", ""), $matches3[0]);

$file3 = '/var/www/html/custom/partspros/partspros2015/imgdata.csv';
$fh3 = fopen($file3, 'w+');
fwrite($fh3, $result3);
rewind($fh3);
fclose($fh3);

$fh3 = fopen($file3, 'r');

if (fgetcsv($fh3, 2000, ',', '"') !== FALSE) {
    mysqli_query($connect, 'TRUNCATE TABLE storedata');
    rewind($fh3);
    while (($data3 = fgetcsv($fh3, 2000, ',', '"')) !== FALSE) {
        $count3 = count($data3);
        $query3 = "INSERT INTO storedata (add_to_cart_image, made_in_usa_image, more_info_image, notify_me_image, out_of_stock_image, storeid) VALUES (";
        
        for ($x = 0; $x < $count3; $x++) {
            $query3 .= '"';
            $query3 .= mysqli_real_escape_string($connect, $data3[$x]);
            $query3 .= '"';
            if ($x != count($data3) - 1) {
                $query3 .= ",";
            }
        }
        
        $query3 .= ');';
        
        //echo "<code>$query3</code>";
        
        if (!mysqli_query($connect, $query3)) {
            array_push($errors, mysqli_error($connect));
        }
    }
} else {
    array_push($errors, 'Could not read store info feed. Table not updated.');
}

fclose($fh3);
unset($data3, $result3);

/*
    Categories and Subcategories
*/


$url4 = "http://yhst-141660816666872.stores.yahoo.net/categories-feed-2.html";

$ch4 = curl_init();
curl_setopt($ch4, CURLOPT_URL, $url4);
curl_setopt($ch4, CURLOPT_RETURNTRANSFER, true);
$result4 = curl_exec($ch4);
curl_close($ch4);

$result4 = preg_match("/\[[^>]*\]/", $result4, $matches4);
$result4 = preg_replace(array("/\[STARTDATA\]/", "/\[ENDDATA\]/"), array("", ""), $matches4[0]);

$file4 = '/var/www/html/custom/partspros/partspros2015/categories.csv';
$fh4 = fopen($file4, 'w+');
fwrite($fh4, $result4);
rewind($fh4);
fclose($fh4);

$fh4 = fopen($file4, 'r');

if (fgetcsv($fh4, 5000, ',', '"') !== FALSE) {
    mysqli_query($connect, 'TRUNCATE TABLE categories');
    rewind($fh4);
    while (($data4 = fgetcsv($fh4, 5000, ',', '"')) !== FALSE) {
        $count4 = count($data4);
        $query4 = "INSERT INTO categories (category, sub_category, note, no_match_text, above_text, below_text) VALUES (";
        
        for ($y = 0; $y < $count4; $y++) {
            $query4 .= '"';
            $query4 .= mysqli_real_escape_string($connect, $data4[$y]);
            $query4 .= '"';
            if ($y != count($data4) - 1) {
                $query4 .= ",";
            }
        }
        
        $query4 .= ');';
        
        //echo "<code>$query4</code>";
        
        if (!mysqli_query($connect, $query4)) {
            array_push($errors, mysqli_error($connect));
        }
    }
} else {
    array_push($errors, 'Could not read store categories feed. Table not updated.');
}

fclose($fh4);
unset($data4, $result4);

/*
    Stock updates
*/

$stockQuery = "select distinct stock_notification_requests.*, items.orderable from stock_notification_requests, items where stock_notification_requests.id = items.item_id and stock_notification_requests.notification_date = '' and items.orderable = 'Y';";

$stockQueryResults = mysqli_query($connect, $stockQuery);

while($row = mysqli_fetch_object($stockQueryResults))
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

    //mail($to, $subject, $message, $headers);


    // clear out requests we just sent email for

    $now = date("M j Y");

    $stockQuery2 = "update stock_notification_requests ".
        "set notification_date=\"$now\" ".
        "WHERE email=\"$row->email\" AND id=\"$row->id\"";
    //mysqli_query($connect, $stockQuery2);
    
    
    echo "<p>Sent notification to $row->name ($row->email) for product [$row->product]</p>\r\n";
}

if (count($errors) === 0) {
    echo 'Import complete.';
} else {
    echo 'There were some problems inserting some data: <br><br><code>';
    for ($z = 0; $z < count($errors); $z++) {
        echo $errors[$z] . '<br>';
    }
    echo '</code>';
}
unset($errors);

?>