<!--{template @admin/header}-->

<!-- * 财付通配置项 * -->

{eval
    $pay = logic('pay')->SrcOne('tenpaywap');
    $cfg = unserialize($pay['config']);
}

<style type="text/css">
.dsp_for_medi {display: none;}
</style>

<form action="?mod=payment&code=save" method="post" enctype="multipart/form-data">
	<table cellspacing="1" cellpadding="4" width="100%" align="center" class="tableborder">
		<tr class="header">
			<td colspan="2">修改财付通手机支付设置</td>
		</tr>
		<tr>
			<td class="td_title" width="20%">财付通密钥：</td>
			<td>
				<input name="cfg[key]" type="text" size="50" value="{$cfg['key']}" />
			</td>
		</tr>
		<tr>
			<td class="td_title">财付通商户号：</td>
			<td>
				<input name="cfg[bargainor_id]" type="text" value="{$cfg['bargainor_id']}">
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