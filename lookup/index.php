<?php
header('Content-Type: text/javascript');

    require_once('../config.php');
    
    function generateTemplate($idx, $item, $qdb, $year) {
        $storeVars = $qdb->getStoreVars();
        $notes = $qdb->getNotes($item['code'], $item['make'], $item['model'], $year);
        $str = "";
        $str .= "<div class=\"itemRow\" id=\"item-" . $item['code'] . "\">";
        $str .= "<div class=\"itemImage\"><a id=\"mainImg-" . $item['code'] . "\" href=\"" . $item['image'] . "\" rel=\"prettyPhoto[gal_" . $idx . "]\">";
        $str .= "<img src=\"". $item['image'] ."\" width=\"200\" alt=\"" . $item['name'] . "\" /></a>";
        for ($i = 0; $i < 13; $i++) {
            $j = ($i < 10) ? "inset_0" . $i . "_thumb" : "inset_" . $i . "_thumb";
            $k = ($i < 10) ? "inset_0" . $i : "inset_" . $i;
            $str .= ($item[$k]) ? "<a class=\"insetLink\" href=\"" . $item[$k] . "\" rel=\"prettyPhoto[gal_" . $idx . "]\"><img src=\"" . $item['$j'] . "\" alt=\"" . $item['name'] . "\" /></a>" : "";
        }
        //$str .= ($item['inset_01']) ? "<a class=\"insetLink\" href=\"" . $item['inset_01'] . "\" rel=\"prettyPhoto[gal_" . $idx . "]\"><img src=\"" . $item['inset_01_thumb'] . "\" alt=\"" . $item['name'] . "\" /></a>" : "";
        //$str .= ($item['inset_02']) ? "<a class=\"insetLink\" href=\"" . $item['inset_02'] . "\" rel=\"prettyPhoto[gal_" . $idx . "]\"><img src=\"" . $item['inset_02_thumb'] . "\" alt=\"[name]\" /></a>" : "";
        //$str .= ($item['inset_03']) ? "<a class=\"insetLink\" href=\"" . $item['inset_03'] . "\" rel=\"prettyPhoto[gal_" . $idx . "]\"><img src=\"" . $item['inset_03_thumb'] . "\" alt=\"[name]\" /></a>" : "";
        $str .= "<a href=\"#\" class=\"moreImageLink\" rel=\"mainImg-" . $item['code'] . "\">[see more images]</a>";
        $str .= ($item['usa_made'] == "Y") ? "<img class=\"madeinusa\" src=\"" . $storeVars['made_in_usa_image'] . "\" alt=\"Made in the USA\" />" : "";
        $str .= "</div>";

        $str .= "<div class=\"itemInfo\"><p class=\"itemName\"><a href=\"" . $item['item_id'] . ".html\">" . $item['name'] . "</a></p>";
        $str .= "<p class=\"itemNumber\">Item # " . $item['code'] . "</p>";
        $str .= "<ul>";
        for ($i = 0; $i < 13; $i++) {
            $k = ($i < 10) ? "bullet_point_0" . $i : "bullet_point_" . $i;
            $str .= ($item[$k] != "") ? "<li>" . $item[$k] . "</li>" : "";
        }
        
        $str .= "</ul>";
        if($qdb->getSearchType() != UNIVERSAL_PART_SEARCH) {
            $str .= "<ul class=\"notes\">";
            $str .= $item['note'];
        }
        $str .= "</ul>";
        $str .= ($item['mid_description'] != "") ? "<p class=\"med-desc\">" . $item['mid_description'] . "</p>" : "";
        $str .= "</div>";
        $str .= "<div class=\"itemOrder\">";
        if ($item['sale_price'] != "0.00") {
            $str .= "<p class=\"price\">$" . $item['price'] . "</p><p class=\"saleprice\">$" . $item['sale_price'] . "</p>";
        } else {
            $str .= "<p class=\"price-bold\">$" . $item['price'] . "</p>";
        }
        $str .= "<p class=\"free-ship\">Free Shipping</p>";
        
        if ($item['orderable'] == "Y") {
            $str .= "<form method=\"post\" action=\"http://order.store.yahoo.net/" . $storeVars['storeid'] . "/cgi-bin/wg-order?" . $storeVars['storeid'] . "\">";
            $str .= ($item['options'] != "") ? "<div class=\"optionWrap\">" . html_entity_decode($item['options']) . "</div>" : "";
            $str .= "<input type=\"image\" alt=\"Add to cart\" src=\"" . $storeVars['add_to_cart_image'] . "\" />";
            $str .= "<a href=\"" . $item['item_id'] . ".html\"><img src=\"" . $storeVars['more_info_image'] . "\" alt=\"More Info\" /></a>";
            $str .= "<input type=\"hidden\" name=\"vwitem\" value=\"" . $item['item_id'] . "\" />";
            $str .= "<input type=\"hidden\" name=\"vwcatalog\" value=\"" . $storeVars['storeId'] . "\" />";
            $str .= "</form>";
        } else {
            $str .= "<img src=\"" . $storeVars['out_of_stock_image'] . "\" alt=\"Sold Out\" />";
            $str .= "<a href=\"http://fastpivotsoftware.com/custom/partspros/partspros2015/product-request.php?product=" . $item['name'] . "&id=" . $item['item_id'] . "&iframe=true&height=300&width=460\" rel=\"prettyPhoto\">";
            $str .= "<img class=\"notifyBtn\" src=\"" . $storeVars['notify_me_image'] . "\" alt=\"Notify me when available\" />";
            $str .= "</a>";
        }
        $str .= "</div></div>";
        return $str;    
    }
    
    function printResults($qdb, $items, $categories, $categoryText) {
    
        $total = $qdb->getCount();
        
        $numItems = $qdb->getItemRange(ITEMS_PER_PAGE);
        $currSearch = $qdb->getCurrentSearch();
        $year = $currSearch['year'];
        $make = $currSearch['make'];
        $model = $currSearch['model'];
        $category = $currSearch['category'];
        $subcat = $currSearch['subcategory'];
        $code = $currSearch['code'];
        
        $returnString = "";
        if ($qdb->getSearchType() == CODE_SEARCH) {
            $returnString .= "<p class=\"title\">Showing results for ";
            $returnString .= "$code:</p>";
        } else if($qdb->getSearchType() == UNIVERSAL_PART_SEARCH) {
            $returnString .= "<p class=\"title\">Showing results for ";
            $returnString .= "<span class=\"catText\">$category</span> ";
            $returnString .= "<span class=\"bc\">&raquo;</span> <span class=\"subcatText\">$subcat</span></p>";
            if ($categoryText) {
                $returnString .= "<p id=\"topText\">" . html_entity_decode($categoryText[0]['above_text']) . "</p>";
            }
        } else {
            $returnString .= "<p class=\"title\">Showing all <span class=\"catText\">$category</span> ";
            $returnString .= ($subcat == "") ? "" : "<span class=\"bc\">&raquo;</span> <span class=\"subcatText\">$subcat</span>  ";
            $returnString .= "for ";
            $returnString .= "$year $make $model:</p>";
            if ($categoryText) {
                $returnString .= "<p id=\"topText\">" . html_entity_decode($categoryText[0]['above_text']) . "</p>";
            }
        }
        
        $returnString .= "<p class=\"numItems\">$numItems</p>";
        
        $returnString = preg_replace("/'/", "\'", $returnString);
        echo "PARTSPROS.displayResults('$returnString'); \n";
        
        $returnString = "";
        for ($k = 0; $k < $total; $k++) {
            $returnString = generateTemplate($k, $items[$k], $qdb, $year);
            $returnString = preg_replace("/'/", "\'", $returnString);
            echo "PARTSPROS.displayResults('$returnString'); \n";
        }

        $returnString = "<p class=\"numItems\">$numItems</p>";
        echo "PARTSPROS.displayResults('$returnString'); \n";
        
        //echo "PARTSPROS.displayResults('$returnString'); \n";
        
        $catString = "<div id=\"more-cats\" style=\"display: none;\"><p><span id=\"moreCats\" title=\"More Categories\">More Categories</span> For your $year $make $model:</p><ul id=\"additionalCategories\">";
        
        $showCats = false;
        
        foreach ($categories as $key => $value) {
        
            $flag = false;
            $keys = array_keys($value);
            
            $subNote = "";
            $subCount = 0;
            
            for ($j = 0; $j < count($value); $j++) {
                $subNote = trim($value[$keys[$j]]['note']);
                $subCount = $value[$keys[$j]]['count'];
                if ($subCount > 0) {
                    $flag = true;
                    break;
                }
                
            }
            if ($flag === true) {
                $liClass = (!empty($subNote)) ? "itemCatR" : "itemCat";
                if (!empty($subNote)) {
                    $catString .= "<li class=\"$liClass\"><a href=\"$subNote\">" . $key . "</a></li>";
                } else {
                    $catString .= "<li class=\"$liClass\"><a href=\"#\">" . $key . "</a></li>";
                }
                $showCats = true;
            }

        }
        
        $catString .= "</ul>";
        $catString .= "<a href=\"#\" id=\"showAll\" title=\"Show all parts\">Show all parts</a> For Your $year $make $model</div>";
        
        if ($categoryText) {
            $catString .= "<p id=\"bottomText\">" . html_entity_decode($categoryText[0]['below_text']) . "</p>";
        }
        
        echo "PARTSPROS.displayResults('$catString'); \n";
        if ($showCats === true) {
            echo "$('#more-cats').show(); \n";
            echo "$('#more-cats li.itemCat a').click(function(e) { \n";
            echo "  e.preventDefault(); \n";
            echo "  var cat = $(this).text().replace(\"&\", \"|amp|\"); \n";
            echo "  var form = $('<form method=\"get\" action=\"" . STORE_URL . "category-results.html\"></form>'); \n";
            echo "  form.append('<input type=\"hidden\" name=\"year\" value=\"$year\" />'); \n";
            echo "  form.append('<input type=\"hidden\" name=\"make\" value=\"$make\" />'); \n";
            echo "  form.append('<input type=\"hidden\" name=\"model\" value=\"$model\" />'); \n";
            echo "  form.append('<input type=\"hidden\" name=\"singlecat\" value=\"' + cat + '\" />'); \n";
            echo "  $('body').append(form); \n";
            echo "  form.submit(); \n";
            echo "}); \n";
            echo "$('a#showAll').click(function(e) { \n";
            echo "  e.preventDefault(); \n";
            echo "  var form = $('<form method=\"get\" action=\"" . STORE_URL . "search-results.html\"></form>'); \n";
            echo "  form.append('<input type=\"hidden\" name=\"year\" value=\"$year\" />'); \n";
            echo "  form.append('<input type=\"hidden\" name=\"make\" value=\"$make\" />'); \n";
            echo "  form.append('<input type=\"hidden\" name=\"model\" value=\"$model\" />'); \n";
            echo "  form.append('<input type=\"hidden\" name=\"allcats\" value=\"true\" />'); \n";
            echo "  $('body').append(form); \n";
            echo "  form.submit(); \n";
            echo "}); \n";
        }
        
        for ($z = 0; $z < $total; $z++) {
            echo "$(\"a[rel^='prettyPhoto[gal_$z]']\").prettyPhoto({deeplinking: false, social_tools:'',theme: 'light-rounded'});" . "\n";
        }
        echo "$(\"a[rel^='prettyPhoto']\").prettyPhoto({deeplinking: false, social_tools:'',theme: 'light-rounded'});" . "\n";
        echo "$('.moreImageLink').click(function(e){e.preventDefault(); $('#'+$(this).attr('rel')).trigger('click')});" . "\n";
    
    }
    
    function printCategories($qdb, $categories) {
        
        $currSearch = $qdb->getCurrentSearch();
        $year = $currSearch['year'];
        $make = $currSearch['make'];
        $model = $currSearch['model'];
        $searchType = $qdb->getSearchType();
        
        $returnString = "<p class=\"catHeading\">Select a category for your <span class=\"chosen\">$year $make $model</span>:</p>";
        
        $returnString .= "<p class=\"catAllLink\"><a href=\"#\" class=\"catShowAll\" id=\"showAll\" title=\"Show all parts\">Show all parts</a> for your $year $make $model</p></div>";
        
        foreach ($categories as $key => $value) {
        
            $returnString .= "<h2 class=\"category\" name=\"$key\"><a title=\"See all $key for your $year $make $model\" href=\"#\" class=\"topLevelCatLink\">$key</a></h2>";
            $rows = floor(count($value) / 3);
            $str1 = "";
            $str2 = "";
            $str3 = "";
            $mod = count($value) % 3;
            $break1 = ($mod > 0) ? $rows + 1 : $rows;
            $break2 = ($mod > 1) ? $break1 + $rows + 1 : $break1 + $rows;
            
            $keys = array_keys($value);
            
                for ($j = 0; $j < $break1; $j++) {
                    $subNote = trim($value[$keys[$j]]['note']);
                    $subCount = $value[$keys[$j]]['count'];
                    $liClass = (!empty($subNote)) ? "itemCatR" : "itemCat";
                    if ($key == "Universal Parts") {
                        $str1 .= "<li class=\"universal\"><a href=\"#\">" . $keys[$j] . "</a></li>";
                    } else if (!empty($subNote)) {
                        $str1 .= "<li class=\"$liClass\">";
                        $str1 .= "<a href=\"" . $subNote . "\">" . $keys[$j] . "</a>";
                        $str1 .= "</li>";
                    } else if ($subCount == 0) {
                        $str1 .= "<li class=\"itemCat\ notAvail\">";
                        $str1 .= "<span>$keys[$j]</span>";
                        $str1 .= "</li>";
                    } else {
                        $str1 .= "<li class=\"$liClass\">";
                        $str1 .= "<a href=\"#\" rel=\"$key\">" . $keys[$j] . "</a>";
                        $str1 .= "</li>";
                    }
                    
                }

                for ($j; $j < $break2; $j++) {
                    $subNote = trim($value[$keys[$j]]['note']);
                    $subCount = $value[$keys[$j]]['count'];
                    $liClass = (!empty($subNote)) ? "itemCatR" : "itemCat";
                    if ($key == "Universal Parts") {
                        $str2 .= "<li class=\"universal\"><a href=\"#\">" . $keys[$j] . "</a></li>";
                    } else if (!empty($subNote)) {
                        $str2 .= "<li class=\"$liClass\">";
                        $str2 .= "<a href=\"" . $subNote . "\">" . $keys[$j] . "</a>";
                        $str2 .= "</li>";
                    } else if ($subCount == 0) {
                        $str2 .= "<li class=\"itemCat\ notAvail\">";
                        $str2 .= "<span>$keys[$j]</span>";
                        $str2 .= "</li>";
                    } else {
                        $str2 .= "<li class=\"$liClass\">";
                        $str2 .= "<a href=\"#\" rel=\"$key\">" . $keys[$j] . "</a>";
                        $str2 .= "</li>";
                    }
                }

                for ($j; $j < count($value); $j++) {
                    $subNote = trim($value[$keys[$j]]['note']);
                    $subCount = $value[$keys[$j]]['count'];
                    $liClass = (!empty($subNote)) ? "itemCatR" : "itemCat";
                    if ($key == "Universal Parts") {
                        $str3 .= "<li class=\"universal\"><a href=\"#\">" . $keys[$j] . "</a></li>";
                    } else if (!empty($subNote)) {
                        $str3 .= "<li class=\"$liClass\">";
                        $str3 .= "<a href=\"" . $subNote . "\">" . $keys[$j] . "</a>";
                        $str3 .= "</li>";
                    } else if ($subCount == 0) {
                        $str3 .= "<li class=\"itemCat notAvail\">";
                        $str3 .= "<span>$keys[$j]</span>";
                        $str3 .= "</li>";
                    } else {
                        $str3 .= "<li class=\"$liClass\">";
                        $str3 .= "<a href=\"#\" rel=\"$key\">" . $keys[$j] . "</a>";
                        $str3 .= "</li>";
                    }
                }

            
            
            $returnString .= "<ul class=\"catList\">" . $str1 . "</ul><ul class=\"catList\">" . $str2 . "</ul><ul class=\"catList\">" . $str3 . "</ul>";
                
            //$returnString .= ($searchType == SINGLE_CAT_SEARCH) ? "" : "<p class=\"toTop\"><a href=\"#\">[back to top]</a></p>";
        }

        //$returnString = preg_replace("/\[linkString\]/", $linkString, $returnString);
        
        echo "PARTSPROS.displayResults('$returnString'); \n";
        
        echo "function backToTop(e) { \n";
        echo "  if (e) { e.preventDefault(); } \n";
        echo "  var pos = $('p.catHeading').position().top; \n";
        echo "  $('html:not(:animated), body:not(:animated)').animate({ scrollTop: pos }, 'fast'); \n";
        echo "  $('#catJump').prop('selectedIndex', 0); \n";
        echo "} \n\n";
        
        echo "$(document).ready(function() { \n";
        echo "  $('ul.catList li.itemCat a').click(function(e) { \n";
        echo "      e.preventDefault(); \n";
        echo "      var subcategory = $(this).text().replace(\"&\", \"|amp|\"); \n";
        echo "      var category = $(this).attr('rel').replace(\"&\", \"|amp|\"); \n";
        echo "      PARTSPROS.runQuery('" . SEARCH_BY_CAT . "', {year: '$year', make: '$make', model: '$model', 'category': category, 'subcategory': subcategory}); \n"; 
        echo "  });";
        echo "  $('ul.catList li.universal a').click(function(e) { \n";
        echo "      e.preventDefault(); \n";
        echo "      var subcategory = $(this).text().replace(\"&\", \"|amp|\"); \n";
        echo "      PARTSPROS.runQuery('" . SEARCH_BY_UNIVERSAL . "', {'subcategory': subcategory}); \n"; 
        echo "  });\n";
        echo "  $('a#showAll').click(function(e) { \n";
        echo "      e.preventDefault(); \n";
        echo "      var form = $('<form method=\"get\" action=\"" . STORE_URL . "search-results.html\"></form>'); \n";
        echo "      form.append('<input type=\"hidden\" name=\"year\" value=\"$year\" />'); \n";
        echo "      form.append('<input type=\"hidden\" name=\"make\" value=\"$make\" />'); \n";
        echo "      form.append('<input type=\"hidden\" name=\"model\" value=\"$model\" />'); \n";
        echo "      form.append('<input type=\"hidden\" name=\"allcats\" value=\"true\" />'); \n";
        echo "      $('body').append(form); \n";
        echo "      form.submit(); \n";
        echo "  }); \n";
        echo "  $('a.topLevelCatLink').click(function(e) { \n";
        echo "  e.preventDefault(); \n";
        echo "      var cat = $(this).text().replace(\"&\", \"|amp|\"); \n";
        echo "      var form = $('<form method=\"get\" action=\"" . STORE_URL . "category-results.html\"></form>'); \n";
        echo "      form.append('<input type=\"hidden\" name=\"year\" value=\"$year\" />'); \n";
        echo "      form.append('<input type=\"hidden\" name=\"make\" value=\"$make\" />'); \n";
        echo "      form.append('<input type=\"hidden\" name=\"model\" value=\"$model\" />'); \n";
        echo "      form.append('<input type=\"hidden\" name=\"allsubcats\" value=\"' + cat + '\" />'); \n";
        echo "      $('body').append(form); \n";
        echo "      form.submit(); \n";
        echo "  }); \n";
        echo "  $('p.toTop a').click(function(e) { \n";
        echo "      backToTop(e); \n";
        echo "  }); \n";
        echo "});";

    }

    if(isset($_GET['action'])) {
    
        require_once(CLASS_DIRECTORY . 'class.queryDB.php');
        if (!$connect) {
            $connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
            mysqli_select_db($connect, DB_NAME);
            $qdb = new queryDB($connect);
            $qdb->setStoreVars();
        }
        
        switch ($_GET['action']) {
            
            case GET_YEARS:
            
                $years = $qdb->getYears();
                $count = count($years);
                for ($i = 0; $i < $count; $i++) {
                    $str .= $years[$i];
                    if($i < $count - 1) {
                        $str .= ',';
                    }
                }
                echo "PARTSPROS.props.years = [" . $str . "]; \n";
                echo "PARTSPROS.updateSelect('year-select', PARTSPROS.props.years); \n";
                echo "cookies.set('ppValidYears', '$str', { expires: 7 });";
                
            break;
            
            case YEAR_SELECT:
                if (isset($_GET['year']) && $_GET['year'] !== '0') {
                    $makes = $qdb->getMakesFromYear($_GET['year'], false);
                    $count = count($makes);
                    echo "PARTSPROS.updateSelect('year-make', [";
                    for ($i = 0; $i < $count; $i++) {
                        echo "'" . $makes[$i] . "'";
                        if($i < $count - 1) {
                            echo ',';
                        }
                    }
                    echo "]);";
                } else {
                
                }
            break;
            
            case MAKE_SELECT:
                if (isset($_GET['make']) && $_GET['make'] !== '0' && isset($_GET['year']) && $_GET['year'] !== '0') {
                    $models = $qdb->getModelsFromMakes($_GET['make'], $_GET['year'], false);
                    $count = count($models);
                    echo "PARTSPROS.updateSelect('year-model', [";
                    for ($i = 0; $i < $count; $i++) {
                        echo "'" . $models[$i] . "'";
                        if($i < $count - 1) {
                            echo ',';
                        }
                    } 
                    echo "]);";
                }
            break;
            
            case MODEL_SELECT:
                if (isset($_GET['make']) && $_GET['make'] !== '0' && isset($_GET['year']) && $_GET['year'] !== '0' && isset($_GET   ['model']) && $_GET['model'] !== '0') {
                
                    $year = $_GET['year'];
                    $make = $_GET['make'];
                    $model = $_GET['model'];
                    $start = (isset($_GET['start'])) ? $_GET['start'] : '0';
                    
                    echo "var form = $('<form method=\"get\" action=\"" . STORE_URL . "category-results.html\"></form>'); \n";
                    echo "form.append('<input type=\"hidden\" name=\"year\" value=\"$year\" />'); \n";
                    echo "form.append('<input type=\"hidden\" name=\"make\" value=\"$make\" />'); \n";
                    echo "form.append('<input type=\"hidden\" name=\"model\" value=\"$model\" />'); \n";
                    echo "form.append('<input type=\"hidden\" name=\"start\" value=\"$start\" />'); \n";
                    echo "form.append('<input type=\"hidden\" name=\"limit\" value=\"" . ITEMS_PER_PAGE . "\" />'); \n";
                    echo "$('body').append(form); \n";
                    echo "form.submit();";
                    //echo "window.location.href=\"/category-results.html?year=$year&make=$make&model=$model&start=$start&limit=" . ITEMS_PER_PAGE . "\";";
                }
            break;
            
            case SEARCH_BY_CAT:
            
                if (isset($_GET['make']) && $_GET['make'] !== '0' && isset($_GET['year']) && $_GET['year'] !== '0' && isset($_GET   ['model']) && $_GET['model'] !== '0' && isset($_GET['category']) && isset($_GET['subcategory'])) {
                    $year = $_GET['year'];
                    $make = $_GET['make'];
                    $model = $_GET['model'];
                    $cat = $_GET['category'];
                    $subcat = $_GET['subcategory'];
                    $start = (isset($_GET['start'])) ? $_GET['start'] : '0';
                    $limit = ITEMS_PER_PAGE;
                    
                    echo "var form = $('<form method=\"get\" action=\"" . STORE_URL . "search-results.html\"></form>'); \n";
                    echo "form.append('<input type=\"hidden\" name=\"year\" value=\"$year\" />'); \n";
                    echo "form.append('<input type=\"hidden\" name=\"make\" value=\"$make\" />'); \n";
                    echo "form.append('<input type=\"hidden\" name=\"model\" value=\"$model\" />'); \n";
                    echo "form.append('<input type=\"hidden\" name=\"category\" value=\"$cat\" />'); \n";
                    echo "form.append('<input type=\"hidden\" name=\"subcategory\" value=\"$subcat\" />'); \n";
                    echo "form.append('<input type=\"hidden\" name=\"start\" value=\"$start\" />'); \n";
                    echo "form.append('<input type=\"hidden\" name=\"limit\" value=\"" . ITEMS_PER_PAGE . "\" />'); \n";
                    echo "$('body').append(form); \n";
                    echo "form.submit();";
                }
            
            break;
            
            case SEARCH_BY_UNIVERSAL:
            
                if (isset($_GET['subcategory'])) {
                    $subcat = $_GET['subcategory'];
                    $start = (isset($_GET['start'])) ? $_GET['start'] : '0';
                    $limit = ITEMS_PER_PAGE;
                    
                    echo "var form = $('<form method=\"get\" action=\"" . STORE_URL . "search-results.html\"></form>'); \n";
                    echo "form.append('<input type=\"hidden\" name=\"subcategory\" value=\"$subcat\" />'); \n";
                    echo "form.append('<input type=\"hidden\" name=\"start\" value=\"$start\" />'); \n";
                    echo "form.append('<input type=\"hidden\" name=\"limit\" value=\"" . ITEMS_PER_PAGE . "\" />'); \n";
                    echo "$('body').append(form); \n";
                    echo "form.submit();";
                }
                
            break;
            
            case SEARCH_BY_CODE:
                
                $code = $_GET['code'];
                $startPoint = $_GET['start'];
                $limit = ITEMS_PER_PAGE;
                
                $items = $qdb->getByCode($code, $startPoint, $limit);
                
                $currSearch = $qdb->getCurrentSearch();
                $year = $currSearch['year'];
                $make = $currSearch['make'];
                $model = $currSearch['model'];
                $categories = $qdb->getCategoriesByItem($year, $make, $model);
                printResults($qdb, $items, $categories);
                
            break;
            
            case CAT_SELECT:
                
                $year = $_GET['year'];
                $make = $_GET['make'];
                $model = $_GET['model'];
                $cat = preg_replace("/\|amp\|/", "&", $_GET['category']);
                $subcat = preg_replace("/\|amp\|/", "&", $_GET['subcategory']);
                $start = (isset($_GET['start'])) ? $_GET['start'] : '0';
                $limit = ITEMS_PER_PAGE;
                
                $items = $qdb->getItemsWithCat($year, $make, $model, $cat, $subcat, $start, $limit);
                $categories = $qdb->getCategoriesByItem($year, $make, $model);
                $categoryText = $qdb->getCategoryText($cat, $subcat);
                printResults($qdb, $items, $categories, $categoryText);
                
            break;
            
            case FULL_SELECT:
            
                $year = $_GET['year'];
                $make = $_GET['make'];
                $model = $_GET['model'];
                $startPoint = $_GET['start'];
                $limit = ITEMS_PER_PAGE;
                
                $items = $qdb->getItems($year, $make, $model, $startPoint, $limit);
                $categories = $qdb->getCategoriesByItem($year, $make, $model);
                printResults($qdb, $items, $categories);

            break;
            
            case GET_CATEGORIES:
            
                $year = $_GET['year'];
                $make = $_GET['make'];
                $model = $_GET['model'];
                
                $categories = $qdb->getCategoriesByItem($year, $make, $model);
                printCategories($qdb, $categories);
            
            break;
            
            case GET_SINGLE_CAT:
            
                $year = $_GET['year'];
                $make = $_GET['make'];
                $model = $_GET['model'];
                $cat = $_GET['category'];
                
                $categories = $qdb->getSingleCat($year, $make, $model, $cat);
                printCategories($qdb, $categories);
                
            break;
            
            case GET_ALL_ITEMS_IN_CAT:
            
                $year = $_GET['year'];
                $make = $_GET['make'];
                $model = $_GET['model'];
                $cat = $_GET['category'];
                $startPoint = $_GET['start'];
                $limit = ITEMS_PER_PAGE;
                
                $items = $qdb->getAllItemsInCat($year, $make, $model, $cat, $startPoint, $limit);
                printResults($qdb, $items, null);
            
            break;
            
            case GET_UNIVERSAL_PARTS:
            
                $subcat = $_GET['subcategory'];
                $startPoint = $_GET['start'];
                $limit = ITEMS_PER_PAGE;
                
                $items = $qdb->getUniversalParts($subcat, $startPoint, $limit);
                printResults($qdb, $items, null);
            
            break;
            
            default:
                echo "";
        
        }
    
    } else {
        die;
    }

?>