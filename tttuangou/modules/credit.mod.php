<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name credit.mod.php
 * @date 2016-05-05 15:32:45
 */




class ModuleObject extends MasterObject
{
    function ModuleObject( $config )
    {
        $this->MasterObject($config);
        if (MEMBER_ID < 1)
        {
            $this->Messager(__('请先登录！'), '?mod=account&code=login');
        }
        $runCode = Load::moduleCode($this);
        $this->$runCode();
    }
    public function main()
    {
        $credit_conf = ini('credit');
        $scores = user()->get('scores');
        $seller_info = logic('seller')->GetOne(null,MEMBER_ID);
        $money = $seller_info['money'];
        $seller_name = $seller_info['sellername'];
        $total_money = $seller_info['total_money'];
        $account_money = $seller_info['account_money'];
        $forbid_money = $seller_info['forbid_money'];
        $assure_money = $seller_info['assure_money'];
        include handler('template')->file('credit_buy');
    }
    
    public function del()
    {
        $id = $this->__orderid();
        $order = logic('recharge')->GetOne($id);
        if (!$order)
        {
            $this->Messager('订单编号无效！', -1);
        }
        if ($order['userid'] != MEMBER_ID)
        {
            $this->Messager('您无权操作此订单！', -1);
        }
        if ($order['paytime'] > 0)
        {
            $this->Messager('已经充值成功订单，无法删除！', -1);
        }
        logic('recharge')->del($id);
        $this->Messager('删除成功！');
    }
    public function order()
    {
        $credit_conf = ini('credit');
        $scores = user()->get('scores');
        $seller_info = logic('seller')->GetOne(null,MEMBER_ID);
        $money = $seller_info['money'];
        $seller_name = $seller_info['sellername'];
        $total_money = $seller_info['total_money'];
        $account_money = $seller_info['account_money'];
        $forbid_money = $seller_info['forbid_money'];
        $assure_money = $seller_info['assure_money'];
        if($_GET['id']){
            $navcss1 = '1';$navcss2 = '2';
            $id = $this->__orderid();
            $order = logic('credit')->GetOne($id);
            if (!$order)
            {
                $this->Messager('订单编号无效！', -1);
            }
            if ($order['userid'] != MEMBER_ID)
            {
                $this->Messager('您无权操作此订单！', -1);
            }
        }else{
            $navcss1 = '2';$navcss2 = '1';$tabcssall = $tabcssno = $tabcssyes = '3';
            $order_list = true;
            $paystatus = get('pay');
            $where = ' ptype = 1 AND userid = ' . MEMBER_ID;
            if ($paystatus)
            {
                if ($paystatus == 'no')
                {
                    $where .= ' AND paytime = 0';
                    $tabcssno = '2';
                }
                elseif($paystatus == 'yes')
                {
                    $where .= ' AND paytime > 0';
                    $tabcssyes = '2';
                }
                else
                {
                    $tabcssall = '2';
                }
            }
            else
            {
                $tabcssall = '2';
            }
            $list = logic('credit')->GetList($where);
        }
        include handler('template')->file('credit_buy_order');
    }
    public function order_save()
    {
        $credit_num = (int)post('credit_num');
        if (!$credit_num || $credit_num <= 0)
        {
            $this->Messager('积分数无效！', -1);
        }
        $credit = ini('credit');
        $money = $credit_num * $credit['buy_money'];
        $order = logic('credit')->GetFree($money,1);
        header('Location: '.rewrite('?mod=credit&code=order&id='.$order['orderid']));
    }
    public function payment_save()
    {
        $id = $this->__orderid();
        $pid = post('payment_id', 'int');
        $ibank = post('PaymentType', 'txt');
        $test = logic('credit')->GetOne($id);
        if (!$test)
        {
            $this->Messager('订单编号无效！', -1);
        }
        logic('credit')->Update($id, array('payment'=>$pid));
        header('Location: '.rewrite('?mod=credit&code=pay&id='.$id.'&ibank='.$ibank));
    }
    public function pay()
    {
        $credit_conf = ini('credit');
        $scores = user()->get('scores');
        $seller_info = logic('seller')->GetOne(null,MEMBER_ID);
        $money = $seller_info['money'];
        $seller_name = $seller_info['sellername'];
        $total_money = $seller_info['total_money'];
        $account_money = $seller_info['account_money'];
        $forbid_money = $seller_info['forbid_money'];
        $assure_money = $seller_info['assure_money'];
        $id = $this->__orderid();
        $order = logic('credit')->GetOne($id);
        if (!$order)
        {
            $this->Messager('订单编号无效！', -1);
        }
        if ($order['payment'] == 0)
        {
            $this->Messager('请选择支付方式！', '?mod=credit&code=order&id='.$id);
        }
        if ($order['paytime'] > 0 || $order['status'] != RECHARGE_STA_Blank)
        {
            $this->Messager('此订单已经支付过了！', '?mod=me&code=bill');
        }
        $pay = logic('pay')->GetOne($order['payment']);
        $pay['code']=='yeepay' && $pay['site'] = 'pc_web';
        if (!$pay)
        {
            $this->Messager('无效的支付方式！', -1);
        }
        if ($order['userid'] != MEMBER_ID)
        {
            $this->Messager('您无权操作此订单！', -1);
        }
        $parameter = array(
            'name' => '积分购买（'.$id.'）',
            'detail' => '支付：'.$order['money'].'元，购买编号：'.$id,
            'price' => $order['money'],
            'sign' => $order['orderid'],
            'notify_url' => ini('settings.site_url').'/index.php?mod=callback&pid='.$pay['id'],
            'product_url' => ini('settings.site_url').'/index.php?mod=me&code=bill',
            'userid' => $order['userid'],
            'productid' => $order['userid'],
            'time' => $order['createtime'],
                    );
        if ($pay['code']== 'bankdirect') {
            $orderinfo['price'] = $order['money'];
            $orderinfo['sign'] = $order['orderid'];
            echo logic('pay')->apiz($pay['code'])->CreatForm($pay, $orderinfo);
            exit;
        }
        $payment_linker = logic('pay')->Linker($pay, $parameter);

        include handler('template')->file('creditbuy_pay');
    }
    private function __orderid()
    {
        $id = get('id', 'number');
        if (!$id || strlen($id) != 13)
        {
            $this->Messager('请输入正确的订单编号！', -1);
        }
        return $id;
    }

}