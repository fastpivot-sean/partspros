<?php 

    $connect = mysql_connect("localhost","root","");
    mysql_select_db("parts_pros",$connect);
    
    $url = "http://yhst-141660816666872.stores.yahoo.net/item-data-feed.html";
    
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
        $query = 'INSERT INTO items (code, path, category, name, price, sale_price,
                                     orderable, caption, ship_weight,
                                     mid_description, long_description, warranty, usa_made, 
                                     options, fp_cross_sell_ids,
                                     image, height, width,
                                     inset_01, inset_01_thumb, inset_02, inset_02_thumb,
                                     inset_03, inset_03_thumb, 
                                     notes_01, notes_02, notes_03, notes_04, notes_05,
                                     notes_06, notes_07, notes_08, notes_09, notes_10, 
                                     notes_11, notes_12, notes_13, notes_14, notes_15, 
                                     notes_16, notes_17, notes_18, notes_19, notes_20, 
                                     bullet_point_01, bullet_point_02, bullet_point_03, bullet_point_04,
                                     bullet_point_05, bullet_point_06, bullet_point_07, bullet_point_08,
                                     bullet_point_09, bullet_point_10, bullet_point_11, bullet_point_12
                                    ) VALUES (';
                                    
        for ($j = 0; $j < count($data); $j++) {
            $query .= '"';
            $query .= mysql_real_escape_string($data[$j]);
            $query .= '"';
            if ($j != count($data) - 1) {
                $query .= ",";
            }
        }
        
        $query .= ');';
        
        echo $query . '<br><br>';
       
        mysql_query($query);
        
        echo "Items imported.";
        echo mysql_error() . '<br>';

    }


?>