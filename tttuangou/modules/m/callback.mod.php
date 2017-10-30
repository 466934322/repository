<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name callback.mod.php
 * @date 2016-04-12 18:33:12
 */





class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);

		$this->Main();
	}
	
	function Main()
	{
	    
		$pid = get('pid');
		$pid || $pid = post('pid');
		$pid || exit($this->Ends());
		preg_match('/^[a-z0-9]+$/i', $pid) || exit($this->Ends());

		# 1、获取支付方式
		$payment = logic('pay')->GetOne($pid);
		$payment || exit($this->Ends());
		# 2、进行支付校验
 		$status = logic('pay')->Verify($payment);
 		$status || exit($this->Ends());
 		# 3、获取交易信息
		$trade = logic('pay')->TradeData($payment);
		$trade || exit($this->Ends());
				if ($payment['code'] == 'alipay' || $payment['code'] == 'tenpay')
		{
			if (ini('payment.lp.enabled'))
			{
				if (MEMBER_ID)
				{
										header('Location: m.php?mod=buy&code=order&op=process&sign='.$trade['sign']);
					exit;
				}
			}
		}
		# 4、订单后续处理
		
		$parserAPI = logic('callback')->Parser($trade);
		$parserAPI->MasterIframe($this);

		if(false === logic('order')->is_virtual_order($trade['sign'])) {
			
						preg_match('/^[a-z_]+$/i', $status) || exit($this->Ends());
			
						if (in_array($status, array('WAIT_SELLER_SEND_GOODS', 'FINISHED')) && logic('yungou')->isYunGouOrder($trade['sign'])) {
				$parserAPI->parse_yunGouPayment($payment, array(), $status);
			} else {
				$code = 'Parse_'.$status;
				method_exists($parserAPI, $code) || exit($this->Ends());
				$parserAPI->$code($payment);
			}
		} else {
						$parserAPI->Virtual_Order_Process($payment, $trade, $status);
		}
	}

	private function Ends()
	{
		echo 'woo.^_^.oow';
	}

}