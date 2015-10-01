<?php
header('Content-Type: text/javascript');

    require_once('../config.php');

    if(isset($_GET['action'])) {
    
        require_once(CLASS_DIRECTORY . 'class.queryDB.php');
        $connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
        mysqli_select_db($connect, DB_NAME);
        $qdb = new queryDB($connect);
        
        switch ($_GET['action']) {
            
            case 'yearselected':
                if (isset($_GET['year']) && isset($_GET['year']) !== '0') {
                    $makes = $qdb->getMakesFromYear($_GET['year'], false);
                    $count = count($makes);
                    echo "PARTSPROS.updateSelect('year-make', [";
                    for ($i = 0; $i < $count; $i++) {
                        echo "'" . $makes[$i] . "'";
                    }
                    if($i < $count - 1) {
                        echo ',';
                    }
                    echo "]);";
                } else {
                
                }
            break;
            
            case 'makeselected':
                $models = $qdb->getMakesFromYear($_GET['model'], false);
                $year = $_GET['year'];
                $count = count($makes);
                echo "PARTSPROS.updateSelect('year-model', [";
                for ($i = 0; $i < $count; $i++) {
                    echo "'" . $models[$i] . "'";
                }
                if($i < $count - 1) {
                    echo ',';
                }
                echo "]);";
            break;
            
            case 'modelselected':
            break;
            
            default:
                echo "";
        
        }
    
    } else {
        die;
    }

?>