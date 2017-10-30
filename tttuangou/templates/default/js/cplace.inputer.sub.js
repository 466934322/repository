/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name cplace.inputer.sub.js * @date 2015-08-31 16:32:14 */ $(document).ready(function(){
	cplace_regions_reload();
});

function cplace_regions_reload()
{
	$('#__cplace_region').html('<option value="0">正在加载</option>');
	$('#__cplace_street').html('<option value="0">全部</option>');

	$.get('ajax.php?mod=getseller&code=getprosubsellerplace&pid='+__CityPlaceInputer_PID, function(html){
		$('#__cplace_region').html(html);
	});
}

function cplace_region_change()
{
	var region_id = $('#__cplace_region').val();
	$('#__cplace_street').html('<option value="0">正在加载</option>');
	$.get('ajax.php?mod=getseller&code=getprosubsellerplace&pid='+__CityPlaceInputer_PID+'&region='+region_id.toString(), function(html){
		$('#__cplace_street').html(html);
	});
	cplace_load_sub_sellers();
}

function cplace_street_change() {
	cplace_load_sub_sellers();
}

function cplace_load_sub_sellers() {
	loadProSubSellers();
}