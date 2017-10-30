/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name prize.sign.js * @date 2015-08-31 16:32:14 */ $(document).ready(function(){
    $('#button_phone').bind('click', ps_sendVCode2Phone);
    $('#button_vcode').bind('click', ps_transVerifyCode);
    $('#button_get_prize').bind('click', doooo_view_prize);
});

function ps_sendVCode2Phone()
{
    var $phone = $('#input_phone').val().toString();
    if ($phone.length != 11 || isNaN($phone))
    {
        alert('请输入正确的手机号码！');
        $('#input_phone').focus();
        return false;
    }

    if('' == __Global_Seccode_Val) {
        alert('请先输入正确的图片验证码');
        return false;
    }

    // hide notice bar
    $('#s2phone_ok').fadeOut();
    $('#s2phone_fail').fadeOut();
    // set button disabled
    $('#button_phone').val('正在发送中...').attr('disabled', 'disabled');
    $.get('index.php?mod=prize&code=ajax&op=s2phone&phone='+$phone+'&seccode='+__Global_Seccode_Val+$.rnd.stamp(), function(data){
        if (data != 'ok')
        {
            $('#s2phone_fail').html(data).fadeIn();
            $('#button_phone').val('获取验证码').attr('disabled', '');
        }
        else
        {
            ps_disable_gpButton(60);
            $('#input_phone').attr('readonly', 'readonly');
            $('#s2phone_ok').fadeIn();
        }
    });
}

function ps_transVerifyCode()
{
    var $phone = $('#input_phone').val().toString();
    if ($phone.length != 11 || isNaN($phone))
    {
        alert('请输入正确的手机号码！');
        $('#input_phone').focus();
        return false;
    }
    var $vfcode = $('#input_vcode').val().toString();
    if ($vfcode.length != 5 || isNaN($vfcode))
    {
        alert('请输入正确的验证码！');
        $('#input_vcode').focus();
        return false;
    }
    // hide status bar
    $('#transVfcode_status').fadeOut();
    // set button disabled
    $('#button_vcode').val('正在验证').attr('disabled', 'disabled');
    $.get('index.php?mod=prize&code=ajax&op=Vfcode&phone='+$phone+'&vcode='+$vfcode+$.rnd.stamp(), function(data){
        if (data != 'ok')
        {
            $('#transVfcode_status').html(data).fadeIn();
            $('#button_vcode').val('确认').attr('disabled', '');
        }
        else
        {
            doooo_view_prize();
        }
    });
}

function ps_disable_gpButton(sec)
{
    var btn = $('#button_phone');
    if (sec == 0)
    {
        btn.val('获取验证码').removeAttr('disabled', '');
        return;
    }
    btn.val(sec.toString()+'秒后可重新获取');
    setTimeout(function(){ps_disable_gpButton(sec-1)}, 1000);
}