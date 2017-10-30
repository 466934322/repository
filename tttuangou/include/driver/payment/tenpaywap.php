<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name tenpaywap.php
 * @date 2015-09-28 17:03:51
 */





class tenpaywapPaymentDriver extends PaymentDriver
{
    private $is_notify = null;
    
    public function inner_disabled()
    {
        return WEB_BASE_ENV_DFS::$APPNAME == 'index';
    }
    
    public function CreateLink($payment, $parameter)
    {
        require_once (DRIVER_PATH . "payment/tenpaywap/RequestHandler.class.php");
        require (DRIVER_PATH . "payment/tenpaywap/client/ClientResponseHandler.class.php");
        require (DRIVER_PATH . "payment/tenpaywap/client/TenpayHttpClient.class.php");

        $is_wap = ('m' == WEB_BASE_ENV_DFS::$APPNAME);

        $callback_url = ini('settings.site_url') . "/api/pay_callback_tenpaywap.html";
        $notify_url = ini('settings.site_url') . "/api/pay_callback_tenpaywap.php";
        if($is_wap) {
            $callback_url = $notify_url;
        }

        
        $reqHandler = new RequestHandler();
        $reqHandler->init();
        $reqHandler->setKey($payment['config']['key']);
                        $reqHandler->setGateUrl("http://wap.tenpay.com/cgi-bin/wappayv2.0/wappay_init.cgi");


        $httpClient = new TenpayHttpClient();
                $resHandler = new ClientResponseHandler();
                                $reqHandler->setParameter("charset", (true === ENC_IS_GBK ? "2" : "1"));
        $reqHandler->setParameter("fee_type", "1");
        $reqHandler->setParameter("total_fee", $parameter['price'] * 100);                  $reqHandler->setParameter("spbill_create_ip", client_ip());        $reqHandler->setParameter("ver", "2.0");        $reqHandler->setParameter("bank_type", "0");         $reqHandler->setParameter("callback_url", $callback_url);        $reqHandler->setParameter("bargainor_id", $payment['config']['bargainor_id']);         $reqHandler->setParameter("sp_billno", $parameter['sign']);         $reqHandler->setParameter("notify_url", $notify_url);        $reqHandler->setParameter("desc", $parameter['name']);


        $httpClient->setReqContent($reqHandler->getRequestURL());

        $reqUrl = '';

                if($httpClient->call()) {

            $resHandler->setContent($httpClient->getResContent());
                        $token_id = $resHandler->getParameter('token_id');
            $reqHandler->setParameter("token_id", $token_id);

                                                            $reqUrl = "http://wap.tenpay.com/cgi-bin/wappayv2.0/wappay_gate.cgi?token_id=".$token_id;

            if($is_wap) {
                return $this->__BuildForm($reqUrl);
            }

        }

        return $reqUrl;
    }
    
    public function CreateConfirmLink($payment, $order)
    {
        return '?mod=buy&code=tradeconfirm&id='.$order['orderid'];
    }

    
    private function __resHandler($payment = array()) {
        static $resHandler = null;

        if(is_null($resHandler) && $payment) {
            require_once (DRIVER_PATH . "payment/tenpaywap/ResponseHandler.class.php");
            require_once (DRIVER_PATH . "payment/tenpaywap/WapResponseHandler.class.php");

            $resHandler = new WapResponseHandler();
            $resHandler->setKey($payment['config']['key']);
        }

        return $resHandler;
    }

    
    public function CallbackVerify($payment)
    {
    	if ($this->__resHandler($payment)->isTenpaySign()) {
            if("0" == $this->__resHandler()->getParameter("pay_result")) {
                $orderid = $this->__resHandler()->getParameter('sp_billno');
                $order = logic('callback')->Bridge($orderid)->SrcOne($orderid, true);
                if($order && $order['paytype'] == $payment['id']){
                    if($order['product']['type'] == 'ticket'){
                        return 'TRADE_FINISHED';
                    }else{
                        return 'WAIT_SELLER_SEND_GOODS';
                    }
                }else{
                    return 'VERIFY_FAILED';
                }
            }
        }
        return 'VERIFY_FAILED';
    }
    
    public function GetTradeData()
    {
        $trade = array();
        if(false != ($orderid = $this->__resHandler()->getParameter('sp_billno'))) {
            $order = logic('callback')->Bridge($orderid)->SrcOne($orderid, true);
            $trade['sign'] = $orderid;
            $trade['trade_no'] = $this->__resHandler()->getParameter('transaction_id');
            $trade['price'] = (float) ($this->__resHandler()->getParameter('total_fee') / 100);
            $trade['money'] = $trade['price'];
            $trade['status'] = (($order['product']['type'] == 'ticket') ? 'TRADE_FINISHED' : 'WAIT_SELLER_SEND_GOODS');
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
    
    private function __BuildForm($url)
    {
        $sHtml = "<a id='pay_submit' name='tenpaywapsubmit' href='$url' target='_self' class='formbutton formbutton_ask'>财付通付款</a>";

        $sHtml .= "<script>document.getElementById('pay_submit').click();</script>";

        return $sHtml;
    }
}

?>