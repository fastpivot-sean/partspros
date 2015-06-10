PARTSPROS = {};
PARTSPROS.makes = [];

<?php

    $connect = mysql_connect("localhost","root","");
    mysql_select_db("parts_pros",$connect);
    
    $query = "SELECT * from makes;";
    $makes = mysql_query($query);
    $number = mysql_num_rows($makes);
    
    echo "\r\n";
    echo "PARTSPROS.makes = [";

    $i = 0;
    while ($row = mysql_fetch_assoc($makes)) {
        $i++;
        echo "'" . $row['make'] . "'";
        if ($i != $number) {
            echo ",";
        }
    }
    echo "];"
?>

PARTSPROS.updateSelect = function(id, options) {
    var sel = $('#' + id);
    $('#' + id + ' option:gt(0)').remove();

    $.each(options, function(value) {
      sel.append($('<option></option>').attr('value', value).text(value));
    });
};