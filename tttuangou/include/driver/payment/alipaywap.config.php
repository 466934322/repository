<!--{template @admin/header}-->



<!-- * 支付宝配置项 * -->



{eval

$pay = logic('pay')->SrcOne('alipaywap');

$cfg = unserialize($pay['config']);

}

<form action="?mod=payment&code=save" method="post" enctype="multipart/form-data">

    <table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">

        <tr class="header">

            <td colspan="2">修改支付宝手机网页支付设置</td>

        </tr>

        <tr>

            <td width="23%" class="td_title">支付宝账户：</td>

            <td width="77%">

                <input name="cfg[account]" type="text" size="38" value="{$cfg['account']}">

            </td>

        </tr>

        <tr>

            <td class="td_title">合作者身份(PID)：</td>

            <td>

                <input name="cfg[partner]" type="text" size="38" value="{$cfg['partner']}" />

            </td>

        </tr>
 	<tr>

            <td class="td_title">安全校验码(KEY)：</td>

            <td>

                <input name="cfg[key]" type="text" size="50" value="{$cfg['key']}" />

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