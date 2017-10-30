<?php
/**
 * 模块：微信支付使用
 * @copyright (C)2015 Cenwor Inc.
 * @author 狐狸<foxis@qq.com>
 * @package module
 * @name wxpay.mod.php
 * @version 1.0
 */
class ModuleObject extends MasterObject
{	
	public function ModuleObject( $config )
	{
		$this->MasterObject($config);

		$this->Main();
	}
	
	public function Main()
	{
				$sign = (post('sign') ? post('sign', 'number') : get('sign', 'number'));
		if(empty($sign)) {
			exit('sign is empty');
		}

				$order = logic('order')->GetOne($sign);
		if(false == $order) {
			exit('sign is invalid');
		}

				if($order['pay']) {
			exit('this sign is payed');
		}

				$payment = logic('pay')->GetOne($order['paytype']);
		if($payment && 'wxpay' == $payment['code'] && 'true' == $payment['enabled']) {
			;
		} else {
			exit('paytype is invalid');
		}

				require_once INCLUDE_PATH . 'driver/payment/wxpay/WxPayPubHelper.php';
		$jsApi = new JsApi_pub($payment['config']['appid'],$payment['config']['mch_id'],$payment['config']['key'],$payment['config']['appsecret']);

				$state = 'STATE';

						if ('' == $_GET['code']) {
						$url = $jsApi->createOauthUrlForCode(urlencode(ini('settings.site_url') . '/wxpay/index.php?sign=' . $order['orderid']), $state);
			Header("Location: $url");
			exit();
		}

				if($state != get('state')) {
			exit('state is invalid');
		}

			    $code = get('code');
		$jsApi->setCode($code);
		$openid = $jsApi->getOpenId();

						$unifiedOrder = new UnifiedOrder_pub($payment['config']['appid'],$payment['config']['mch_id'],$payment['config']['key'],$payment['config']['appsecret']);	
		$unifiedOrder->setParameter("openid", $openid);		$unifiedOrder->setParameter("body", $order['orderid']);				$unifiedOrder->setParameter("out_trade_no", $order['orderid']);		$unifiedOrder->setParameter("total_fee", (100 * $order['paymoney']));		$unifiedOrder->setParameter("notify_url", ini('settings.site_url').'/wxpay/callback.php');		$unifiedOrder->setParameter("trade_type", "JSAPI");		$unifiedOrder->setParameter("attach", 'ATTACH');
		$prepay_id = $unifiedOrder->getPrepayId();

				$jsApi->setPrepayId($prepay_id);
		$jsApiParameters = $jsApi->getParameters();
				
		$returnUrl = ini('settings.site_url').'/m.php?mod=me&code=order';

				echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset='.ini('settings.site_url').'" /><meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;" /><meta name="apple-mobile-web-app-capable" content="yes" /><meta name="apple-mobile-web-app-status-bar-style" content="black" /><meta name="format-detection" content="telephone=no" /><link href="'.ini('settings.site_url').'/wxpay/wxpay.css" rel="stylesheet" type="text/css" /><title>微信支付</title><script language="javascript">function callpay(){WeixinJSBridge.invoke("getBrandWCPayRequest",'.$jsApiParameters.',function(res){WeixinJSBridge.log(res.err_msg);if(res.err_msg=="get_brand_wcpay_request:ok"){document.getElementById("payDom").style.display="none";document.getElementById("successDom").style.display="";setTimeout("window.location.href = \''.$returnUrl.'\'",2000);}else{if(res.err_msg == "get_brand_wcpay_request:cancel"){var err_msg = "您取消了支付";}else if(res.err_msg == "get_brand_wcpay_request:fail"){var err_msg = "支付失败<br/>错误信息："+res.err_desc;}else{var err_msg = res.err_msg +"<br/>"+res.err_desc;}document.getElementById("payDom").style.display="none";document.getElementById("failDom").style.display="";document.getElementById("failRt").innerHTML=err_msg;}});}</script></head><body style="padding-top:20px;"><style>.deploy_ctype_tip{z-index:1001;width:100%;text-align:center;position:fixed;top:50%;margin-top:-23px;left:0;}.deploy_ctype_tip p{display:inline-block;padding:13px 24px;border:solid #d6d482 1px;background:#f5f4c5;font-size:16px;color:#8f772f;line-height:18px;border-radius:3px;}</style><div id="payDom" class="cardexplain"><ul class="round"><li class="title mb"><span class="none">支付信息</span></li><li class="nob"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="kuang"><tr><th>金额</th><td>'.floatval($order['paymoney']).'元</td></tr></table></li></ul><div class="footReturn" style="text-align:center"><input type="button" style="margin:0 auto 20px auto;width:100%"  onclick="callpay()"  class="submit" value="点击进行微信支付" /></div></div><div id="failDom" style="display:none" class="cardexplain"><ul class="round"><li class="title mb"><span class="none">支付结果</span></li><li class="nob"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="kuang"><tr><th>支付失败</th><td><div id="failRt"></div></td></tr></table></li></ul><div class="footReturn" style="text-align:center"><input type="button" style="margin:0 auto 20px auto;width:100%"  onclick="callpay()"  class="submit" value="重新进行支付" /></div></div><div id="successDom" style="display:none" class="cardexplain"><ul class="round"><li class="title mb"><span class="none">支付成功</span></li><li class="nob"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="kuang"><tr><td>您已支付成功，页面正在跳转...</td></tr></table><div id="failRt"></div></li></ul></div></body></html>';
		exit;
	}

}