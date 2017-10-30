<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name allinpay.php
 * @date 2015-04-24 17:05:16
 */
 



class allinpayPaymentDriver extends PaymentDriver
{
	
	private $is_notify = null;
	
    public function inner_disabled()
    {
        return WEB_BASE_ENV_DFS::$APPNAME == 'index';
    }
	
    public function CreateLink($payment, $parameter)
    {
    	$is_wap = in_array(WEB_BASE_ENV_DFS::$APPNAME, array('wap', '3g', 'api', 'sdk', ));
    	$data = $this->getdata($payment, $parameter, $is_wap);
    	if($is_wap) {
    		    		return json_encode($data);
    	} else {
    		    	}
    	    }

    
    public function CreateConfirmLink($payment, $order)
    {
        return '?mod=buy&code=tradeconfirm&id='.$order['orderid'];
    }
    
    public function CallbackVerify($payment)
    {
    	$result = $_POST;
    	unset($result['mod']);

		if($result) {
    		$signMsg = $result['signMsg'];
    		unset($result['signMsg']);

			if($signMsg == $this->signMsg($result, $payment) && 1 == $result['payResult']) {
				$order = logic('callback')->Bridge($result['orderNo'])->SrcOne($result['orderNo'], true);
				if($order && $order['paytype'] == $payment['id']){
					if($order['product']['type'] == 'ticket'){
						return 'TRADE_FINISHED';
					}else{
						return 'WAIT_SELLER_SEND_GOODS';
					}
				}else{
					return 'VERIFY_FAILED';
				}
			}else{
				return 'VERIFY_FAILED';
			}
		}else{
			return 'VERIFY_FAILED';
		}
    }
    
    public function GetTradeData()
    {
        $trade = array();
        $result = $_POST;
		if($result['orderNo']) {
			$order = logic('callback')->Bridge($result['orderNo'])->SrcOne($result['orderNo'], true);
			$trade['sign'] = $result['orderNo'];
			$trade['trade_no'] = $result['paymentOrderId'];
			$trade['price'] = round($result['orderAmount']/100, 2);
			$trade['money'] = $trade['price'];
			$trade['status'] = (($order['product']['type'] == 'ticket') ? 'TRADE_FINISHED' : 'WAIT_SELLER_SEND_GOODS');
			$trade['uid'] = $order['userid'];
		}
		return $trade;
    }
    
    public function StatusProcesser($status)
    {
    	if(true === $this->__Is_Nofity()) {
    		return false;
    	}
		if ($status != 'VERIFY_FAILED')
        {
            echo 'success';
        }
        else
        {
            echo 'failed';
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
    }

	private function getdata($payment, $parameter, $is_wap = false) {
		$data = array(
			'inputCharset' => '1',
			'receiveUrl' => $parameter['notify_url'],
			'version' => 'v1.0',
			'signType' => '0',
			'merchantId' => $payment['config']['merchantId'],
			'orderNo' => $parameter['sign'],
			'orderAmount' => ($parameter['price'] * 100),
			'orderCurrency' => '0',
			'orderDatetime' => date('YmdHis', ($parameter['time'] ? $parameter['time'] : time())),
			'productName' => (true !== ENC_IS_GBK ? $parameter['name'] : array_iconv('gbk', 'utf-8', $parameter['name'])),
			'payType' => '0',
		);
		$data['signMsg'] = $this->signMsg($data, $payment);

		return $data;
	}

	private function signMsg($data, $payment) {
		$bufSignSrc = '';
		foreach($data as $k=>$v) {
			$v = trim($v);
			if('' != $v) {
				$bufSignSrc .= "{$k}={$v}&";
			}
		}
		$bufSignSrc .= "key=" . $payment['config']['merchantKey'];
		return strtoupper(md5($bufSignSrc));
	}

	
	private function __Is_Nofity()
	{
		if (is_null($this->is_notify))
		{
			if (post('payResult'))
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

	private function build_form($action, $data, $is_wap = false) {
		$form_data = '';
		foreach($data as $k=>$v) {
			$form_data .= '<input type="hidden" name="'.$k.'" value="'.$v.'" />';
		}
		return '<div style="display:'.($is_wap ? 'none' : 'block').';"><form id="pay_submit" name="pay_submit" method="POST" action="'.$action.'">'.$form_data.'<input type="submit" value="'.($is_wap ? 'Allinpay online payment' : '通联在线支付').'" /></form><script type="text/javascript">document.forms["pay_submit"].submit();</script></div>';
	}

}
