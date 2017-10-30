/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name getcity.js * @date 2016-04-12 18:33:13 */ 
var __editor_allow_close = false;

$(document).ready(function(){
    // city
    $('#allCityList').bind('change', function(){
        $.hook.call('pro.city.sel.change');
    });
    $.hook.add('pro.city.sel.change', function(){
        cplace_regions_reload();
    });
    home_loadCitys();
});

function home_loadCitys()
{
    $('#allCityList').html('<option value="-1">正在加载</option>');
    $.get('index.php?mod=seller_join&code=quick&op=listCity'+$.rnd.stamp(), function(data){
        $('#allCityList').html(data);
    });
}

function cplace_regions_reload()
{
    var city_id = $('#allCityList').val();
    $('#__cplace_region').html('<option>正在加载</option>');
    $('#__cplace_street').html('<option value="0">全部</option>');
    $.get('index.php?mod=seller_join&code=place&op=ajaxlist&type=city&id='+city_id.toString(), function(html){
        $('#__cplace_region').html(html);
    });
}

function cplace_region_change()
{
    var region_id = $('#__cplace_region').val();
    $('#__cplace_street').html('<option>正在加载</option>');
    $.get('index.php?mod=seller_join&code=place&op=ajaxlist&type=region&id='+region_id.toString(), function(html){
        $('#__cplace_street').html(html);
    });
}