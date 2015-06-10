<?php 

    $connect = mysql_connect("localhost","root","");
    mysql_select_db("parts_pros",$connect);
    
    $url = "http://yhst-141660816666872.stores.yahoo.net/item-var-feed.html";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    
    $result = preg_match("/\[[^>]*\]/", $result, $matches);
    $result = preg_replace(array("/\[STARTDATA\]/", "/\[ENDDATA\]/"), array("", ""), $matches[0]);

    $csv = str_getcsv($result, "\n");
    
    for ($i = 0; $i < count($csv); $i++) {
    
        $data = str_getcsv($csv[$i], ",", "\"");
        $query = "INSERT INTO variations (code, make, model, start_year, end_year) VALUES (";
                                    
        for ($j = 0; $j < count($data); $j++) {
            $query .= '"';
            $query .= addslashes($data[$j]);
            $query .= '"';
            if ($j != count($data) - 1) {
                $query .= ",";
            }
        }
        
        $query .= ');';
        
        echo $query . '<br><br>';
       
        mysql_query($query);

    }


?>