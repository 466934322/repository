<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name index.mod.php
 * @date 2016-07-25 18:45:53
 */



class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	function Main()
	{
		$clientUser = get('u', 'int');
        if(get('uid', 'int')){
            $clientUser = get('uid', 'int');
        }

		if ( $clientUser != '' )
		{
			handler('cookie')->setVar('finderid', $clientUser);
			handler('cookie')->setVar('findtime', time());
		}

		$data = logic('product')->display();
                if(user()->get('id')){
            $uid = user()->get('id');
        }
				if (!$data && get('page', 'int') == 0)
		{
			header('Location: '.rewrite('?mod=subscribe&code=mail'));
			exit;
		}
		$product = $data['product'];
                                if(false == $data['mutiView']){
            $product['sells_out'] = $product['sells_real']+$product['virtualnum'];
			$favorited = logic('favorite')->get_one($product['id']);
        }

		$this->Title = $data['mutiView'] ? '' : $product['name'];
		$data['mutiView'] || mocod('product.view');
		$data['mutiView'] || productCurrentView($product);
						if(INDEX_DEFAULT === true) {
			$new_product = logic('product')->GetNewList(20, true);
			if(false === $new_product) {
				$new_product = logic('product')->GetNewList(20);
			}
		}
		
		if(get('city')) {
			header('Location: ' . ini('settings.site_url'));
		}
        if($data['file'] === 'detail'){
            if($_GET['uid']){
                $home_id = $_GET['uid'];
            }
        }
		include handler('template')->file($data['file']?$data['file']:'home');
	}
	function ExpressConfirm()
	{
		$oid = $this->Get['id'];
		$result = $this->OrderLogic->orderExpressConfirm($oid);
		if ( $result )
		{
			$this->Messager(__('已经确认收货，本次交易完成！'), '?mod=me&code=order');
		}
		else
		{
			$this->Messager(__('无效的订单号！'), '?mod=me&code=order');
		}
	}
	function Recent_view_Clean() {
		logic('recent_view')->clean();
				exit;
	}

        public function Rebate_day(){
        echo 'start : '.date('Y-m-d H:i:s').' <br>';
                $products = dbc(DBCMax)->select('product')->where('last_rebate_day>0')->done();
        if($products) {
            foreach($products as $val){
                                $where = 1;
                $where .= ' AND process not in ("__CREATE__","WAIT_BUYER_PAY","WAIT_SELLER_SEND_GOODS","WAIT_BUYER_CONFIRM_GOODS")';
                $where .= ' AND productid = '.$val['id'];
                $where .= ' AND buytime >= 1450511905';
                                $orders = dbc(DBCMax)->select('order')->where($where)->done();
                foreach($orders as $v){
                    $order = logic('order')->GetOne($v['orderid']);
                    $product = logic('product')->SrcOne($order['productid']);
                                        if($product['rebate_day'] > 0  && $product['rebate_money'] != 0){
                        $rebate_day_money = round($product['rebate_money']/$product['rebate_day'],2);
                        if($rebate_day_money > 0){
                            $time = ($this->time > 0 ? $this->time : time());
                            $user = dbc(DBCMax)->select('members')->where('uid='.$v['userid'])->limit(1)->done();
                            $rebate_data = array(
                                'uid'   => $v['userid'],
                                'uname' => $user['username'],
                                                                'deal_money' => $order['paymoney'],
                                'salary_pre' => $order['paymoney']!=0?($rebate_day_money/$order['paymoney'])*100:0,
                                'salary_money' => $rebate_day_money,
                                'orderid' => $order['orderid'],
                                                                'type' => 'rebate_day',
                                'addtime' => $time,
                            );
                                                        if($order['last_rebate_day'] >= 1){
                                $last_rebate_day = (int) ($order['last_rebate_day'] - 1);
                                                                dbc(DBCMax)->insert('rebate_log')->data($rebate_data)->done();
                                                                logic('me')->money()->add($rebate_day_money, $v['userid'], array(
                                    'name' => '按天返利',
                                    'intro' => "买家【".$user['username']."】于：".date('Y-m-d H:i:s', $time)."<br>消费金额：&yen;".$v['paymoney']."元，您今日获得返利：&yen;".$rebate_day_money."元，剩余".$last_rebate_day."天，每天还可获利".$rebate_day_money."元" . ($order['orderid'] ? "<br>订单号：".$order['orderid'] : ""),
                                ));

                                                                                                                                                                dbc(DBCMax)->update('order')->data('last_rebate_day='.$last_rebate_day)->where('orderid='.$order['orderid'])->done();

                                echo 'order '.$order['orderid'].' ok<br>';
                            }

                            echo 'product '.$product['id'].' ok<br>';
                        }
                    }
                }
            }
        }
        exit('all done!<br>');
    }

}
?>