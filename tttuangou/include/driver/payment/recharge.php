<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name recharge.php
 * @date 2015-08-31 16:32:13
 */





class rechargePaymentDriver extends PaymentDriver
{
    private $errno = '0';

    public function CreateLink($payment, $parameter)
    {
        $html =  '<form action="?mod=callback&pid='.$payment['id'].'" method="post" id="zf_form">';
        $html .= '<input type="hidden" name="sign" value="'.$parameter['sign'].'" />';
        $html .= '<div class="field" ><label>充值卡号码：</label><input id="recharge_card" type="text" name="number" class="f_input l_input" /><span style=" display:block; padding-top:5px; float:left;"><font id="query_result"></font></span></div>';
        $html .= '<div class="field"><label>充值卡密码：</label><input type="password" name="password" class="f_input l_input" /></div>';
        $html .= '<p style="margin-left: 110px;font-size: 12px;"><b>注意：实际充值金额是您的充值卡面额</b></p>';
        $html .= '<div class="act" id="l_act" style="width:400px;"><input type="submit" class="btn btn-primary" value=" 充 值 " /></div>';
        $html .= '</form>';
        $html .= ui('loader')->js('@recharge.pay');
        return $html;
    }
    public function CreateConfirmLink($payment, $order)
    {
        return '?mod=buy&code=tradeconfirm&id='.$order['orderid'];
    }
    public function CallbackVerify($payment)
    {
        
        $trade = $this->GetTradeData();
        if ($trade['__order__']['paytype'] != $payment['id'])
        {
            $this->errno = '-1';
            return 'VERIFY_FAILED';
        }
        if (!$trade['number'] || !$trade['password'])
        {
            $this->errno = '-2';
            return 'VERIFY_FAILED';
        }
        $card = logic('recharge')->card()->ifo($trade['number']);
        if(false == $card) {
            $this->errno = '-3';
            return 'VERIFY_FAILED';            
        }
        if ($card['usetime'] > 0)
        {            
            $this->errno = '-4';
            return 'VERIFY_FAILED';
        }
        if ($card['status'] != RECHARGE_CARD_STA_Normal)
        {            
            $this->errno = '-5';
            return 'VERIFY_FAILED';
        }
        if($card['password'] != $trade['password']) {
            $this->errno = '-6';
            return 'VERIFY_FAILED';
        }
                logic('recharge')->Update($trade['sign'], array('money'=>$card['price']));
        return $trade['status'];
    }
    public function GetTradeData()
    {
        $sign = post('sign', 'number');
        $order = logic('callback')->Bridge($sign)->SrcOne($sign);
        $trade = array();
        $trade['sign'] = $sign;
        $trade['trade_no'] = time();
        $trade['price'] = $order['money'];
        $trade['status'] = 'TRADE_FINISHED';
        $trade['__order__'] = $order;
        $trade['number'] = post('number', 'number');
        $trade['password'] = post('password', 'number');
        $trade['userid'] = $order['userid'];
        return $trade;
    }
    public function StatusProcesser($status)
    {
        if ($status == 'TRADE_FINISHED')
        {
            $trade = $this->GetTradeData();
            if($trade['number'] && $trade['password']) {
                logic('recharge')->card()->MakeUsed($trade['number'], $trade['password'], $trade['userid']);
            }                        
        }
        if(in_array(post('from'), array('wap'))) {
                        echo $this->errno;
            return true;
        }
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