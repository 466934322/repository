/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name push.mgr.js * @date 2015-04-21 16:43:55 */ function push_data_delete(dsp, table, id)
{
    if (!confirm('您确认要删除吗？')) return false;
    $(dsp).html('正在删除...');
    $.get('admin.php?mod=push&code=manage&op=delete&table='+table+'&id='+id+$.rnd.stamp(), function(data){
        if (data == 'ok')
        {
            $(dsp).html('已经删除！');
            setTimeout(function(){$('#tr_of_'+id).fadeOut()}, 300);
        }
        else
        {
            $(dsp).html('删除失败！');
        }
    });
}
function push_resend(id)
{
	artDialog.open('admin.php?mod=push&code=manage&op=resend&table=log&id='+id+'&~iiframe=yes');
}
//短信日志zzy
function isphone_timev_goto()
{
	var isphone_num = $('#isphone_num').val();
	var isphone_tvbegin = $('#isphone_timev_begintime').val();
	var isphone_tvfinish = $('#isphone_timev_finishtime').val();
	if ((isphone_tvbegin == '' && isphone_tvfinish == '') && isphone_num == '')
	{
		
		$.notify.show('搜索条件必须填写至少一个！');
		return;
	}
	var kurls = {};
	    kurls['isphone_num'] = isphone_num;
		kurls['isphone_tvbegin'] = isphone_tvbegin;
		kurls['isphone_tvfinish'] = isphone_tvfinish;
	var loc = window.location.toString();
	$.each(kurls, function(k, v) {
		var rxSearch = new RegExp(k+'=.*?$', 'ig');
		if (rxSearch.test(loc)) 
		{
			loc = loc.replace(rxSearch, (k+'='+v));
		}
		else
		{
			loc = loc+'&'+k+'='+v;
		}
	});
	
	window.location = loc;
}
//邮件日志zzy
function ismail_timev_goto()
{
	var ismail_num = $('#ismail_num').val();
	var ismail_tvbegin = $('#ismail_timev_begintime').val();
	var ismail_tvfinish = $('#ismail_timev_finishtime').val();
	if ((ismail_tvbegin == '' && ismail_tvfinish == '') && ismail_num == '')
	{
		
		$.notify.show('搜索条件必须填写至少一个！');
		return;
	}
	var kurls = {};
	    kurls['ismail_num'] = ismail_num;
		kurls['ismail_tvbegin'] = ismail_tvbegin;
		kurls['ismail_tvfinish'] = ismail_tvfinish;
	var loc = window.location.toString();
	$.each(kurls, function(k, v) {
		var rxSearch = new RegExp(k+'=.*?$', 'ig');
		if (rxSearch.test(loc)) 
		{
			loc = loc.replace(rxSearch, (k+'='+v));
		}
		else
		{
			loc = loc+'&'+k+'='+v;
		}
	});
	
	window.location = loc;
}