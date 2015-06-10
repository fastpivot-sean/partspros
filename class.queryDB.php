<?php

class queryDB {

    public $years = [];
    
    public function  __construct() {
        $query = "select distinct start_year from variations order by start_year asc limit 1;";
        $results = mysql_query($query);

        $startyear = intval(mysql_fetch_object($results)->start_year);

        $query2 = "select distinct end_year from variations order by end_year desc limit 1;";
        $results2 = mysql_query($query2);
        $endyear = intval(mysql_fetch_object($results2)->end_year);

        for($i = $startyear; $i <=$endyear; $i++) {
            array_push($this->years, $i);
        }
    }
    public function searchYears($startyear, $endyear) {
        $query = "select distinct make from variations where start_year >= '$startyear' and end_year <= '$endyear';";
        return mysql_query($query);
    }
    
    public function searchMakes($make) {
        $query = "select distinct model from variations where make = '$make';";
        return mysql_query($query);
    }
    
    public function searchItems($code) {
        $query = "select * from items where code = '$code'";
        return mysql_query($query);
    }
    
    public function searchVariations($code) {
        $query = "select * from variations where code = '$code'";
        return mysql_query($query);
    }
}

?>

