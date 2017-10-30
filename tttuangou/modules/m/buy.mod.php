<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name buy.mod.php
 * @date 2016-05-25 16:51:50
 */





class ModuleObject extends MasterObject
{
    function ModuleObject( $config )
    {
        $this->MasterObject($config);
        if (MEMBER_ID < 1)
        {
            $this->Messager(__('请先登录！'), 'm.php?mod=account&code=login');
        }
        $runCode = Load::moduleCode($this);
        $this->$runCode();
    }
    
    function Main()
    {
        header('Location: .');
    }
    
    function Checkout()
    {
        $this->Title = __('提交订单');
        $id = get('id', 'int');
        #购物车
        if($id < 0)
        {
            $cartItems = logic('cart_manage')->GetItems();
            $cartItems = logic('cart_manage')->filter($cartItems);
            if(empty($cartItems))
            {
                header('Location: ?');
                exit;
            }

        }
        #立即抢购
        else
        {
            $product = logic('product')->BuysCheck($id);
            if (isset($product['false']))
            {
                $this->Messager($product['false']);
            }
            if ($product['type'] == 'prize')
            {
                header('Location: '.rewrite('?mod=prize&code=sign&pid='.$product['id']));
                exit;
            }
            $cartItems = logic('cart_manage')->filter(array($product));
            unset($product);
        }
        include handler('template')->file('@m/buy_checkout');
    }

    function Checkout_save()
    {
        $os = new OrderShow();
        $os->setObj($this);
        #每个产品生成一张订单
        $r = $os->MCheckout_save();
                                $orders = $msg = array();
        foreach($r as $k => $v)
        {
        	if($v['status'] == 'ok')
        		$orders[] = $v['id'];
        	else
        		$msg[] = $v['msg'];
        }
        if(empty($msg))        {
        	$ops = array('status'=>'ok','id'=>implode('_', $orders));
        }
        if(empty($orders))        {
        	$ops = array('status'=>'fail','msg'=>implode(',', $msg));
        }
        if(!empty($msg) && !empty($orders))        {
        	$ops = array('status'=>'ok_fail','id'=>implode('_', $orders),'msg'=>implode(',', $msg));
        }
        if(count($orders) > 1)
        {
                        $opsv = $this->_Checkout_save_V($orders);
            $ops['id'] = $opsv['idv'];
        }
         
        echo jsonEncode($ops);
    }

    
    public function create_paybill()
    {
        $uid = MEMBER_ID;
        $sid = get('sid', 'int');        $bill_money = get('bill_money', 'float');                if($uid < 1 || $sid < 1 || $bill_money < 0) {
            $this->Messager('订单创建失败');
        }
        $seller = logic('seller')->GetOne($sid);
        if(false == $seller) {
            $this->Messager('请输入正确的商家ID');
        }
        if($seller['pay_into'] < 0) {
            $this->Messager('该商家暂时不支持到店买单服务');
        }
                $totalprice = round($bill_money, 2);
        $order = logic('order')->GetFree($uid,99999);

        logic('order')->Update($order['orderid'], array('totalprice'=>$totalprice, 'paymoney'=>$totalprice, 'sid'=>$sid, 'status'=>ORD_STA_Normal));
        $order['totalprice'] = $totalprice;
        $order['paymoney'] = $totalprice;
        echo $order['orderid'];
    }

    
    function _Checkout_save_V($ordersId)
    {
        if(empty($ordersId))
            return false;

        $attrs_price_all = 0;        $expressprice    = 0;        foreach ($ordersId as $v)
        {
            $orders[] = $orders_tmp = logic('order')->GetOne($v);
            if(isset($orders_tmp['attrs']['price_all']))
                $attrs_price_all = $orders_tmp['attrs']['price_all'];
            $expressprice = $orders_tmp['expressprice'];
        }

        $mergeOrder = new MergeOrder($orders);
         $mergeOrder->setCommands(new Orderprice_of_product());
        $mergeOrder->setCommands(new OrderExpresstype());
        $mergeOrder->setCommands(new OrderAddressid());
        $mergeOrder->setCommands(new OrderMixedProductidAddressidExpresstype());
        $mergeOrder->setCommands(new OrderAttrsPriceall());
        $mergeOrder->setCommands(new OrderExpressprice());
         $mergeOrder->setCommands(new OrderProductid());
         $mergeOrder->merge('price.of.product');
        $mergeOrder->merge('expresstype');
        $mergeOrder->merge('addressid');
        $mergeOrder->merge('mixedProductidAddressidExpresstype');
         $mergeOrder->merge('productid');
        $mergeOrder->merge('attrs_price_all');
        $mergeOrder->merge('expressprice');
        $order = $mergeOrder->getOrder();
        $order['extmsg_reply'] = isset($order['mixedProductidAddressidExpresstype']) ? serialize($order['mixedProductidAddressidExpresstype']) : '';
        #生成空白订单
        $order_tmp = logic('order')->GetFree(user()->get('id'), $order['productid'], ORD_STA_Virtual);
        $order['orderid'] = $order_tmp['orderid'];
        $order['buytime'] = $order_tmp['buytime'];
        $order['extmsg'] = serialize($ordersId);
        $order['expressprice'] = $order['expressprice'];
        $order['totalprice']   = $order['price_of_product'] + $order['attrs_price_all'] + $order['expressprice'];

        
                $order['process'] = '__CREATE__';
        $order['status'] = ORD_STA_Virtual;
        
        logic('order')->Update($order['orderid'], $order);

                logic('express')->virtual_order_expressprice_fix($order['orderid']);

                $ops = array(
            'status' => 'ok',
            'idv' => $order['orderid']
        );

        return $ops;
    }

    
    function _Checkout_save()
    {
        $product_id = post('product_id', 'int');
        $product = logic('product')->BuysCheck($product_id);
        if (isset($product['false']))
        {
            return $this->__ajax_save_failed($product['false'],false);
        }
        $num_buys = post('num_buys', 'int');
        if ($num_buys < 1 || ($product['oncemax'] > 0 && $num_buys > $product['oncemax']) || $num_buys < $product['oncemin'])
        {
            return $this->__ajax_save_failed(__('请填写正确的购买数量！'),false);
        }
        #生成空白订单
        $order = logic('order')->GetFree(user()->get('id'), $product_id);
        $order['productnum'] = $num_buys;
        $order['productprice'] = $product['nowprice'];
        $order['extmsg'] = post('extmsg', 'txt');
        if ($product['type'] == 'stuff')
        {
			logic('address')->Accessed('order.save', $order);			logic('express')->Accessed('order.save', $order);        }
                logic('notify')->Accessed('order.save', $order);
				if (!logic('attrs')->Accessed('order.save', $order))
        {
            return $this->__ajax_save_failed(__('请选择正确的产品属性规格！'),false);
        }
                $r = logic('cut')->Accessed('order.save', $order);
                $price_total = $order['productprice'] * $order['productnum'] + $order['expressprice'];
                logic('attrs')->order_calc($order['orderid'], $price_total);
                if ((float)$price_total < 0)
        {
            return $this->__ajax_save_failed(__('订单总价不正确，请重新下单！'),false);
        }
                $order['totalprice'] = $price_total;
                $order['process'] = '__CREATE__';
        $order['status'] = ORD_STA_Normal;
		if( $product['is_countdown'] == 1 ){
			$order['is_countdown'] = 1;
			dbc(DBCMax)->update('product')->data('sells_count=sells_count+'.(int)$num_buys)->where('id='.$product_id)->done();		}
        logic('order')->Update($order['orderid'], $order);
                $ops = array(
            'status' => 'ok',
            'id' => $order['orderid']
        );
        if (!X_IS_AJAX)
        {
        	header('Location: '.rewrite('?mod=buy&code=order&id='.$order['orderid']));
        	exit;
        }
		return $ops;
    }



    
    private function __ajax_save_failed($msg,$echo='true')
    {
        $ops = array(
            'status' => 'failed',
            'msg' => $msg
        );
        if (!X_IS_AJAX)
        {
        	$this->Messager($msg, -1);
        }
        if($echo)
        {
        	echo jsonEncode($ops);
        	return false;
        }
        else
        {
        	return $ops;
        }
    }

    function Order()
    {
    	$id = get('id', 'text');
    	$order = logic('order')->GetOne($id);
    	if($order == false)
    	    $this->Messager('订单号错误');
    	if($order['productid'] < 0  && @unserialize($order['extmsg']) === false)
    	    $this->Messager('订单号错误');
    	#虚拟订单
    	if(@unserialize($order['extmsg']) !== FALSE )
    	{
    	    $subOrders = unserialize($order['extmsg']);
    	}
    	else
    	{
    	    $subOrders = array($order['orderid']);
    	}
    	$attrs_price_all = 0;
    	$expressprice    = 0;
    	foreach ($subOrders as $v)
    	{
    	    $orders[] = $orders_tmp = logic('order')->GetOne($v);
    	    if(isset($orders_tmp['attrs']['price_all']))
    	       $attrs_price_all = $orders_tmp['attrs']['price_all'];
    	    $expressprice = $orders_tmp['expressprice'];
    	}

    	#将订单数组，合并为一个订单
     	$mergeOrder = new MergeOrder($orders);
     	$mergeOrder->setCommands(new OrderProductTypeAlis());
    	$mergeOrder->setCommands(new OrderIscountdown());
    	$mergeOrder->setCommands(new OrderBuytime());
     	$mergeOrder->merge('product_type');
    	$mergeOrder->merge('is_countdown');
    	$mergeOrder->merge('buytime');
     	$order_t = $mergeOrder->getOrder();

     	$order = array_merge_multi($order,$order_t);
     	if( $order['is_countdown'] == 1 ){
     	    $timeLimit_free = 15;     	    $order['timelimit'] = ($order['buytime'] + 60 * $timeLimit_free) - time();
     	         	}

    	#生成空白订单

    	logic('address')->Accessed('order.show', $order);
    	logic('express')->Accessed('order.show', $order);
    	logic('notify')->Accessed('order.show', $order);
    	logic('attrs')->Accessed('order.show', $order);

    	$order['price_of_total'] = $order['totalprice'];
                logic('promo_code')->Accessed('order.show', $order);
                logic('cut')->Accessed('order.show', $order);

    	include handler('template')->file('@m/buy_order');
    }


    


    
    function Order_save()
    {
        $order_id = post('order_id', 'number');
        $ibank = post('ibank','txt');
        $payment_id = post('payment_id', 'int');
        $order = logic('order')->GetOne($order_id);
        if (user()->get('id') != $order['userid'])
        {
            return $this->__ajax_save_failed(__('您没有权限操作此订单！'));
        }

        if (!in_array($order['status'], array(ORD_STA_Normal,ORD_STA_Virtual)) || $order['pay'] == ORD_PAID_Yes )
        {
            return $this->__ajax_save_failed(__('此订单已经不能支付！'));
        }
                

		
        

        				        $price_total = $order['totalprice'];
        $pay_money = $price_total;

                $pay = logic('pay')->GetOne($payment_id);
        if ($pay_money == 0 && $pay['code'] != 'self') {
            return $this->__ajax_save_failed(__('请选择余额支付'));
        }
		
				$product = logic('product')->SrcOne($order['productid']);
		if ($product && $product['yungou'] > 0) {
			if (!in_array($pay['code'], handler('config')->get('yungou','paycods'))) {
				return $this->__ajax_save_failed(__('云购产品不支持“' . $pay['name'] . '”，请选择其它支付方式'));
			}
		}

        $me_money = user()->get('money');
        if ($payment_id == 1)
        {
            $me_money = 0;
        }
        
        $use_surplus = post('payment_use_surplus', 'txt');
        
        if ($use_surplus == 'true' && $me_money > 0 && $price_total > $me_money && false == logic('order')->is_virtual_order($order['orderid']))
        {
            $pay_money = $price_total - $me_money;
        }

                logic('cut')->order_calc($order['orderid'], $pay_money);

        $array = array(
            'totalprice' => $price_total,
            'paytype' => $payment_id,
            'paymoney' => $pay_money
        );
		
                logic('order')->Update($order_id, $array);

        $ops = array(
            'status' => 'ok',
            'tourl'  => rewrite("?mod=buy&code=pay&id=".$order["orderid"]."&ibank=".$ibank),
        );
        if (logic('pay')->plugin_has_ext_html($payment_id) === true) {
            header('Location: '.rewrite('?mod=buy&code=pay&id='.$order_id.'&ibank='.$ibank));
            exit;
        }
    	if (!X_IS_AJAX)
        {
        	header('Location: '.rewrite('?mod=buy&code=pay&id='.$order_id.'&ibank='.$ibank));
        	exit;
        }
         echo jsonEncode($ops);
    }
    
    function Pay()
    {
        
        logic('cart_manage')->RemoveAllItem();


        $this->Title = __('订单支付');
        $id = get('id', 'number');
        $order = logic('order')->GetOne($id);
        if (user()->get('id') != $order['userid'])
        {
            $this->Messager('对不起，您没有权限支付此订单！', '?mod=me&code=order');
        }
		if($order['process'] == "_TimeLimit_"){
			 $this->Messager('对不起，该订单已经失效！', '?mod=me&code=order');
		}
        if (!in_array($order['status'],array(ORD_STA_Normal,ORD_STA_Virtual)))
        {
        	$this->Messager(__('关于此订单：').logic('order')->STA_Name($order['status']), '?mod=me&code=order');
        }
        if ($order['paytype'] == 0)
        {
                        header('Location: '.rewrite('?mod=buy&code=order&id='.$id));
        }
        if ($order['pay'] == 1 && $order['paytime'] > 0)
        {
            $this->Messager(__('此订单已经支付过了！'), '?mod=me&code=order');
        }
		if ($order['product']['type'] == 'stuff' && $order['addressid'] == 0)
        {
			logic('order')->Delete($id);
			$this->Messager(__('该订单无效，请重新下单！'), '?mod=buy&code=checkout&id='.$order['productid']);
        }
        if($order['productid'] != 99999){
            $product = logic('product')->BuysCheck($order['productid'], false);
        }
        if (isset($product['false']))
        {
            return $this->Messager($product['false']);
        }
                if($order['sid']){
            $seller = logic('seller')->GetOne($order['sid']);
        }
                $payment_id = get('p');
        if ( is_numeric($payment_id)) {
            logic('order')->Update($id, array('paytype' => $payment_id));
        }
        $pay = logic('pay')->GetOne($order['paytype']);
		$pay['code']=='yeepay' && $pay['site'] = 'pc_web';

                $rewrite_me = false;
        include(CONFIG_PATH.'rewrite.php');
        if($_rewrite['mode'] != '')  {
            $me_uname   = isset($_rewrite['value_replace_list']['mod']['me']) === false ? 'me' : $_rewrite['value_replace_list']['mod']['me'];
            $rewrite_me = strpos($_SERVER['HTTP_REFERER'],$me_uname.'/order');
        }
        if ($pay['code'] == 'bankdirect' && ( $rewrite_me || strpos($_SERVER['HTTP_REFERER'], 'mod=me&code=order'))) {
            header('Location: '.rewrite('?mod=buy&code=order&id='.$id));
        }

        $parameter = array(
			'userid' => $order['userid'],
            'name' => $order['product']['flag'],
            'detail' => $order['product']['intro'],
            'price' => $order['paymoney'],
            'sign' => $order['orderid'],
			'time' => $order['buytime'],
            'notify_url' => ini('settings.site_url').'/m.php?mod=callback&pid='.$pay['id'],
            'product_url' => ini('settings.site_url').'/m.php?view='.$order['productid'],
            'productid' => $order['productid']
        );
        if ($order['product']['type'] == 'stuff')
        {
            $address = logic('address')->GetOne($order['addressid']);
            $parameter['addr_name'] = $address['name'];
            $parameter['addr_address'] = $address['region'].$address['address'];
            $parameter['addr_zip'] = $address['zip'];
            $parameter['addr_phone'] = $address['phone'];
        }
        if (logic('pay')->plugin_has_ext_html($pay['code']) === true && get('ibank','txt') != '') {
            $log_data = array(
                'type' => $pay['id'],
                'sign' => $parameter['sign'],
                'money' => $parameter['price']
            );
            logic('pay')->__LogCreate($log_data) && logic('order')->Processed($parameter['sign'], 'WAIT_BUYER_PAY');
            $link = logic('pay')->apiz($pay['code'])->CreatForm($pay, $parameter);
            echo $link;
            exit;
        }
		if(method_exists(logic('pay')->apiz($pay['code']), 'inner_disabled') && logic('pay')->apiz($pay['code'])->inner_disabled()){
			$payment_linker = '<input type="button" value="请用手机付款">';
		}else{
			$payment_linker = logic('pay')->Linker($pay, $parameter);
		}        
		#虚拟订单状态修改
		        include handler('template')->file('@m/buy_pay');
    }
    
    function TradeConfirm()
    {
        $id = get('id', 'number');
        if (!$id)
        {
            $this->Messager(__('订单号无效！'));
        }
        $order = logic('order')->GetOne($id);
        if (user()->get('id') != $order['userid'])
        {
            $this->Messager('对不起，您没有权限操作此订单！', '?mod=me&code=order');
        }
        logic('order')->Processed($id, 'TRADE_FINISHED');
                logic('rebate')->Add_Rebate_For_Item($order);
                
        $this->Messager(__('本次交易已经完成！'), '?mod=me&code=order');
    }
    
    public function order_process()
    {
        $sign = get('sign', 'number');
        include handler('template')->file('@m/buy_order_process');
    }
    
    public function order_url()
    {
        $sign = get('sign', 'number');
        if ($sign)
        {
            $order = logic('order')->GetOne($sign);
            if (!$order)
            {
                exit(rewrite('?mod=me&code=order'));
            }
        }
        else
        {
            exit(rewrite('?mod=me&code=order'));
        }
        if ($order['process'] == 'TRADE_FINISHED')
        {
            $url = rewrite('?mod=me&code=order');
        }
        elseif ($order['process'] == 'WAIT_BUYER_CONFIRM_GOODS')
        {
            if ($order['product']['type'] == 'ticket')
            {
                $url = logic('pay')->ConfirmLinker($order);
            }
            else
            {
                $url = rewrite('?mod=me&code=order');
            }
        }
        else
        {
            $url ='wait';
        }
        exit($url);
    }

    public function promo_cut_save() {
        $orderid = post('orderid', 'number');
        $promo_code = post('promo_code');

        $params = array(
                'userid'=>MEMBER_ID,
                'orderid'=>$orderid,
                'code'=>$promo_code,
            );
        $rets = logic('promo_code')->save($params);

        response_text($rets);
    }
}


class OrderShow
{
    private $_obj = '';
    private $_post = null;

    function __construct()
    {
        if($this->_post == null)
        {
            $this->_post = $_POST;
        }
    }

    public function setObj($obj)
    {
        $this->_obj = $obj;
    }

        public function MCheckout_save()
    {
        $r = array();
        foreach($this->_post['product_id'] as $key => $value)
        {
            unset($_POST);
            #重构post数据
            $_POST['FORMHASH']   = $this->_post['FORMHASH'];
            $_POST['product_id'] = $key;
            $_POST['num_buys']   = intval($this->_post['num_buys'][$key]);
            if(isset($this->_post['phone']))
                $_POST['phone']      = $this->_post['phone'];
            if(isset($this->_post['express_id']))                $_POST['express_id'] = current($this->_post['express_id']);
            if(isset($this->_post['address_id'][0]))                $_POST['address_id'] = $this->_post['address_id'][0];
            if(isset($this->_post['cat_f'][$key]))
                foreach($this->_post['cat_f'][$key] as $k => $v) {
                    $_POST['cat_f_'.$k] = $v;
                }
            $_POST['cut_f_']     = $this->_post['cut_f_'];
            $_POST['extmsg']     = $this->_post['extmsg'];
            $_POST['mod']        = $this->_post['mod'];
            $r[] = $this->_obj->_Checkout_save();
        }
        
        return $r;
    }
}
class OrderSave
{
    private $_obj = '';
    private $_post = null;

    function __construct()
    {
        if($this->_post == null)
        {
            $this->_post = $_POST;
        }
    }

    public function setObj($obj)
    {
        $this->_obj = $obj;
    }

    public function MOrder_save()
    {
        $r = array();
        $orders = explode(',',$this->_post['order_id'] );
        foreach($orders as $key => $orderid)
        {
            unset($_POST);
            #重构post数据
            $_POST['FORMHASH']   = $this->_post['FORMHASH'];
            $_POST['order_id']   = $orderid;
            $_POST['payment_id']   = $this->_post['payment_id'];
            $r[] = $this->_obj->_Order_save();
        }
        return $r;
    }
}
?>