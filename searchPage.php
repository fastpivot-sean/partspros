<?php
    header('Content-Type: text/javascript');
    
    require_once('../config.php');
    require_once(CLASS_DIRECTORY . 'class.queryDB.php');
    
    $connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
    mysqli_select_db($connect, DB_NAME);
    $qdb = new queryDB($connect);
    
    $q = explode("?", $_SERVER['HTTP_REFERER']); // Do we have a query string?

    if (count($q) > 1 && (preg_match('/search-results.html/', $q[0]) || preg_match('/category-results.html/', $q[0]))) {
        
        $qstring = parse_str($q[1], $res);

        if (isset($res['model']) || isset($res['code'])) {
            
            if (isset($res['code'])) {
            
                $code = $res['code'];
                $startPoint = (isset($res['start'])) ? $res['start'] : '0';
                
                echo "PARTSPROS.runQuery('" . SEARCH_BY_CODE . "', ";
                echo "{code: '$code', start: '$startPoint'}); \n";
            
            } else if (isset($res['subcategory'])) {
                
                $year = $res['year'];
                $make = $res['make'];
                $model = $res['model'];
                $category = $res['category'];
                $subcat = $res['subcategory'];
                $startPoint = (isset($res['start'])) ? $res['start'] : '0';
                
                echo "PARTSPROS.runQuery('" . CAT_SELECT . "', ";
                echo "{year: '$year', make: '$make', model: '$model', category: '$category', subcategory: '$subcat', start: '$startPoint'});";
                
            } else if (isset($res['singlecat'])) {
            
                $year = $res['year'];
                $make = $res['make'];
                $model = $res['model'];
                $category = $res['singlecat'];
                $startPoint = (isset($res['start'])) ? $res['start'] : '0';
                
                echo "PARTSPROS.runQuery('" . GET_SINGLE_CAT . "', ";
                echo "{year: '$year', make: '$make', model: '$model', category: '$category', start: '$startPoint'}); \n";
            
            } else if (isset($res['allsubcats'])) {
            
                $year = $res['year'];
                $make = $res['make'];
                $model = $res['model'];
                $category = $res['allsubcats'];
                $startPoint = (isset($res['start'])) ? $res['start'] : '0';
                
                echo "PARTSPROS.runQuery('" . GET_ALL_ITEMS_IN_CAT . "', ";
                echo "{year: '$year', make: '$make', model: '$model', category: '$category', start: '$startPoint'}); \n";
            
            } else if (isset($res['allcats'])) {
            
                $year = $res['year'];
                $make = $res['make'];
                $model = $res['model'];
                $startPoint = (isset($res['start'])) ? $res['start'] : '0';
                
                echo "PARTSPROS.runQuery('" . FULL_SELECT . "', ";
                echo "{year: '$year', make: '$make', model: '$model', category: '$category', start: '$startPoint'}); \n";
            
            } else {
            
                $year = $res['year'];
                $make = $res['make'];
                $model = $res['model'];
                
                echo "PARTSPROS.runQuery('" . GET_CATEGORIES . "', ";
                echo "{year: '$year', make: '$make', model: '$model'}); \n";
                
            }
        } else if (isset($res['subcategory'])) {
        
            $subcat = $res['subcategory'];
            $startPoint = (isset($res['start'])) ? $res['start'] : '0';
            
            echo "PARTSPROS.runQuery('" . GET_UNIVERSAL_PARTS . "', ";
            echo "{subcategory: '$subcat', start: '$startPoint'});";
            
        }
    }
?>