<!--{template @admin/header}-->

<!-- * 通联支付配置项 * -->

{eval
$pay = logic('pay')->SrcOne('allinpay');
$cfg = unserialize($pay['config']);
}
<form action="?mod=payment&code=save" method="post" enctype="multipart/form-data">
    <table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">
        <tr class="header">
            <td colspan="2">通联支付设置<br>
			1、请认真填写以下信息；<br>
			</td>
        </tr>
        <tr>
            <td width="23%" class="td_title">商户号（merchantId）：</td>
            <td width="77%">
                <input name="cfg[merchantId]" type="text" size="38" value="{$cfg['merchantId']}">
                <span><font color="red">*</font> 商户号 </span>
            </td>
        </tr>
        <tr>
            <td width="23%" class="td_title">商户安全MD5值（merchantKey）：</td>
            <td width="77%">
                <input name="cfg[merchantKey]" type="text" size="38" value="{$cfg['merchantKey']}">
                <span><font color="red">*</font> 安全校验时使用，需要和商户服务平台中设置的值保持一致 </span>
            </td>
        </tr>
    </table>
    <br/>
    <center>
        <input type="hidden" name="id" value="{$pay['id']}" />
        <input type="submit" name="submit" value="提 交" class="button" />
    </center>
</form>
<!--{template @admin/footer}-->