var hasOwnProperty = Object.prototype.hasOwnProperty;
var country, site, gender, platform, dateFrom, dateTo;

$(function() {
    $('#submit').on('click', function() {
        getValues();
    });
    /*
    $('#country').on('change', function() {
        country = !empty($('#country').val()) ? $('#country').val() : false;
    });
    $('#site').on('change', function() {
        site = !empty($('#site').val()) ? $('#site').val() : false;
    });
    $('#gender').on('change', function() {
        gender = !empty($('#gender').val()) ? $('#gender').val() : false;
    });
    $('#platform').on('change', function() {
        platform = !empty($('#platform').val()) ? $('#platform').val() : false;
    });
    $('#dateFrom').on('change', function() {
        dateFrom = !empty($('#dateFrom').val()) ? $('#dateFrom').val() : false;
    });
    $('#dateTo').on('change', function() {
        dateTo = !empty($('#dateTo').val()) ? $('#dateTo').val() : false;
    });*/
});

function getValues() {

    country = !empty($('#country').val()) ? $('#country').val() : false;
    site = !empty($('#site').val()) ? $('#site').val() : false;
    gender = !empty($('#gender').val()) ? $('#gender').val() : false;
    platform = !empty($('#platform').val()) ? $('#platform').val() : false;
    dateFrom = !empty($('#dateFrom').val()) ? $('#dateFrom').val() : false;
    dateTo = !empty($('#dateTo').val()) ? $('#dateTo').val() : false;
    window.location.href = 'http://scam/monitor/showStat?country='+country+'&site='+site+'&gender='+gender+'&platform='+platform+'&dateFrom='+dateFrom+'&dateTo='+dateTo;
    
}
function empty(obj) {

    if (obj == null) return true;
    if (obj.length > 0)    return false;
    if (obj.length === 0)  return true;
    for (var key in obj) {
        if (hasOwnProperty.call(obj, key)) return false;
    }
    return true;
}
