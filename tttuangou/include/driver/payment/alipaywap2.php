<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name alipaywap2.php
 * @date 2015-09-28 17:03:51
 */






class alipaywap2PaymentDriver extends PaymentDriver {

    
    public function inner_disabled()
    {
        return WEB_BASE_ENV_DFS::$APPNAME == 'index';
    }

    public function CreateLink($payment, $parameter)
    {
    	$alipay_config = $this->_alipay_config($payment);

		

		include DRIVER_PATH.'payment/alipay.sdk/alipay_submit.class.php';

		        $parameter['name'] = preg_replace('/\&[a-z]{2,4}\;/i', '', trim(strip_tags($parameter['name'])));
        $parameter['detail'] = str_replace(array('"',"'",'\\','&',"\r","\n",'{','}',';','#','%','^','*','[',']','|','/','.','?',
            '~','`','!','@','$','(',')','-','_','+','=',':','<','>',',','.'), '',
            trim(strip_tags($parameter['detail'])));

        

						$notify_url = ini('settings.site_url') . "/api/pay_callback_alipaywap2.php";
				$return_url = $notify_url;

				$out_trade_no = $parameter['sign'];
		
						$subject = $parameter['sign'];
		
				$total_fee = $parameter['price'];
		
				$parameter = array(
				"service" => "alipay.wap.create.direct.pay.by.user",
				"partner" => trim($alipay_config['partner']),
				"seller_id" => trim($alipay_config['seller_id']),
								"payment_type"	=> "1", 				"notify_url"	=> $notify_url,
				"return_url"	=> $return_url,
				"out_trade_no"	=> $out_trade_no,
				"subject"	=> $subject,
				"total_fee"	=> $total_fee,
				"show_url"	=> $parameter['product_url'],
				
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);

				$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
		return $html_text;
    }

	public function CreateConfirmLink($payment, $order)
    {
        return '?mod=buy&code=tradeconfirm&id='.$order['orderid'];
    }

    public function CallbackVerify($payment)
    {
    	include DRIVER_PATH.'payment/alipay.sdk/alipay_notify.class.php';
		$alipayNotify = new AlipayNotify($this->_alipay_config($payment));
		if ($this->__Is_Nofity())
		{
			sleep(rand(1, 9));
			$verify_result = $alipayNotify->verifyNotify();
			$trade_status = $_POST['trade_status'];
		}
		else
		{
			$verify_result = $alipayNotify->verifyReturn();
			$trade_status = $_GET['trade_status'];
		}
		return $this->__Trade_Status(($verify_result && in_array($trade_status, array('TRADE_FINISHED', 'TRADE_SUCCESS'))) ? 'TRADE_SUCCESS' : 'VERIFY_FAILED');
    }

    public function GetTradeData()
    {
        $src = ($this->__Is_Nofity()) ? 'POST' : 'GET';
        $trade = array();
		$trade['sign'] = logic('safe')->Vars($src, 'out_trade_no', 'number');
		$trade['trade_no'] = logic('safe')->Vars($src, 'trade_no', 'number');
		$trade['price'] = logic('safe')->Vars($src, 'total_fee', 'number');
		$trade['money'] = $trade['price'];
        $trade['status'] = 'TRADE_FINISHED';
		$order = logic('callback')->Bridge($trade['sign'])->SrcOne($trade['sign'], true);
		$trade['uid'] = $order['userid'];
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
    }


    private function __Trade_Status($trade_status)
    {
		return ($trade_status == 'TRADE_SUCCESS') ? 'TRADE_FINISHED' : $trade_status;
	}

	private function __Is_Nofity()
	{
		if (is_null($this->is_notify))
		{
			if (post('out_trade_no'))
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

	private function _alipay_config($payment) {
		$alipay_config = array();
		$alipay_config['partner']	= $payment['config']['partner'];
		$alipay_config['key']	= $payment['config']['key'];
				$alipay_config['seller_id']	= $payment['config']['partner'];
		$alipay_config['sign_type']    = 'MD5';
		$alipay_config['input_charset']= ('gbk' == strtolower(ini('settings.charset')) ? 'gbk' : 'utf-8');
						$alipay_config['cacert']    = DRIVER_PATH.'payment/alipay.sdk/cacert.pem';
		$alipay_config['transport']    = 'http';
		return $alipay_config;
	}

}
?>