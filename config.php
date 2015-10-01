<?php

    // Class directory
    define('CLASS_DIRECTORY', '/var/www/html/custom/partspros/partspros2015/classes/');
    
    // Backup directory
    define('BACKUP_DIRECTORY', '/var/mnt2/partspros/backups_new/');
    
    // DB credentials
    define('DB_HOST', 'ec2-50-19-255-151.compute-1.amazonaws.com');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'parts_pros');
    
    // Actions
    define('GET_YEARS', 'getyears');
    define('YEAR_SELECT', 'yearselected');
    define('MAKE_SELECT', 'makeselected');
    define('MODEL_SELECT', 'modelselected');
    define('FULL_SELECT', 'fullselect');
    define('SEARCH_BY_CODE', 'searchbycode');
    define('GET_CATEGORIES', 'getcategories');
    define('CAT_SELECT', 'catselected');
    define('SEARCH_BY_CAT', 'searchbycat');
    define('GET_SINGLE_CAT', 'getsinglecat');
    define('SEARCH_BY_UNIVERSAL', 'searchbyuniversal');
    define('GET_UNIVERSAL_PARTS', 'getuniversalparts');
    define('GET_ALL_ITEMS_IN_CAT', 'getallitemsincat');
    
    // Search types
    define('ITEM_CAT_SEARCH', 'catsearch');
    define('FULL_SEARCH', 'fullsearch');
    define('CODE_SEARCH', 'codesearch');
    define('SINGLE_CAT_SEARCH', 'singlecatsearch');
    define('ALL_ITEMS_IN_CAT_SEARCH', 'allitemsincatsearch');
    define('UNIVERSAL_PART_SEARCH', 'universalpartsearch');
    
    // All items "category"
    define('ALL_ITEMS', 'parts');
    
    // Settings
    define('ITEMS_PER_PAGE', 50);
    define('DEBUG_MODE', true);
    define('STORE_URL', 'http://yhst-141660816666872.stores.yahoo.net/');
    define('FAQ_EMAIL', 'sales@partspros.com');
    
?>