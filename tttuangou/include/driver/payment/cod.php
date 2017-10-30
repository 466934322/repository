<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name cod.php
 * @date 2015-09-11 16:54:02
 */





class codPaymentDriver extends PaymentDriver
{
	public function CreateLink($payment, $parameter)
	{
		$html =  '<form action="?mod=callback&pid='.$payment['id'].'" method="post">';
		$html .= '<input type="hidden" name="sign" value="'.$parameter['sign'].'" />';
		$html .= '<p>请确认以上订单信息</p>';
		$html .= '<input type="submit" value="下单" class="b_btn " />';
		$html .= '</form>';
		return $html;
	}
	public function CreateConfirmLink($payment, $order)
	{
		return '?mod=buy&code=tradeconfirm&id='.$order['orderid'];
	}
	public function CallbackVerify($payment,$sign='')
	{
				if (user()->get('id') < 1)
		{
			return 'VERIFY_FAILED';
		}
		$trade = $this->GetTradeData($sign);
		if ($trade['__order__']['paytype'] != $payment['id'])
		{
			return 'VERIFY_FAILED';
		}
		return $trade['status'];
	}
	public function GetTradeData($sign='')
	{
	    if($sign == '')
		  $sign = post('sign', 'number');
		$order = logic('callback')->Bridge($sign)->SrcOne($sign);
		$trade = array();
		$trade['sign'] = $sign;
		$trade['trade_no'] = time();
		$trade['price'] = $order['paymoney'];
		$trade['money'] = 0;
		$trade['nmadd'] = true;
		$trade['nmpay'] = true;
		$trade['status'] = 'WAIT_SELLER_SEND_GOODS';
		$trade['__order__'] = $order;
		return $trade;
	}
	public function StatusProcesser($status)
	{
		return false;
	}
	public function GoodSender($payment, $express, $sign, $type)
	{
		logic('callback')->Bridge($sign)->Processed($sign, 'WAIT_BUYER_CONFIRM_GOODS');
	}
}

?>