<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name wxpay.php
 * @date 2015-11-12 16:33:06
 */


class wxpayPaymentDriver extends PaymentDriver
{
    private $is_notify = null;

    
    public function inner_disabled()
    {
        return WEB_BASE_ENV_DFS::$APPNAME == 'index';
    }

	public function CreateLink($payment, $parameter)
	{
		        $parameter['name'] = preg_replace('/\&[a-z]{2,4}\;/i', '', trim(strip_tags($parameter['name'])));
        $parameter['detail'] = str_replace(array('"',"'",'\\','&',"\r","\n",'{','}',';','#','%','^','*','[',']','|','/','.','?',
            '~','`','!','@','$','(',')','-','_','+','=',':','<','>',',','.'), '', 
            trim(strip_tags($parameter['detail'])));

        		$parameter['site_url'] = ini('settings.site_url');

                $parameter['notify_url'] = $parameter['site_url'] . '/wxpay/callback.php';

				return $this->__BuildForm($payment, $parameter);
	}

	public function CreateConfirmLink($payment, $order)
	{
		return '?mod=buy&code=tradeconfirm&id='.$order['orderid'];
	}
	public function CallbackVerify($payment)
    {
    	unset($_POST['mod']);
		if (!class_exists('Notify_pub')){
			require_once INCLUDE_PATH . 'driver/payment/wxpay/WxPayPubHelper.php';
		}
		$notify = new Notify_pub($payment['config']['appid'],$payment['config']['mch_id'],$payment['config']['key'],$payment['config']['appsecret']);

		        
        $xml = file_get_contents('php:/'.'/input');

		if($xml) {
			$notify->saveData($xml);
			if($notify->checkSign() == FALSE) {
				$notify->setReturnParameter("return_code","FAIL");
				$notify->setReturnParameter("return_msg","签名失败");

				return 'VERIFY_FAILED';
			} else {
                                $order_query = new OrderQuery_pub($payment['config']['appid'],$payment['config']['mch_id'],$payment['config']['key'],$payment['config']['appsecret']);
                $order_query->setParameter('transaction_id', $notify->data['transaction_id']);
                $result = $order_query->getResult();
                if(array_key_exists("return_code", $result)
                    && array_key_exists("result_code", $result)
                    && $result["return_code"] == "SUCCESS"
                    && $result["result_code"] == "SUCCESS") {
                    $notify->setReturnParameter("return_code","SUCCESS");

                    $order = logic('order')->GetOne($result["out_trade_no"]);
                    $this->wxreturn=$result;
                    if($order && $order['paytype'] == $payment['id']){
                        if($order['product']['type'] == 'ticket') {
                            return 'TRADE_FINISHED';
                        } else {
                            return 'WAIT_SELLER_SEND_GOODS';
                        }
                    } else {
                        return 'VERIFY_FAILED';
                    }
                } else {
                    $notify->setReturnParameter("return_code","FAIL");
                    $notify->setReturnParameter("return_msg","订单状态回查失败");

                    return 'VERIFY_FAILED';
                }
			}
		} else {
			return 'VERIFY_FAILED';
		}
    }
    public function GetTradeData()
    {
        $trade = array();
		$return = $this->wxreturn;
		if($return && is_array($return)){
			$order = logic('order')->GetOne($return['out_trade_no']);
			$trade['sign'] = $return['out_trade_no'];
			$trade['trade_no'] = $return['transaction_id'];
			$trade['price'] = round($return['total_fee']/100, 2);
			$trade['money'] = $trade['price'];
			$trade['status'] = ($order['product']['type'] == 'ticket') ? 'TRADE_FINISHED' : 'WAIT_SELLER_SEND_GOODS';
			$trade['uid'] = $order['userid'];
		}
		return $trade;
    }
    
    public function StatusProcesser($status)
    {
        if (!$this->__Is_Nofity())
        {
            return false;
        }
        if ($status != 'VERIFY_FAILED')
        {
            echo 'success';
        }
        else
        {
            echo 'fail';
        }
        return true;
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
        return;
    }
    private function __Is_Nofity()
    {
        if (is_null($this->is_notify))
        {
            if (count($_COOKIE) == 0)
            {
                $this->is_notify = true;
            }
            else
            {
                $this->is_notify = false;
            }
        }
        return $this->is_notify;
    }
	private function __BuildForm($payment, $parameter)
	{
		$sHtml = '<form id="pay_submit" name="pay_submit" action="'.ini('settings.site_url').'/wxpay/index.php" method="post">';
		$sHtml .= '<input type="hidden" name="sign" value="'.$parameter['sign'].'" />';
		
		$sHtml .= '<input type="submit" value="微信支付" class="formbutton formbutton_ask">';
		$sHtml .= '</form>';
		$sHtml .= '<script>document.forms["pay_submit"].submit();</script>';
		return $sHtml;
	}
}
?>