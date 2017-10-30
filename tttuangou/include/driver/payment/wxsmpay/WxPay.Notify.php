<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name WxPay.Notify.php
 * @date 2015-09-28 17:03:51
 */



class WxPayNotify extends WxPayNotifyReply
{
    public $notify_data;
	
	final public function Handle($needSign = true)
	{
		$msg = "OK";
        				$result = WxpayApi::notify(array($this, 'NotifyCallBack'), $msg);
		if($result == false){
			$this->SetReturn_code("FAIL");
			$this->SetReturn_msg($msg);
			$this->ReplyNotify(false);
			return;
		} else {
						$this->SetReturn_code("SUCCESS");
			$this->SetReturn_msg("OK");
		}
		$this->ReplyNotify($needSign);
	}

	
	public function NotifyProcess($data, &$msg)
	{
				
		$transaction_id = $data['transaction_id'];
		if(empty($transaction_id)) {
			$msg = '输入参数不正确';
			return false;
		}

		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
				if( $result["trade_state"] = "SUCCESS"
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			$this->notify_data = $result;

			return true;
		}
		$msg = '订单查询失败';
		return false;
	}

	
	final public function NotifyCallBack($data)
	{
		$msg = "OK";
		$result = $this->NotifyProcess($data, $msg);

		if($result == true){
			$this->SetReturn_code("SUCCESS");
			$this->SetReturn_msg("OK");
		} else {
			$this->SetReturn_code("FAIL");
			$this->SetReturn_msg($msg);
		}
		return $result;
	}

	
	final private function ReplyNotify($needSign = true)
	{
				if($needSign == true &&
			$this->GetReturn_code($return_code) == "SUCCESS")
		{
			$this->SetSign();
		}
		WxpayApi::replyNotify($this->ToXml());
	}
}