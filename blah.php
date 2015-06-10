do {
            if (isset($data[0])) {
                $count++;
                $arrsize = count($data);
                $query = "INSERT INTO items (code, path, category, name, price, sale_price, ship_weight,
                                             mid_description, long_description, warranty, usa_made, fp_cross_sell_ids,
                                             model_01, model_02, model_03, model_04, model_05,
                                             model_06, model_07, model_08, model_09, model_10, 
                                             model_11, model_12, model_13, model_14, model_15, 
                                             model_16, model_17, model_18, model_19, model_20, 
                                             notes_01, notes_02, notes_03, notes_04, notes_05,
                                             notes_06, notes_07, notes_08, notes_09, notes_10, 
                                             notes_11, notes_12, notes_13, notes_14, notes_15, 
                                             notes_16, notes_17, notes_18, notes_19, notes_20, 
                                             bullet_point_1, bullet_point_2, bullet_point_3, bullet_point_4,
                                             bullet_point_5, bullet_point_6, bullet_point_7, bullet_point_8,
                                             bullet_point_9, bullet_point_10, bullet_point_11, bullet_point_12
                                            ) VALUES (";
                for ($i=0; $i < $arrsize; $i++) {
                    $query .= addslashes($data[$i]);
                    if ($i != $arrsize - 1) {
                        $query .= ', ';
                    }
                }
                
                $query .= ");";
                echo '<br><br>' . $query;
                
                if($count == 5) {
                    break;
                    
                }
                //mysql_query($query);
            }
        } while ($data = str_getcsv($result,5000,",","\""));
        
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Import a CSV File with PHP & MySQL</title>
</head>

<body>

<?php if (!empty($_GET['success'])) { echo "<b>Your file has been imported.</b><br><br>"; } //generic success notice ?>

<form action="" method="post" enctype="multipart/form-data" name="form1" id="form1">
  Choose your file: <br />
  <input name="csv" type="file" id="csv" />
  <input type="submit" name="Submit" value="Submit" />
</form>

</body>
</html>