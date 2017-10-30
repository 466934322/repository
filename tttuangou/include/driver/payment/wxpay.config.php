<!--{template @admin/header}-->

<!-- * 微信支付配置项 * -->

{eval
$pay = logic('pay')->SrcOne('wxpay');
$cfg = unserialize($pay['config']);
}
<form action="?mod=payment&code=save" method="post" enctype="multipart/form-data">
    <table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">
        <tr class="header">
            <td colspan="2">修改微信支付设置</td>
        </tr>
        <tr>
            <td width="23%" class="td_title">appid：</td>
            <td width="77%">
                <input name="cfg[appid]" type="text" size="38" value="{$cfg['appid']}">
            </td>
        </tr>
        <tr>
            <td class="td_title">appsecret：</td>
            <td>
                <input name="cfg[appsecret]" type="text" size="40" value="{$cfg['appsecret']}" />
            </td>
        </tr>
        <tr>
            <td class="td_title">mch_id：</td>
            <td>
                <input name="cfg[mch_id]" type="text" size="10" value="{$cfg['mch_id']}" />

            </td>
        </tr>
        <tr>
            <td class="td_title">key:</td>
            <td>
                <input name="cfg[key]" type="text" size="40" value="{$cfg['key']}" />
        </tr>
    </table>
    <br/>
    <center>
        <input type="hidden" name="id" value="{$pay['id']}" />
        <input type="submit" name="submit" value="提 交" class="button" />
    </center>
</form>
<!--{template @admin/footer}-->