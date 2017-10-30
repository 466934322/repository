<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name wxsmpay.php
 * @date 2016-07-25 18:45:53
 */


require_once "wxsmpay/WxPay.Api.php";
require_once 'wxsmpay/WxPay.Notify.php';
require_once 'wxsmpay/log.php';
require_once "wxsmpay/WxPay.NativePay.php";
$logHandler= new CLogFileHandler(ROOT_PATH."include/driver/payment/wxsmpay/logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);
class wxsmpayPaymentDriver extends PaymentDriver
{
    private $is_notify = null;

    
    public function inner_disabled()
    {
            }

        private function _init_config($payment){
        if(empty($payment['config']['appid']) || empty($payment['config']['mch_id']) || empty($payment['config']['key']) || empty($payment['config']['appsecret'])) {
            errorlog('wxsmpayPaymentDriver::_init_config error $payment : ' . var_export($payment, true), 'wxsmpay');
        }
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
                                $input->SetNotify_url(ini('settings.site_url') . '/api/pay_callback_wxsmpay.php');
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($parameter['productid']);
        $notify = new NativePay();
        $result = $notify->GetPayUrl($input);
        $url2 = $result["code_url"];
        if(empty($url2)) {
            errorlog('wxsmpayPaymentDriver::CreateLink error $payment : ' . var_export($payment, true) . ' $parameter : ' . var_export($parameter, true) . ' $result : ' . var_export($result, true), 'wxsmpay');
        }
        $img_url = driver('qrcode')->url($url2);
                $img = '<p style="text-align: center">请使用微信扫描以下二维码完成支付</p>';
        $img .= '<div style="text-align: center"><img src="'.$img_url.'"></div>';
                $img .= '<script>';
        $img .= 'var t1;var sum = 0;';
        $img .= 'function update_native_status(){
        sum++;';
        $img .= 'if(sum>600){window.clearInterval(t1);return false;}';
        $img .= 'if(sum>180){
                        m=sum % 10;if(m!=0){
                        return false;}
                 }';
        $img .= '$.get("'.ini('settings.site_url') .'/index.php?mod=me&code=native_Status&id='.$parameter['sign'].'",function(data){';
        $img .= 'if(data == "success"){window.clearInterval(t1);';
        $img .= '$("p").html("支付成功!");window.location.href="'.ini('settings.site_url') .'/index.php?mod=me&code=order";$("div>img").hide()}else{
        window.setInterval(update_native_status(),1000);
        }});}';
        $img .= 't1 = window.setInterval(update_native_status(),1000);';
        $img .= '</script>';
        return $img;
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

                        $order = logic('callback')->Bridge($notify_result["out_trade_no"])->SrcOne($notify_result["out_trade_no"], true);
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
						$order = logic('callback')->Bridge($return["out_trade_no"])->SrcOne($return["out_trade_no"], true);
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

