<?php

class queryDB {

    private $years = array();
    private $makes = array();
    private $models = array();
    private $items = array();
    private $numItems = array();
    private $categories = array();
    private $categoryItemCount = array();
    private $categoryText = array();
    private $notes = array();
    private $startPoint = 0;
    private $limit = 0;
    private $totalItems = 0;
    private $queryString = '';
    private $searchType = '';
    private $connect = null; //handle to mysql db
    
    private $storeData = array(
        'add_to_cart_image' => '',
        'made_in_usa_image' => '',
        'more_info_image' => '',
        'notify_me_image' => '',
        'out_of_stock_image' => '',
        'storeId' => ''
    );
    
    private $currentSearch = array(
        'year' => '',
        'make' => '',
        'model' => '',
        'category' => '',
        'subcategory' => '',
        'start' => '',
        'code' => ''
    );
    
    private function resetArray($ref) {
        $this->$ref = null;
        unset($this->$ref);
        $this->$ref = array();
    }
    
    private function getCategoryList() {
        $db = $this->connect;
        $query = "select distinct category from categories order by category asc;";
        $result = mysqli_query($db, $query);
        while($row = mysqli_fetch_assoc($result)) {
            $tempArr = array();
            $query2 = "select sub_category, note from categories where category = '". $row['category'] . "' order by sub_category asc;";
            $result2 = mysqli_query($db, $query2);
            while($row2 = mysqli_fetch_assoc($result2)) {
                $tempArr2 = array();
                $tempArr2['sub_category'] = $row2['sub_category'];
                $tempArr2['note'] = $row2['note'];
                array_push($tempArr, $tempArr2);
            }
            
            $this->categories[$row['category']] = $tempArr;
        }
    }
    
    public function  __construct($connect) {
    
        if (!$this->connect = $connect) {
            throw new Exception('No mysql connection');
        }
        
        $db = $this->connect;
        
        $query = "select distinct start_year from variations order by start_year asc limit 1;";
        $results = mysqli_query($db, $query);

        $startyear = intval(mysqli_fetch_object($results)->start_year);

        $query2 = "select distinct end_year from variations order by end_year desc limit 1;";
        $results2 = mysqli_query($db, $query2);
        $endyear = intval(mysqli_fetch_object($results2)->end_year);

        for($i = $endyear; $i >= $startyear; $i--) {
            array_push($this->years, $i);
        }
        
        $this->getCategoryList();
    }
 
    public function getMakesFromYear($year, $returnQuery) {
        $this->resetArray('makes');
        $db = $this->connect;
        $year = mysql_real_escape_string($year);
        $query = "select distinct make from variations where start_year <= '$year' and end_year >= '$year' order by make asc;";
        $result = mysqli_query($db, $query);
        while($row = mysqli_fetch_assoc($result)) {
            array_push($this->makes, $row['make']);
        }
        if ($returnQuery == true) {
            return $result; 
        } else {
            return $this->makes;
        } 
    }
    
    public function getModelsFromMakes($make, $year, $returnQuery) {
        $this->resetArray('models');
        $db = $this->connect;
        $make = mysql_real_escape_string($make);
        $year = mysql_real_escape_string($year);
        $query = "select distinct model from variations where make = '$make' and start_year <= $year and end_year >= $year order by model asc;";
        $result = mysqli_query($db, $query);
        while($row = mysqli_fetch_assoc($result)) {
            array_push($this->models, $row['model']);
        }
        if ($returnQuery == true) {
            return $result; 
        } else {
            return $this->models;
        } 
    }
    
    public function getNotes($code, $make, $model, $year) {
        $this->resetArray('notes');
        $code = mysql_real_escape_string($code);
        $db = $this->connect;
        $query = "select * from variations where code = '$code' and make = '$make' and model = '$model' and start_year <= $year and end_year >= $year order by variations.code asc";
        $result = mysqli_query($db, $query);
        while($row = mysqli_fetch_assoc($result)) {
            array_push($this->notes, $row);
        }
        
        return $this->notes;
    }
    
    public function getUniversalParts($subcat, $startPoint, $limit) {
        $this->resetArray('items');
        $subcat = mysql_real_escape_string($subcat);
        $startPoint = mysql_real_escape_string($startPoint);
        $limit = mysql_real_escape_string($limit);
        $db = $this->connect;
        $query = "select * from items inner join variations on items.code = variations.code where category = 'universal parts' and sub_category = '$subcat' order by items.code asc";
        $result = mysqli_query($db, $query);
        while($row = mysqli_fetch_assoc($result)) {
            array_push($this->items, $row);
        }
        
        $query = "select count(1) as 'items' from items inner join variations on items.code = variations.code where category = 'universal parts' and sub_category = '$subcat'";
        $result = mysqli_query($db, $query);
        
        $this->limit = $limit;
        $this->numItems = count($this->items);
        $this->searchType = UNIVERSAL_PART_SEARCH;
        
        $this->currentSearch['year'] = "";
        $this->currentSearch['make'] = "";
        $this->currentSearch['model'] = "";
        $this->currentSearch['category'] = "Universal Parts";
        $this->currentSearch['subcategory'] = $subcat;
        $this->currentSearch['start'] = $startPoint;
        
        while($row = mysqli_fetch_assoc($result)) {
            $this->totalItems = $row['items'];
        }
        
        return $this->items;
    }
    
    public function getItems($year, $make, $model, $startPoint, $limit) {
        $db = $this->connect;
        $year = mysql_real_escape_string($year);
        $make = mysql_real_escape_string($make);
        $model = mysql_real_escape_string($model);
        $startPoint = mysql_real_escape_string($startPoint);
        $limit = mysql_real_escape_string($limit);
        $query = "select * from variations inner join items on variations.code = items.code where start_year <= $year and end_year >= $year and make = '$make' and model = '$model' order by variations.code asc limit $startPoint, $limit;";
        $result = mysqli_query($db, $query);
        while($row = mysqli_fetch_assoc($result)) {
            array_push($this->items, $row);
        }
        $query = "select count(1) as 'items' from variations where start_year <= $year and end_year >= $year and make = '$make' and model = '$model';";
        $result = mysqli_query($db, $query);
        
        $this->limit = $limit;
        $this->numItems = count($this->items);
        $this->searchType = FULL_SEARCH;
        
        $this->currentSearch['year'] = $year;
        $this->currentSearch['make'] = $make;
        $this->currentSearch['model'] = $model;
        $this->currentSearch['category'] = ALL_ITEMS;
        $this->currentSearch['start'] = $startPoint;
        
        while($row = mysqli_fetch_assoc($result)) {
            $this->totalItems = $row['items'];
        }
        return $this->items;
    }
    
    public function getByCode($code, $startPoint, $limit) {
        $db = $this->connect;
        $code= mysql_real_escape_string($code);
        $startPoint = mysql_real_escape_string($startPoint);
        $limit = mysql_real_escape_string($limit);
        //$query = "select * from items where items.code = '$code' order by code asc limit $startPoint, $limit;";
        $query = "select * from items inner join variations on items.code = variations.code where items.code like '%$code%' or items.name like '%$code%' or variations.note like '%$code%' or items.mid_description like '%$code%' or items.bullet_point_01 like '%$code%' or items.bullet_point_02 like '%$code%' or items.bullet_point_03 like '%$code%' or items.bullet_point_04 like '%$code%' or items.bullet_point_05 like '%$code%' or items.bullet_point_06 like '%$code%' or items.bullet_point_07 like '%$code%' or items.bullet_point_08 like '%$code%' or items.bullet_point_09 like '%$code%' or items.bullet_point_10 like '%$code%' or items.bullet_point_11 like '%$code%' or items.bullet_point_12 like '%$code%' group by items.code limit $startPoint, $limit;";
        $result = mysqli_query($db, $query);
        while($row = mysqli_fetch_assoc($result)) {
            array_push($this->items, $row);
        }
        $query = "select count(distinct items.code) as 'items' from items inner join variations on items.code = variations.code where items.code like '%$code%' or items.name like '%$code%' or variations.note like '%$code%' or items.mid_description like '%$code%' or items.bullet_point_01 like '%$code%' or items.bullet_point_02 like '%$code%' or items.bullet_point_03 like '%$code%' or items.bullet_point_04 like '%$code%' or items.bullet_point_05 like '%$code%' or items.bullet_point_06 like '%$code%' or items.bullet_point_07 like '%$code%' or items.bullet_point_08 like '%$code%' or items.bullet_point_09 like '%$code%' or items.bullet_point_10 like '%$code%' or items.bullet_point_11 like '%$code%' or items.bullet_point_12 like '%$code%'";
        $result = mysqli_query($db, $query);
        
        $this->startPoint = $startPoint;
        $this->limit = $limit;
        $this->numItems = count($this->items);
        $this->currentSearch['code'] = $code;
        $this->searchType = CODE_SEARCH;
        while($row = mysqli_fetch_assoc($result)) {
            $this->totalItems = $row['items'];
        }

        return $this->items;
    }
    
    
    public function getCategoryText($cat, $subcat) {
        $db = $this->connect;
        $cat = mysql_real_escape_string($cat);
        $subcat = mysql_real_escape_string($subcat);
        $query = "select * from categories where category = '$cat' and sub_category = '$subcat';";
        $result = mysqli_query($db, $query);
        $row = mysqli_fetch_assoc($result);
        $this->resetArray('categoryText');
        array_push($this->categoryText, $row);
        return $this->categoryText;
    }
    
    public function getAllItemsInCat($year, $make, $model, $cat, $start, $limit) {
        $db = $this->connect;
        $year = mysql_real_escape_string($year);
        $make = mysql_real_escape_string($make);
        $model = mysql_real_escape_string($model);
        $cat = mysql_real_escape_string($cat);
        $start = mysql_real_escape_string($start);
        $limit = mysql_real_escape_string($limit);
        if ($cat == 'Universal Parts') {
            $query = "select * from variations inner join items on variations.code = items.code where category = '$cat' order by variations.code asc limit $start, $limit;";
        } else {
            $query = "select * from variations inner join items on variations.code = items.code where start_year <= $year and end_year >= $year and make = '$make' and model = '$model' and category = '$cat' order by variations.code asc limit $start, $limit;";
        }
        
        $result = mysqli_query($db, $query);
        while($row = mysqli_fetch_assoc($result)) {
            array_push($this->items, $row);
        }
        if ($cat == 'Universal Parts') {
            $query = "select count(1) as 'items' from variations inner join items on variations.code = items.code where category = '$cat';";
        } else {
            $query = "select count(1) as 'items' from variations inner join items on variations.code = items.code where start_year <= $year and end_year >= $year and make = '$make' and model = '$model' and category = '$cat';";
        }
        $result = mysqli_query($db, $query);
        
        $this->limit = $limit;
        $this->numItems = count($this->items);
        $this->searchType = ALL_ITEMS_IN_CAT_SEARCH;
        
        $this->currentSearch['year'] = $year;
        $this->currentSearch['make'] = $make;
        $this->currentSearch['model'] = $model;
        $this->currentSearch['start'] = $startPoint;
        $this->currentSearch['category'] = $cat;
        $this->currentSearch['subcategory'] = "";
        
        while($row = mysqli_fetch_assoc($result)) {
            $this->totalItems = $row['items'];
        }
        return $this->items;
    }
    
    public function getItemsWithCat($year, $make, $model, $cat, $subcat, $start, $limit) {
        $db = $this->connect;
        $year = mysql_real_escape_string($year);
        $make = mysql_real_escape_string($make);
        $model = mysql_real_escape_string($model);
        $cat = mysql_real_escape_string($cat);
        $subcat = mysql_real_escape_string($subcat);
        $start = mysql_real_escape_string($start);
        $limit = mysql_real_escape_string($limit);
        $query = "select * from variations inner join items on variations.code = items.code where start_year <= $year and end_year >= $year and make = '$make' and model = '$model' and category = '$cat' and sub_category = '$subcat' order by variations.code asc limit $start, $limit;";

        $result = mysqli_query($db, $query);
        while($row = mysqli_fetch_assoc($result)) {
            array_push($this->items, $row);
        }
        $query = "select count(1) as 'items' from variations inner join items on variations.code = items.code where start_year <= $year and end_year >= $year and make = '$make' and model = '$model' and category = '$cat' and sub_category = '$subcat';";
        $result = mysqli_query($db, $query);
        
        $this->limit = $limit;
        $this->numItems = count($this->items);
        $this->searchType = ITEM_CAT_SEARCH;
        
        $this->currentSearch['year'] = $year;
        $this->currentSearch['make'] = $make;
        $this->currentSearch['model'] = $model;
        $this->currentSearch['start'] = $startPoint;
        $this->currentSearch['category'] = $cat;
        $this->currentSearch['subcategory'] = $subcat;
        
        while($row = mysqli_fetch_assoc($result)) {
            $this->totalItems = $row['items'];
        }
        return $this->items;
    }
    
    public function getSingleCat($year, $make, $model, $cat) {
        $db = $this->connect;
        $year = mysql_real_escape_string($year);
        $make = mysql_real_escape_string($make);
        $model = mysql_real_escape_string($model);
        $cat = mysql_real_escape_string($cat);
        
        $this->currentSearch['year'] = $year;
        $this->currentSearch['make'] = $make;
        $this->currentSearch['model'] = $model;
        $this->currentSearch['category'] = $cat;
        
        $this->searchType = SINGLE_CAT_SEARCH;
        
        $this->resetArray('categoryItemCount');
        $this->categoryItemCount[$cat] = array();

        $subcats = $this->categories[$cat];
        
        foreach ($subcats as $sub) {
            $subcategory = $sub['sub_category'];
            $query = "select count(1) as 'count' from variations inner join items on variations.code = items.code where make = '$make' and model = '$model' and start_year <= $year and end_year >= $year and category = '$cat' and sub_category = '$subcategory';";
            $result = mysqli_query($db, $query);
            $row = mysqli_fetch_assoc($result);
            $this->categoryItemCount[$cat][$subcategory]['count'] = $row['count'];
            $this->categoryItemCount[$cat][$subcategory]['note'] = $sub['note'];
        }
        return $this->categoryItemCount;
    }
    
    public function getCategoriesByItem($year, $make, $model) {
        $db = $this->connect;
        $year = mysql_real_escape_string($year);
        $make = mysql_real_escape_string($make);
        $model = mysql_real_escape_string($model);

        $this->currentSearch['year'] = $year;
        $this->currentSearch['make'] = $make;
        $this->currentSearch['model'] = $model;
        
        $this->resetArray('categoryItemCount');
        foreach ($this->categories as $cat => $subcats) {

            $this->categoryItemCount[$cat] = array();
            foreach ($subcats as $sub) {
                $subcategory = $sub['sub_category'];
                $query = "select count(1) as 'count' from variations inner join items on variations.code = items.code where make = '$make' and model = '$model' and start_year <= $year and end_year >= $year and category = '$cat' and sub_category = '$subcategory';";
                $result = mysqli_query($db, $query);
                $row = mysqli_fetch_assoc($result);
                $this->categoryItemCount[$cat][$subcategory]['count'] = $row['count'];
                $this->categoryItemCount[$cat][$subcategory]['note'] = $sub['note'];
            }
        }
        return $this->categoryItemCount;
    }
    
    public function setStoreVars() {
        $db = $this->connect;
        $query = "select * from storedata;";
        $result = mysqli_query($db, $query);
        $row = mysqli_fetch_assoc($result);
        $this->storeData['add_to_cart_image'] = $row['add_to_cart_image'];
        $this->storeData['made_in_usa_image'] = $row['made_in_usa_image'];
        $this->storeData['more_info_image'] = $row['more_info_image'];
        $this->storeData['notify_me_image'] = $row['notify_me_image'];
        $this->storeData['out_of_stock_image'] = $row['out_of_stock_image'];
        $this->storeData['storeid'] = $row['storeid'];
    }
    
    public function setQueryString($q) {
        $this->queryString = $q;
    }
    
    public function getStoreVars() {
        return $this->storeData;
    }
    
    public function getSearchType() {
        return $this->searchType;
    }
    
    public function getYears() {
        return $this->years;
    }
    
    public function getMakes() {
        return $this->makes;
    }
    
    public function getModels() {
        return $this->models;
    }
    
    public function getCount() {
        return $this->numItems;
    }
    
    public function getCurrentSearch() {
        return $this->currentSearch;
    }
    
    public function getItemRange($itemsPerPage) {
    
        $year = $this->currentSearch['year'];
        $make = $this->currentSearch['make'];
        $model = $this->currentSearch['model'];
        $start = $this->startPoint;
        $cat = $this->currentSearch['category'];
        $subcat = $this->currentSearch['subcategory'];
        $code = $this->currentSearch['code'];
        
        echo "/* $start */ \n";
        if ($this->numItems + intval($itemsPerPage) > $this->totalItems) {
            $end = $this->totalItems;
        } else {
            $end = intval($start) + intval($itemsPerPage);
        }
        $ret = '';
        
        if ($this->numItems == 0) {
            return (isset($this->categoryText[0]['no_match_text'])) ? $this->categoryText[0]['no_match_text'] : "No items found that match your search parameters.";
        } else {
            
            if ($start > 0) {
  
                $qStart = intval($start) - intval($itemsPerPage);
                if ($qStart < 0) {
                    $qStart = 0;
                }
                
                switch($this->searchType) {
                
                    case CODE_SEARCH:
                        $newQuery = "code=$code&start=$qStart";
                    break;
                        
                    case UNIVERSAL_PART_SEARCH:
                        $newQuery = "subcategory=$subcat&start=$qStart";
                    break;
                    
                    case FULL_SEARCH:
                        $newQuery = "allcats=true&year=$year&make=$make&model=$model&start=$qStart";
                    break;
                    
                    case ITEM_CAT_SEARCH:
                        $newQuery = "year=$year&make=$make&model=$model&start=$qStart";
                    break;
                    
                    case SINGLE_CAT_SEARCH:
                        $newQuery = "year=$year&make=$make&model=$model&singlecat=$cat&start=$qStart";
                    break;
                    
                    case ALL_ITEMS_IN_CAT_SEARCH:
                        $newQuery = "year=$year&make=$make&model=$model&allsubcats=$cat&start=$qStart";
                    break;
                
                }
                
                $ret .= "<a href=\"search-results.html?" . $newQuery . "\">Previous</a> ";
            }
            
            $ret .= "<span class=\"itemSet\">Showing item(s) " . intval($start + 1) . " - $end of $this->totalItems.</span>";
            
            if ($end < $this->totalItems) {
 
                $qStart2 = $end;
                
                switch($this->searchType) {
                
                    case CODE_SEARCH:
                        $newQuery = "code=$code&start=$qStart2";
                    break;
                        
                    case UNIVERSAL_PART_SEARCH:
                        $newQuery = "subcategory=$subcat&start=$qStart2";
                    break;
                    
                    case FULL_SEARCH:
                        $newQuery = "allcats=true&year=$year&make=$make&model=$model&start=$qStart2";
                    break;
                    
                    case ITEM_CAT_SEARCH:
                        $newQuery = "year=$year&make=$make&model=$model&start=$qStart2";
                    break;
                    
                    case SINGLE_CAT_SEARCH:
                        $newQuery = "year=$year&make=$make&model=$model&singlecat=$cat&start=$qStart2";
                    break;
                    
                    case ALL_ITEMS_IN_CAT_SEARCH:
                        $newQuery = "year=$year&make=$make&model=$model&allsubcats=$cat&start=$qStart2";
                    break;
                }
                
                $ret .= " <a href=\"search-results.html?" . $newQuery . "\">Next</a>";
            }

            return $ret;
        }
    }
}

?>

