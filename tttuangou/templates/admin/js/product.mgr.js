/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name product.mgr.js * @date 2015-08-31 16:32:14 */ var IMG_LOADING = 'templates/admin/images/btn_loading.gif';

var __img_last_id = '';
var __img_control_d = false;
var __click_from_submit = false;
var __editor_allow_exit = false;
var __editor_allow_close = false;

$(document).ready(function(){
    document.title = 'Product Editor';
	// thickbox
	tb_init('a.thickbox');
	// hook for Swfupload
	$.hook.add('swfuploaded', function(file){InsertImage(file)});
	// bind button
	$('#submitButton').bind('click', function(){
        __editor_allow_close = true;
		$.hook.call('productIfoSubmit');
	});
	$.hook.add('productIfoSubmit', function(){
		if (productSubmitCheck(true))
		{
			submitClick(true);
		}
	});
	$('#exitButton').bind('click', exitConfirm);
    /*
	$(window).bind('beforeunload',  function(){
        if (!__editor_allow_close) return '直接关闭将丢失您的所有内容！退出请“保存”或者点击“退出编辑器”！';
    });
    */
    // city
    $('#allCityList').bind('change', function(){
        $.hook.call('pro.city.sel.change');
    });
    $.hook.add('pro.city.sel.change', function(){
        loadSellers($('#allCityList').val());
    });
    // seller
    $('#sellerid').bind('change', function(){
    	$.hook.call('pro.seller.sub.change');
    });
    $.hook.add('pro.seller.sub.change', function(){
    	loadSubSellers($('#sellerid').val(), __Global_PID);
    });
    loadCitys(__Default_CityID);
    loadSellers(__Default_CityID, __Default_SellerID);
    loadSubSellers(__Default_SellerID, __Global_PID);
});

function exitConfirm()
{
	if (confirm('确认退出编辑器？'))
	{
		$.hook.call('productEditorExit');
	}
}

function introFocus(obj)
{
	$(obj).attr('last', $(obj).val());
}

function introChange(id, obj)
{
	if ($(obj).attr('last') == $(obj).val())
	{
		return;
	}
	$(obj).attr('last', $(obj).val());
	var TMP_id = 'img_loading_'+__rand_key();
	$(obj).after('<img id="'+TMP_id+'" src="'+IMG_LOADING+'" />');
	$.get('?mod=product&code=save&op=intro&id='+id+'&intro='+encodeURIComponent($(obj).val())+$.rnd.stamp(), function(data){
		if (data != 'ok')
		{
			$.notify.failed('保存失败！');
		}
		$('#'+TMP_id).remove();
	});
}

function InsertImage(file)
{
	if (__Global_PID == '')
	{
		$('#imgs').val($('#imgs').val()+file.id+',');
		ShowUploadImage(file);
		return;
	}
	$.get('?mod=product&code=add&op=image&pid='+__Global_PID+'&id='+file.id+$.rnd.stamp(), function(data){
		if (data == 'ok')
		{
			ShowUploadImage(file);
		}
	});
}

function ShowUploadImage(file)
{
	var tpl = $('#img_li_TPL').html();
	tpl = tpl.replace(/\[id\]/g, file.id);
	tpl = tpl.replace(/#http\:\/\/\[url\]\//g, file.url);
	$('#img_li_TPL').before('<li id="img_li_for_'+file.id+'">'+tpl+'</li>');
}

function DeleteImage(id)
{
	if (!confirm('确认删除？')) return;
	$.get('?mod=product&code=del&op=image&pid='+__Global_PID+'&id='+id+$.rnd.stamp(), function(data){
		if (data == 'ok')
		{
			if (__Global_PID == '')
			{
				$('#imgs').val($('#imgs').val().replace(id+',', ''));
			}
			$('#img_li_for_'+id).slideUp();
		}
	});
}

/**
 * 随机字符
 */
function __rand_key()
{
	var salt = '0123456789qwertyuioplkjhgfdsazxcvbnm';
	var str = '';
	for(var i=0; i<6; i++)
	{
		str += salt.charAt(Math.ceil(Math.random()*100000000)%salt.length);
	}
	return str;
}

function productSubmitCheck(showErr)
{
	// check must val
	var mvcList = {
		'productName': '产品标题不能为空！',
		'productFlag': '简短名称不能为空！',
		'allCityList': '请选择产品投放城市！',
		'sellerid': '请指定合作商家！',
		'productPrice': '请输入产品原价！',
		'productNowPrice': '请输入产品现价！'
	};
	if($("#allCityList").length == 0){delete mvcList.allCityList;}
	var foundErr = false;
	var errString = '';
	$.each(mvcList, function(id, err){
		var kval = $('#'+id).val();
		if (id == 'productNowPrice') kval = isNaN(kval) ? 0 : '__';
        kval = typeof(kval) == 'undefined' ? '' : kval;
		if (kval == '' || kval == 0)
		{
			foundErr = true;
			errString = err;
			return false;
		}
	});
	if (foundErr)
	{
		showErr && $.notify.alert(errString);
		return false;
	}
	
	return true;
}

function submitClick(clk)
{
	__click_from_submit = clk;
}

function checkIfClick()
{
	return __click_from_submit;
}

function loadCitys(cid)
{
    $('#allCityList').html('<option value="-1">正在加载</option>');
    $.get('admin.php?mod=product&code=quick&op=listCity&icity='+cid+$.rnd.stamp(), function(data){
        $('#allCityList').html(data);
    });
}

function proIfoAddCity()
{
    // open dialog
    art.dialog({
        title: '添加城市',
        content: document.getElementById('OPBox_addCity'),
         button: [
            {
                name: '保存',
                callback: function(){
                    $.notify.loading('正在添加...');
                    var $cityName = $('#opb_addcity_name').val();
                    var $cityFlag = $('#opb_addcity_flag').val();
                    var opener = this;
                    $.get('admin.php?mod=product&code=quick&op=addCity&name='+encodeURIComponent($cityName)+'&flag='+encodeURIComponent($cityFlag), function(data){
                        $.notify.loading(false);
                        if (!isNaN(data))
                        {
                            opener.close();
                            loadCitys(data);
                            loadSellers(data);
                        }
                        else
                        {
                            $.notify.failed(data);
                        }
                    });
                    return false;
                }
            },
            {
                name: '关闭',
                callback: function(){
                    this.close();
                }
            }
         ]
    });
}

function loadSellers(cid, sid)
{
    if(cid == '' || cid == 0)
    {
        return;
    }
    sid = (sid != '' && typeof(sid) != 'undefined') ? sid : 0;
    $.get('ajax.php?mod=getseller&city='+cid+'&seller='+sid+$.rnd.stamp(), function(data){
        $('#allSellerList').html(data);
    });
}

function loadSubSellers(sid, pid) {
	sid = (sid != '' && typeof(sid) != 'undefined') ? sid : 0;
	if(0 == sid || '' == sid) {
		return ;
	}
	pid = (pid != '' && typeof(pid) != 'undefined') ? pid : 0;
	$.get('ajax.php?mod=getseller&code=getsubseller&seller='+sid+'&pid='+pid+$.rnd.stamp(), function(data) {
		$('#allSubSellerList').html(data);
	});
}

function proIfoAddSeller()
{
    // process dialog
    var $cityID = $('#allCityList').val();
    if (isNaN($cityID) || $cityID <= 0)
    {
        $.notify.alert('请先选择投放城市！');
        return;
    }
    $cityID = parseInt($cityID);
    art.dialog({
        title: '添加商家',
        content: document.getElementById('OPBox_addSeller'),
         button: [
            {
                name: '保存',
                callback: function(){
                    $.notify.loading('正在添加...');
                    var $userName = $('#opb_addseller_username').val();
                    var $sellerName = $('#opb_addseller_sellername').val();
                    var opener = this;
                    $.get('admin.php?mod=product&code=quick&op=addSeller&city='+$cityID+'&username='+encodeURIComponent($userName)+'&sellername='+encodeURIComponent($sellerName), function(data){
                        $.notify.loading(false);
                        if (!isNaN(data))
                        {
                            opener.close();
                            loadSellers($cityID, data);
                        }
                        else
                        {
                            $.notify.failed(data);
                        }
                    });
                    return false;
                }
            },
            {
                name: '关闭',
                callback: function(){
                    this.close();
                }
            }
         ]
    });
}

function ifoShowHelper(item)
{
    art.dialog({
        title: '帮助手册',
        icon: 'question',
        lock: true,
        content: document.getElementById('helper_of_'+item),
        yesText: '知道了',
        yesFn: true
    });
}

function dsp_payment_list($DSP)
{
    var tar = $('#dsp_payment_list');
    $DSP ? tar.show() : tar.hide();
}

function load_product_tag(product_id, retryv) {
	$.get('admin.php?mod=tag&code=view&product_id=' + product_id + '&retry=' + retryv, function(data){
		$('#product_tag_view').html(data);
	});	
}
function product_tag_mgr(product_id) {
	// open dialog
    art.dialog({
        title: '标签设置',
        content: document.getElementById('OPBox_productTag'),
         button: [
            {
                name: '保存',
                callback: function(){
                    $.notify.loading('正在保存...');
                    var opener = this;
                    $.post($('#tag_list_mgr_form').attr('action') + '&in_ajax=1', $('#tag_list_mgr_form').serialize(), function(data){
                        $.notify.loading(false);
                        if (!isNaN(data))
                        {
                            opener.close();
							load_product_tag(product_id, 'must');
                        }
                        else
                        {
                            $.notify.failed(data);
                        }
                    });
                    return false;
                }
            },
            {
                name: '关闭',
                callback: function(){
                    this.close();
					load_product_tag(product_id);
                }
            }
         ]
    });
}

function product_tag_delete(product_id, tag_id) {
	// open dialog
    art.dialog({
        title: '标签删除',
        content: '删除后的内容不可恢复，确认删除？',
         button: [
            {
                name: '确认删除',
                callback: function(){
                    $.notify.loading('正在删除...');
                    var opener = this;
                    $.get('admin.php?mod=tag&code=delete&product_id=' + product_id + '&tag_id=' + tag_id, function(data){
                        $.notify.loading(false);
						opener.close();
                        $('#product_' + product_id + '_tag_' + tag_id).remove();
						load_product_tag(product_id);
                    });
                    return false;
                }
            },
            {
                name: '关闭',
                callback: function(){
                    this.close();
                }
            }
         ]
    });
}



/**
 *云购相关的代码
 */
// 当用户选择了“云购，预付费，或不选”时，一些属性有不同的名称
var CurMode = 
{
	// ”购买价格“的名称
	'YUNGOU_PRICE'  : '<font color="red">* </font>云购价：',
	'YUFU_PRICE'    : '<font color="red">* </font>预付价：',
	'TUANGOU_PRICE' : '<font color="red">* </font>' + TUANGOU_STR + '价：',
	// ”产品数量“的名称
	'YUNGOU_COUNT'	: '<font color="red">* </font>云购总份数: ',
	'TUANGOU_COUNT' : '<font color="red">* </font>产品总数量：',
	// ”产品数量“的注释
	'YUNGOU_COUNT_COMMENT' : '&nbsp;&nbsp;*注意：此项不能留空，所填值必须大于0。本产品的云购价=云购单价*云购总份数=1*2500=2500元',
	'TUANGOU_COUNT_COMMENT' : '&nbsp;&nbsp;*0表示不限制，否则产品会出现“已卖光”状态',
	
	// 当模式改变时，很多属性的显示状态需要调整
	'changeMode' : function() {

		// 当前模式是“启用云购”
		if ($('#isYunGou').is(':checked')) {	
			$('#isPreSell_tr').hide();										// ”启用预付模式“不可见
			$('#curPriceName').html(CurMode.YUNGOU_PRICE);					// ”购买价格“的名称
			$('#totalCount').html(CurMode.YUNGOU_COUNT);					// ”产品总数“的名称
			$('#totalCountComment').html(CurMode.YUNGOU_COUNT_COMMENT);		// ”产品总数“的注释
			$('#chengTuanRenShu_tr').hide();								// "成功团购人数要求“不可见
			$('#tuanGouJieShuShiJian').hide();								// ”团购结束时间“不可见
			$('#duoQuanHeYi').hide();										// "多券合一”不可见
			$('#attrs-append-pox').hide();									// "添加属性分类“不可见
			// 隐藏掉抽奖类型，如果当前团购类型是“抽奖”，则必为“实物”
			if ($('#product_type_sel').val() == 'prize') {
				$('#product_type_sel option[value="stuff"]').attr('selected',true).trigger('change');
			}
			$('#product_type_sel option[value="prize"]').remove();			// “抽奖”团购类型不存在

		}// 当前模式是“启用预付模式”
		else if ($('#isYuFu').is(':checked')) {		
			$('#isPreSell_tr').show();		
			$('#curPriceName').html(CurMode.YUFU_PRICE);
			$('#totalCount').html(CurMode.TUANGOU_COUNT);	
			$('#totalCountComment').html(CurMode.TUANGOU_COUNT_COMMENT);	
			$('#chengTuanRenShu_tr').show();
			$('#tuanGouJieShuShiJian').show();
			if ($('#product_type_sel').val() == 'ticket') {					// 团购券产品才有“多券全一”选项
				$('#duoQuanHeYi').show();
			}
			$('#attrs-append-pox').show();
			if ($('#product_type_sel option[value="prize"]').length <= 0) {
				$('#product_type_sel').append('<option value="prize">抽奖</option>');
			}
		}// 默认
		else {					
			$('#isPreSell_tr').show();		
			$('#curPriceName').html(CurMode.TUANGOU_PRICE);
			$('#totalCount').html(CurMode.TUANGOU_COUNT);	
			$('#totalCountComment').html(CurMode.TUANGOU_COUNT_COMMENT);
			$('#chengTuanRenShu_tr').show();
			$('#tuanGouJieShuShiJian').show();
			if ($('#product_type_sel').val() == 'ticket') {					// 团购券产品才有“多券全一”选项
				$('#duoQuanHeYi').show();
			}
			$('#attrs-append-pox').show();
			if ($('#product_type_sel option[value="prize"]').length <= 0) {
				$('#product_type_sel').append('<option value="prize">抽奖</option>');
			}
		}
	}
}

// 加载完文档后后做一些初始化的工作
$(document).ready(function() {
	// 注册事件监听
	// “启用云购”或”启用预付模式“的选中状态发生改变时，调整一些显示效果
	$('#isYunGou,#isYuFu').click(function() {
		CurMode.changeMode();
	});
	
	// 点击：“启用云购”下的“查看使用帮助”时显示帮助内容。
	$('#isYunGou_help').click( function(){
		ifoShowHelper('yunGou');
		return false;
	});
	// 当点击了“启用预付价”的“查看使用帮助”时显示帮助内容。
	$('#isYuFu_help').click( function() {
		ifoShowHelper('yuFu');
		return false;
	});
	
	
	CurMode.changeMode();
});