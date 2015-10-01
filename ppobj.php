<?php 
header('Content-Type: text/javascript');
require_once('../config.php');
?>
PARTSPROS = {};
PARTSPROS.props = {};
PARTSPROS.props.results = [];
PARTSPROS.props.categories = [];

PARTSPROS.runQuery = function(action, values) {
    var key = '';
    var url = "http://fastpivotsoftware.com/custom/partspros/partspros2015/lookup/?";
    url += "action=" + action;
    
    for (key in values) {
      if (values.hasOwnProperty(key)){
        url += '&' + key + '=' + values[key];
      }
    }
    
    $.ajax({
        'url': url,
        'dataType': 'jsonp'
    });
}

PARTSPROS.clearModel = function() {
    $('select#year-model option:gt(0)').remove();
    $('select#year-model').attr('disabled', true);
    $('select#year-model').parent().addClass('disabled');
};

PARTSPROS.clearMakeModel = function() {
    $('select#year-make, select#year-model').each(function() {
        $('#' + $(this).attr('id') + ' option:gt(0)').remove();
        $(this).attr('disabled', true);
        $(this).parent().addClass('disabled');
    });
};

PARTSPROS.updateSelect = function(id, options) {
    var sel = $('#' + id);
    $('#' + id + ' option:gt(0)').remove();

    $.each(options, function(idx, value) {
        sel.append($('<option></option>').attr('value', value).text(value));
    });
    
    sel.attr('disabled', false);
    sel.parent().removeClass('disabled');
};


PARTSPROS.displayResults = function(str) {
    $('#search-results').append(str);
};

PARTSPROS.redirect = function(str) {
    window.location = "<?php echo STORE_URL; ?>" + str;
}

$(document).ready(function() {

    if (!cookies.get('ppValidYears')) {
        PARTSPROS.runQuery('<?php echo GET_YEARS; ?>');
    } else {
        PARTSPROS.props.years = cookies.get('ppValidYears').split(','); 
        PARTSPROS.updateSelect('year-select', PARTSPROS.props.years); 
        <?php if (DEBUG_MODE == true) {echo "\n console.log('Cookie read. No db lookup needed.')";} ?>
    }
    $('#year-select').change(function() {
        PARTSPROS.clearMakeModel();
        PARTSPROS.runQuery('<?php echo YEAR_SELECT; ?>', {year: $('#year-select').val()});
    });
    $('#year-make').change(function() {
        PARTSPROS.clearModel();
        PARTSPROS.runQuery('<?php echo MAKE_SELECT; ?>', {year: $('#year-select').val(), make: $('#year-make').val()});
    });
    $('#year-model').change(function() {
        PARTSPROS.runQuery('<?php echo MODEL_SELECT; ?>', {year: $('#year-select').val(), make: $('#year-make').val(), model: $('#year-model').val()});
    });
});