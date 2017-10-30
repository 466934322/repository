/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name recharge.card.generate.js * @date 2015-08-31 16:32:14 */ $(document).ready(function(){
    $('#btn_create').bind('click', GenerateRechargeCard);
});

function GenerateRechargeCard()
{
    if (!confirm('确认生成？')) return;
    var price = $('#card_price').val().toString();
    var nums = $('#card_nums').val().toString();
    if (price == '' || nums == '')
    {
        $.notify.alert('充值卡面额或份数不能为空！');
        return;
    }
    $('#generate_result').text('正在生成...');
    $.get('?mod=recharge&code=card&op=generate_ajax&price='+price+'&nums='+nums+$.rnd.stamp(), function(data){
        if (data == 'ok')
        {
            $('#generate_result').html('<font color="green"><b>已经生成！</b></font>');
            /*
            setTimeout(function(){$('#generate_result').text('等待生成');}, 2000);
            */
            window.location.href = 'admin.php?mod=recharge&code=card';
        }
        else
        {
            $('#generate_result').html(data);
        }
    });
}