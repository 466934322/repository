<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name WxPay.NativePay.php
 * @date 2015-09-23 18:07:18
 */


require_once "WxPay.Api.php";


class NativePay
{
	
	public function GetPrePayUrl($productId)
	{
		$biz = new WxPayBizPayUrl();
		$biz->SetProduct_id($productId);
		$values = WxpayApi::bizpayurl($biz);
		$url = "weixin://wxpay/bizpayurl?" . $this->ToUrlParams($values);
		return $url;
	}
	
	
	private function ToUrlParams($urlObj)
	{
		$buff = "";
		foreach ($urlObj as $k => $v)
		{
			$buff .= $k . "=" . $v . "&";
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}
	
	
	public function GetPayUrl($input)
	{
		if(in_array($input->GetTrade_type(), array('APP', 'NATIVE')))
		{
			$result = WxPayApi::unifiedOrder($input);
			return $result;
		}
	}
}