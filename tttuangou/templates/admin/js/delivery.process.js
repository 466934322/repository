/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name delivery.process.js * @date 2015-09-23 18:07:19 */ $(document).ready(function(){
	$('#submiter').bind('click', function(){submitTrackingNo(false)});
	$('#cdpServiceButton').bind('click', function(){cdpServiceOpen(false)});
});

function submitTrackingNo(BTN, OID, TNO)
{
	if (!confirm('确定提交吗？')) return;
	var submiter = BTN ? $(BTN) : $('#submiter');
	submiter.val('正在登记').attr('disabled', 'disabled');
	var trackingno = TNO ? TNO : $('#trackingno').val();
	OID = OID ? OID : __Global_OID.toString();
	$.get('?mod=delivery&code=upload&op=single&oid='+OID+'&no='+trackingno+$.rnd.stamp(), function(data){
		if (data != 'ok')
		{
			submiter.val('保存失败，请填写8位以上的单号');
		}
		else
		{
			submiter.val('保存成功');
		}
	});
}

function cdpServiceOpen(OID)
{
	var sender = $('#cdpAddressID').val();
	if (!sender)
	{
		alert('您还没有选择发货人，不能进行运单打印！');
		return;
	}
	OID = OID ? OID : __Global_OID.toString();
	window.open('?mod=print&code=delivery&oid='+OID+'&sender='+sender.toString()+$.rnd.stamp());
}