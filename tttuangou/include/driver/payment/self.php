<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name self.php
 * @date 2016-05-05 15:32:45
 */





class selfPaymentDriver extends PaymentDriver
{
	public function CreateLink($payment, $parameter)
	{
		$market_account = dbc(DBCMax)->select('members')->where(array('uid'=>MEMBER_ID))->limit(1)->done();		
		if($market_account['email2']){
			$url  	 =  rewrite('?mod=me&code=setting');
			$market_note = '&nbsp;&nbsp;<a href="'.$url.'" target="_blank">点此修改登录密码</a>';
		}

		$html =  '<form action="?mod=callback&pid='.$payment['id'].'" method="post">';
		$html .= '<input type="hidden" name="sign" value="'.$parameter['sign'].'" />';
		$html .= '<div class="payment-line">登录密码：<input type="password" name="password"/></div>';
		$html .= '<div class="payment-btn"><input type="submit" value=" 提交 " class="btn btn-primary"/></div>'.$market_note;
		$html .= '</form>';
		return $html;
	}
	public function CreateConfirmLink($payment, $order)
	{
		return '?mod=buy&code=tradeconfirm&id='.$order['orderid'];
	}
	
	#支付验证
	#返回支付状态
	public function CallbackVerify($payment)
	{
		#第一步，是否登录
				$user = user()->get();
		if ($user['id'] < 1)
		{
			return 'VERIFY_FAILED';
		}
        $sign = post('sign', 'number');
		$password = post('password', 'txt');
		#第二步，支付密码是否正确
		$password_hash = account()->password($password, $user);
		if(empty($password) || false == in_array($user['password'], array($password, md5($password), $password_hash, md5($password_hash)))) {
			return 'VERIFY_FAILED';
		}
		#第三步，支付方式是否跟订单中支付方式一致
                $trade = logic('credit')->GetOne($sign);
        if($trade){
            $trade['price'] = $trade['money'];
            if($trade['payment'] != $payment['id']){
                return 'VERIFY_FAILED';
            }
        }else{
            $trade = $this->GetTradeData();
            if ($trade['__order__']['paytype'] != $payment['id'])
            {
                return 'VERIFY_FAILED';
            }
        }

		#第四步，校验一下用户帐户余额是否大于要支付的订单金额
		if(false == logic('me')->money()->check($user['id'], $trade['price'])) {
			return 'VERIFY_FAILED';
		}
		return $trade['status'];
	}
	
	#根据订单号取出product(并且支付状态做修改)，如不存在该product，返回空数组
	public function GetTradeData($sign = '')
	{
		if(empty($sign)) {
			$sign = post('sign', 'number');
		}
                $credit_order = logic('credit')->GetOne($sign);
		$order = logic('callback')->Bridge($sign)->SrcOne($sign);
		$order && $product = logic('product')->SrcOne($order['productid']);
		$trade = array();
        $trade['sign'] = $sign;
        $trade['trade_no'] = time();
        $trade['price'] = $credit_order ? $credit_order['money'] : $order['paymoney'];
        $trade['money'] = 0;
        $trade['nmadd'] = true;
        $trade['status'] = ($product['type'] == 'ticket' || $order['productid'] == 99999 || $credit_order) ? 'TRADE_FINISHED' : 'WAIT_SELLER_SEND_GOODS';
        $trade['__order__'] = $order;
        return $trade;
	}
	public function StatusProcesser($status)
	{
		return false;
	}
	public function GoodSender($payment, $express, $sign, $type)
	{
		if ($type == 'ticket')
		{
			logic('callback')->Bridge($sign)->Processed($sign, 'TRADE_FINISHED');
		}
		else
		{
			logic('callback')->Bridge($sign)->Processed($sign, 'WAIT_BUYER_CONFIRM_GOODS');
		}
	}
}

?>