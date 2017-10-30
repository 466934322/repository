<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name wxapppay.php
 * @date 2015-09-28 17:03:51
 */


require_once "wxsmpay/WxPay.Api.php";
require_once 'wxsmpay/WxPay.Notify.php';
require_once 'wxsmpay/log.php';
require_once "wxsmpay/WxPay.NativePay.php";
$logHandler= new CLogFileHandler(ROOT_PATH."include/driver/payment/wxsmpay/logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);
class wxapppayPaymentDriver extends PaymentDriver
{
    private $is_notify = null;

    
    public function inner_disabled()
    {
        return WEB_BASE_ENV_DFS::$APPNAME == 'index';
    }

        private function _init_config($payment){
        define(WxPayConfig_APPID,$payment['config']['appid']);
        define(WxPayConfig_MCHID,$payment['config']['mch_id']);
        define(WxPayConfig_KEY,$payment['config']['key']);
        define(WxPayConfig_APPSECRET,$payment['config']['appsecret']);
    }

	public function CreateLink($payment, $parameter)
	{
        $this->_init_config($payment);
        
                $input = new WxPayUnifiedOrder();
                $input->SetBody($parameter['sign']);
                $input->SetOut_trade_no($parameter['sign']);
        $input->SetTotal_fee($parameter['price']*100);
                                $input->SetNotify_url(ini('settings.site_url') . '/api/pay_callback_wxapppay.php');
        $input->SetTrade_type("APP");
        $input->SetProduct_id($parameter['productid']);
        $notify = new NativePay();
        $result = $notify->GetPayUrl($input);
        $data['appid'] = WxPayConfig_APPID;
        $data['noncestr'] = random(32);
        $data['partnerid'] = WxPayConfig_MCHID;
        $data['prepayid'] = $result['prepay_id'];
        $data['package'] = 'Sign=WXPay';
        $data['timestamp'] = time();
        $data['sign'] = strtoupper(md5('appid='.WxPayConfig_APPID.'&noncestr='.$data['noncestr'].'&package=Sign=WXPay&partnerid='.WxPayConfig_MCHID.'&prepayid='.$data['prepayid'].'&timestamp='.$data['timestamp'].'&key='.WxPayConfig_KEY));
        return $data;
	}

	public function CreateConfirmLink($payment, $order)
	{
		return '?mod=buy&code=tradeconfirm&id='.$order['orderid'];
	}
	public function CallbackVerify($payment)
    {
        $this->_init_config($payment);
                $notify = new WxPayNotify();
        $notify ->Handle(true);
        $notify_result = $notify->notify_data;

                        if($notify_result['return_code'] === "SUCCESS") {

            $order = logic('order')->GetOne($notify_result["out_trade_no"]);
            $this->wxreturn=$notify_result;
            if($order && $order['paytype'] == $payment['id']){
                if($order['product']['type'] == 'ticket') {
                    return 'TRADE_FINISHED';
                } else {
                    return 'WAIT_SELLER_SEND_GOODS';
                }
            }

        }

        return 'VERIFY_FAILED';
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


        public function Queryorder($transaction_id)
    {
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = WxPayApi::orderQuery($input);
                if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            return true;
        }
        return false;
    }
}
?>