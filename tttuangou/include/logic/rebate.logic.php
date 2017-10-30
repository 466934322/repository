<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name rebate.logic.php
 * @date 2016-08-16 17:36:22
 */



class RebateLogic
{
	private $time = 0;
	private $salary_money = 0;

	
	public function Get_HomeInfo_For_Seller($sellerid)
	{
		$res = dbc(DBCMax)->select('seller')->where('`id`='. (int)$sellerid)->limit(1)->done();
		if( $res ){
			$arr = $this->Get_HomeInfo_For_member($res['userid']);
			$arr['profit_pre'] = $res['profit_pre'];
			return $arr;
		}else{
			return 0;
		}
	}
	public function Get_HomeInfo_For_member($userid)
	{
		$res = dbc(DBCMax)->select('members')->where('`uid`='. (int)$userid)->limit(1)->done();
		if( !$res ) return 0;
		$home_uid = $res['home_uid'];
		$uname    = $res['username'];
		if( $home_uid==0 ){
			return array(
				'userid' => $userid,
				'uname'=>$uname,
			);
		}
		$res = dbc(DBCMax)->select('members')->where('`uid`='. (int)$home_uid)->limit(1)->done();
		if( $res ){
			return array(
				'userid' => $userid,
				'uname'=>$uname,
				'home_uid'=>$res['uid'],
				'buy_pre'=>$res['buy_pre'],
				'sell_pre'=>$res['sell_pre'],
			);
		}else{
			return array(
				'userid' => $userid,
				'uname'=>$uname,
			);
		}
	}
	public function Add_RebateValue_For_Seller($sellerid,$money,$orderid,$ticketid=0,$fundmoney=0){
		$ret = false;
		$sellerid = (int) $sellerid;
		$money = (float) $money;
		$orderid = (is_numeric($orderid) ? $orderid : 0);
		$ticketid = (is_numeric($ticketid) ? $ticketid : 0);
		$fundmoney = (float) $fundmoney;

		$home = $this->Get_HomeInfo_For_Seller($sellerid);
        $order = logic('order')->SrcOne($orderid);
		if( !$home ) {
			return $ret;
		}

		$time = ($this->time > 0 ? $this->time : time());

		$dr_set = ini('rebate_setting');

				$userid     = $home['userid'];
						if(1) {
			$profit_pre = ($home['profit_pre'] > 0 ? $home['profit_pre'] : (($dr_set['profit'] && $dr_set['profit'][0]['pre'] > 0) ? $dr_set['profit'][0]['pre'] : 0));
			$data = array(
				'uid'      => $userid,
				'uname' => $home['uname'],
				'home_uid' => 0,
				'deal_money' => $money,
				'fund_money' => $fundmoney,
				'salary_pre' => $profit_pre,
				'salary_money' => $this->gave_me_money($money, $profit_pre),
				'orderid' => $orderid,
				'ticketid' => $ticketid,
				'type' => 'master',
				'addtime' => $time,
				'rec_all' => 1,
			);
			if(false == $this->_check_exists($data)) {
				$ret = true;
				dbc(DBCMax)->insert('rebate_log')->data($data)->done();
								if($fundmoney >= 0){
					$salary_money = $fundmoney;
				}elseif($profit_pre > 0){
					$salary_money = $money - $this->gave_me_money($money, $profit_pre);
				}else{
					$salary_money = $money;
				}
				$this->salary_money = $salary_money;
				dbc(DBCMax)->update('seller')->data('account_money=account_money+'. $salary_money .',total_money=total_money+'. $salary_money )->where('id='.(int)$sellerid)->done();
			}
		}
				if( !$home['home_uid'] ) {
			return $ret;
		}
		$salary_pre = ($home['sell_pre'] > 0 ? $home['sell_pre'] : (($dr_set['sell_pre'] && $dr_set['sell_pre'] > 0) ? $dr_set['sell_pre'] : 0));
		if($salary_pre > 0){
	        	        if($dr_set['rebate_type'] == 2){
	            	            	            
	            $money -= $this->salary_money;
	        }

			$salary_money = $this->gave_me_money($money, $salary_pre);
			$home_uid   = $home['home_uid'];
			$data = array(
				'uid'      => $userid,
				'uname' => $home['uname'],
				'home_uid' => $home_uid,
				'deal_money' => $money,
				'salary_pre' => $salary_pre,
				'salary_money' => $salary_money,
				'orderid' => $orderid,
				'ticketid' => $ticketid,
				'type' => 'sell',
				'addtime' => $time,
			);
			if(false == $this->_check_exists($data)) {
				$ret = true;
				dbc(DBCMax)->insert('rebate_log')->data($data)->done();
				logic('me')->money()->add($salary_money, $home_uid, array(
					'name' => '邀请返利',
					'intro' => "卖家【".$home['uname']."】于：".date('Y-m-d H:i:s', $time)."<br>卖出商品：".$money."元，您获得返利：".$salary_money."元<br>订单号：".$orderid
				));
			}
		}
		return $ret;
	}

    
    public function Add_RebateValue_For_buySeller($sellerid,$money,$orderid,$ticketid=0,$fundmoney=0){
        $ret = false;
        $sellerid = (int) $sellerid;
        $money = (float) $money;
        $orderid = (is_numeric($orderid) ? $orderid : 0);
        $ticketid = (is_numeric($ticketid) ? $ticketid : 0);
        $fundmoney = (float) $fundmoney;

        $home = $this->Get_HomeInfo_For_Seller($sellerid);

        if( !$home ) {
            return $ret;
        }

        $time = ($this->time > 0 ? $this->time : time());
        $seller = logic('seller')->GetOne($sellerid);
                if(1) {
            $data = array(
                'uid'      => $home['userid'],
                'uname' => $home['uname'],
                'home_uid' => 0,
                'deal_money' => $money,
                'fund_money' => $fundmoney,
                'salary_pre' => max(0, $seller['pay_into']),
                'salary_money' => $fundmoney,
                'orderid' => $orderid,
                'ticketid' => $ticketid,
                'type' => 'pay_bill',
                'addtime' => $time,
                'rec_all' => 1,
            );
            $this->salary_money = $fundmoney;
            if(false == $this->_check_exists($data)) {
                $ret = true;
                dbc(DBCMax)->insert('rebate_log')->data($data)->done();
                dbc(DBCMax)->update('seller')->data('account_money=account_money+'. $fundmoney .',total_money=total_money+'. $fundmoney )->where('id='.(int)$sellerid)->done();
            }
        }

        		if( !$home['home_uid'] ) {
			return $ret;
		}
		$dr_set = ini('rebate_setting');
		$salary_pre = ($home['sell_pre'] > 0 ? $home['sell_pre'] : (($dr_set['sell_pre'] && $dr_set['sell_pre'] > 0) ? $dr_set['sell_pre'] : 0));
		if($salary_pre > 0){
	        	        if($dr_set['rebate_type'] == 2){
	            	            	            
	            $money -= $this->salary_money;
	        }

			$salary_money = $this->gave_me_money($money, $salary_pre);
			$home_uid   = $home['home_uid'];
			$data = array(
				'uid'      => $userid,
				'uname' => $home['uname'],
				'home_uid' => $home_uid,
				'deal_money' => $money,
				'salary_pre' => $salary_pre,
				'salary_money' => $salary_money,
				'orderid' => $orderid,
				'ticketid' => $ticketid,
				'type' => 'sell',
				'addtime' => $time,
			);
			if(false == $this->_check_exists($data)) {
				$ret = true;
				dbc(DBCMax)->insert('rebate_log')->data($data)->done();
				logic('me')->money()->add($salary_money, $home_uid, array(
					'name' => '邀请返利',
					'intro' => "卖家【".$home['uname']."】于：".date('Y-m-d H:i:s', $time)."<br>卖出商品：".$money."元，您获得返利：".$salary_money."元<br>订单号：".$orderid
				));
			}
		}

        return $ret;
    }

	public function Add_RebateValue_For_Buyer($userid,$money,$orderid='',$ticketid=0){
        $ret = false;
        $userid = (int) $userid;
        $money = (float) $money;
        $orderid = (is_numeric($orderid) ? $orderid : 0);
        $ticketid = (is_numeric($ticketid) ? $ticketid : 0);

        $home = $this->Get_HomeInfo_For_member($userid);
        if( !$home || !$home['home_uid'] ) {
            return $ret;
        }

        $dr_set = ini('rebate_setting');
        $salary_pre = ($home['buy_pre'] > 0 ? $home['buy_pre'] : (($dr_set['buy_pre'] && $dr_set['buy_pre'] > 0) ? $dr_set['buy_pre'] : 0));
        if($salary_pre > 0){        	
	        	        if($dr_set['rebate_type'] == 2){
	            	            	            
	           	$money -= $this->salary_money;
	        }

            $time = ($this->time > 0 ? $this->time : time());
            $home_uid   = $home['home_uid'];
            $salary_money = $this->gave_me_money($money, $salary_pre);
            $data = array(
                'uid'   => $userid,
                'uname' => $home['uname'],
                'home_uid' => $home_uid,
                'deal_money' => $money,
                'salary_pre' => $salary_pre,
                'salary_money' => $salary_money,
                'orderid' => $orderid,
                'ticketid' => $ticketid,
                'type' => 'buy',
                'addtime' => $time,
            );
            if(false == $this->_check_exists($data)) {
                $ret = true;
                dbc(DBCMax)->insert('rebate_log')->data($data)->done();
                dbc(DBCMax)->update('members')->data('salary_number=salary_number+'.$money)->where('uid='.(int)$home_uid)->done();
                logic('me')->money()->add($salary_money, $home_uid, array(
                    'name' => '邀请返利',
                    'intro' => "买家【".$home['uname']."】于：".date('Y-m-d H:i:s', $time)."<br>消费金额：&yen;".$money."元，您获得返利：&yen;".$salary_money."元" . ($orderid ? "<br>订单号：".$orderid : ""),
                ));
                notify($home_uid, 'user.buy.rebate', array(
                    'username' => $home['uname'],
                    'paytime' => date('Y-m-d H:i:s', $time),
                    'paymoney' => $money,
                    'rebate_money' => $salary_money,
                ));
            }
        }
        return $ret;
	}
    public function Add_RebateValue_For_Sharer($productid){
        $ret = false;
        $product = logic('product')->SrcOne($productid);
                if($product['share_rebate_money']){
            $ret = true;
        }
        return $ret;
    }

	public function Add_Rebate_For_Ticket($product,$uid, $fix_time = false){
        $seller = dbc(DBCMax)->select('seller')->where('`userid`='. (int)$uid)->limit(1)->done();
        if($uid){
            $sellerid = $seller['id'];
        }else{
            $sellerid = $product['sellerid'];
        }
		$orderid = $product['coupon']['orderid'];
		$ticketid = $product['coupon']['ticketid'];
		if($fix_time) {
			$this->time = ($product['coupon']['usetime'] > 0 ? strtotime($product['coupon']['usetime']) : 0);
						if($this->time < 1 || $this->time < $this->_first_time()) return false;
		}
		$mutis = $product['coupon']['mutis'];
		$data_order = dbc(DBCMax)->select('order')->in('productnum,paymoney,totalprice,expressprice,userid')->where('`orderid`='. $orderid)->limit(1)->done();
		$totalprice = $data_order['totalprice'];
		$paymoney = $data_order['paymoney'];
		$productnum = (int)$data_order['productnum']?(int)$data_order['productnum']:1;
		$userid = $data_order['userid'];
		if( $mutis == $productnum ){
			            $money = $paymoney;
			$fundmoney = $product['fundprice'] * $productnum;
			$score = $productnum * $product['score'];
		}else{
			                        $money = $paymoney / $productnum;
			$fundmoney = $product['fundprice'];
			$score = $product['score'];
		}
				if(false != $product['yungou']) {
			$money = $product['price'];
		}
		$ret2 = $this->Add_RebateValue_For_Seller($sellerid,$money,$orderid,$ticketid,$fundmoney);
		$ret1 = $this->Add_RebateValue_For_Buyer($userid,$money,$orderid,$ticketid);
		$ret3 = $this->Add_RebateValue_For_Sharer($orderid);
				if($ret1 || $ret2 || $ret3) {
			logic('credit')->add_score($product['id'],$userid,$score);

						$this->__rebate($orderid, $mutis);
		}
				dbc(DBCMax)->update('order')->data(array('comment' => '1'))->where(array('orderid' => $orderid,'comment' => '0'))->done();
	}

    public function Add_Rebate_For_Paybill($sid,$order){
                $seller = logic('seller')->GetOne($sid);

        $fundmoney = $order['totalprice'] - ($order['totalprice']*max(0, $seller['pay_into'])*0.01);
                $this->Add_RebateValue_For_buySeller($sid,$order['paymoney'],$order['orderid'],0,$fundmoney);
                $this->Add_RebateValue_For_Buyer($order['userid'], $order['paymoney'], $order['orderid']);
                if($seller['send_points'] > 0) {
        	$score = (int)$order['paymoney']*$seller['send_points'];
        	logic('credit')->add_score($order['productid'],$order['userid'],$score,'pay_bill','',$order['sid']);
        }
    }
	public function Add_Rebate_For_Item($data_order, $fix_time = false){
		$orderid = $data_order['orderid'];
		$productid = (int) $data_order['productid'];
		$totalprice = $data_order['totalprice'];
		$productnum = (int) $data_order['productnum'];
		$userid = $data_order['userid'];
		if($fix_time) {
			$this->time = (ORD_STA_Normal == $data_order['status'] ? max($data_order['paytime'], $data_order['process_time']) : 0);
						if($this->time < 1 || $this->time < $this->_first_time()) return false;
		}
		$data_product = dbc(DBCMax)->select('product')->in('sellerid,fundprice,score,price,yungou')->where('`id`='. $productid)->limit(1)->done();
		$sellerid = $data_product['sellerid'];
						if(false != $data_product['yungou']) {
			$money = $data_product['price'];
			$productnum = 1;
		}
        $money = $data_order['paymoney'];
		$fundmoney = $data_product['fundprice'] * $productnum;
		$ret2 = $this->Add_RebateValue_For_Seller($sellerid,$money,$orderid,$ticketid,$fundmoney);
		$ret1 = $this->Add_RebateValue_For_Buyer($userid,$money,$orderid,$ticketid);
        $ret3 = $this->Add_RebateValue_For_Sharer($productid);
        
				$score = $productnum * $data_product['score'];
		if($ret1 || $ret2 || $ret3) {
			logic('credit')->add_score($productid,$userid,$score);

						$this->__rebate($orderid);
		}
				dbc(DBCMax)->update('order')->data(array('comment' => '1'))->where(array('orderid' => $orderid,'comment' => '0'))->done();
	}

	private function __rebate($orderid, $mutis = null) {
		$order = logic('order')->GetOne($orderid);
		if(false == $order || $order['productnum'] < 1) {
			return false;
		}

		$tprice = $order['totalprice'];
		logic('cut')->order_calc($order['orderid'], $tprice, $order['productnum']);
		if($tprice <= 0) {
			return false;
		}

		$this->__rebate_reg($order, $tprice);

		if(is_null($mutis) || $mutis == $order['productnum']) {
			$rprice = $tprice;
		} else {
			$rprice = $tprice / $order['productnum'];
			$order['productnum'] = 1;
		}
        $product = logic('product')->SrcOne($order['productid']);
        if($product['rebate_day']){
            $this->__rebate_day($order, $rprice);
        }else{
            $this->__rebate_buy($order, $rprice);
        }
                $productid = $order['productid'];

                if($order['share_uid'] && ($order['share_uid']!=$order['userid'])){
            $data_product = dbc(DBCMax)->select('product')->in('sellerid,fundprice,score,price,yungou,share_rebate_money,role_rebate_user')->where('`id`='. $productid)->limit(1)->done();
                        if($data_product['role_rebate_user']){
                $user = dbc(DBCMax)->select('members')->where('uid='.$order['share_uid'])->limit(1)->done();
                if($user['get_share_rebate']){
                    $rprice = $data_product['share_rebate_money'];
                    $this->__rebate_product($order, $rprice);
                }
            }else{
                $rprice = $data_product['share_rebate_money'];
                $this->__rebate_product($order, $rprice);
            }

        }
	}

		private function __rebate_reg($order, $price = null) {
		$reg_money = ini('rebate_setting.reg_money');
		if($reg_money > 0) {
			$uid = (int) $order['userid'];
			$price = (float) (null === $price ? $order['paymoney'] : $price);
			if($uid > 0 && $price > 0) {
				$home_uid = user($uid)->get('home_uid');
				if($home_uid > 0 && logic('order')->Count(" `userid`='{$uid}' AND `paymoney`>'0' AND `pay`='1' ") < 2) {
					logic('me')->money()->add($reg_money, $home_uid, array(
						'name' => '注册返现',
						'intro' => '订单号：<b>'.substr((string) $order['orderid'], 0, -4).'****</b><br>
							商品名：<b>'.$order['product']['flag'].'</b><br>
							支付：<b>&yen;'.$price.'元</b><br>
							备注：邀请新用户<b>'.user($uid)->get('name').'</b>注册并首次消费后返现<br>
							返现：<b>'.($reg_money.'元').'</b><br>'
					));
					notify($home_uid, 'user.buy.rebate', array(
							'username' => user($uid)->get('name'),
							'paytime' => date('Y-m-d H:i:s', time()),
							'paymoney' => $price,
							'rebate_money' => $reg_money,
						));
				}
			}
		}
	}

		private function __rebate_buy($order, $price = null) {
		$uid = (int) $order['userid'];
		$price = (float) (null === $price ? $order['paymoney'] : $price);
		if($uid < 1 || $price <= 0) {
			return ;
		}
		$money = 0;
		$rebate_money = (float) $order['product']['rebate_money'];
		if($rebate_money < 0) {
						return ;
		} elseif (0 == $rebate_money) {
						$buy_rebate = (float) ini('recharge_buy_rebate');
			if($buy_rebate > 0 && $buy_rebate < 100) {
				$money = round($price * $buy_rebate / 100, 2);
                                if($order['productnum'] > 1){
                    $money = $money * $order['productnum'];
                }
			}
		} elseif ($rebate_money > 0) {
						$rebate_limit = (int) $order['product']['rebate_limit'];
			if($rebate_limit > 0) {
				                $count_limit = logic('order')->Count_pro(" `userid`='{$uid}' AND `productid`='{$order['productid']}' AND `pay`='1' AND `orderid`<>'{$order['orderid']}' ");
                				if($count_limit <= $rebate_limit) {
                    $rest_count = intval($rebate_limit-$count_limit);
                    if($order['productnum'] >= 1 && $order['productnum']<=$rest_count){
                        $money = $rebate_money * $order['productnum'];
                    }elseif($rest_count<$order['productnum']){
                        $money = $rebate_money * $rest_count;
                    }else{
                        $money = $rebate_money;
                    }
				}
			} else {
                if($order['productnum'] >= 1){
                    $money = $rebate_money * $order['productnum'];
                }
			}
		}
		if($money > 0) {
			logic('me')->money()->add($money, $uid, array(
				'name' => '购物返现',
				'intro' => '订单号：<b>'.$order['orderid'].'</b><br>
					商品名：<b>'.$order['product']['flag'].'</b><br>
					支付：<b>&yen;'.$price.'元</b><br>
					返现：<b>'.($buy_rebate ? $buy_rebate.'%' : $money.'元').'</b><br>'
			));
		}
	}

        private function __rebate_day($order, $price = null) {
        $uid = (int) $order['userid'];
        $product = logic('product')->SrcOne($order['productid']);
        $price = (float) (null === $price ? $order['paymoney'] : $price);
        if($uid < 1 || $price <= 0) {
            return ;
        }
        if($product['rebate_day'] != 0 && $product['rebate_money'] != 0){
            $rebate_money = (float) $order['product']['rebate_money'];
            $rebate_day_money = round($rebate_money/$product['rebate_day'],2);
            if($rebate_day_money > 0){
                $time = ($this->time > 0 ? $this->time : time());
                $user = dbc(DBCMax)->select('members')->where('uid='.$uid)->limit(1)->done();
                $rebate_data = array(
                    'uid'   => $uid,
                    'uname' => $user['username'],
                                        'deal_money' => $order['paymoney'],
                    'salary_pre' => ($rebate_day_money/$order['paymoney'])*100,
                    'salary_money' => $rebate_day_money,
                    'orderid' => $order['orderid'],
                                        'type' => 'rebate_day',
                    'addtime' => $time,
                );
                                dbc(DBCMax)->insert('rebate_log')->data($rebate_data)->done();
                                logic('me')->money()->add($rebate_day_money, $uid, array(
                    'name' => '按天返利',
                    'intro' => "买家【".$user['username']."】于：".date('Y-m-d H:i:s', $time)."<br>消费金额：&yen;".$price."元，您今日获得返利：&yen;".$rebate_day_money."元，剩余".(int)($product['rebate_day']-1)."天，每天还可获利".$rebate_day_money."元" . ($order['orderid'] ? "<br>订单号：".$order['orderid'] : ""),
                ));
                                                                dbc(DBCMax)->update('order')->data('last_rebate_day='.(int)($product['rebate_day']-1))->where('orderid='.$order['orderid'])->done();
            }
        }
    }

        private function __rebate_product($order, $price = null) {
        $uid = (int) $order['userid'];
        $user = dbc(DBCMax)->select('members')->where('uid='.$uid)->limit(1)->done();
        $product = logic('product')->SrcOne($order['productid']);
        if($price == null){
                        $rebate_money = $product['share_rebate_money'];
        }else{
            $rebate_money = $price;
        }

                if($uid < 1 || $price <= 0) {
            return ;
        }
        $money = 0;
                if($rebate_money <= 0) {
                        return ;
        } else {
            if($order['productnum'] >= 1){
                $money = $rebate_money * $order['productnum'];
            }
        }
        if($money > 0) {
                        $rs = dbc(DBCMax)->update('members')->data('money=money+'.$money)->where('uid='.(int)$order['share_uid'])->done();
            if($rs){
                $member = dbc(DBCMax)->select('members')->where('uid='.(int)$order['share_uid'])->limit(1)->done();
            }
            $dr_set = ini('rebate_setting');
                                    
            $data = array(
                'uid'   => (int)$user['uid'],
                'uname' => $user['username'],
                'home_uid' => (int)$order['share_uid'],
                'deal_money' => $order['paymoney'],
                'salary_pre' => ($money/$order['paymoney'])*100,
                'salary_money' => $money,
                'orderid' => $order['orderid'],
                                'type' => 'share_product',
                'addtime' => time(),
            );
                        dbc(DBCMax)->insert('rebate_log')->data($data)->done();

            logic('me')->money()->add($money, $order['share_uid'], array(
                'name' => '分享返利产品获得返利',
                'intro' => '订单号：<b>'.$order['orderid'].'</b><br>
					商品名：<b>'.$order['product']['flag'].'</b><br>
					支付：<b>&yen;'.$order['paymoney'].'元</b><br>
					数量：<b>'.$order['productnum'].'</b><br>
					返现金额：<b>'.$money.'元</b><br>
					购买用户：<b>'.$user['username'].'</b><br>
					余额：<b>'.$member['money'].'</b>'
            ));
        }
    }

	private function gave_me_money($money,$pre100){
				
		return round(($money * $pre100 / 100), 2);
	}
	
	public function Get_Rebate_setting($toHtml = false){
		$cfg = ini('rebate_setting');
		return $cfg;
	}
	public function Save_Rebate_Setting($data){
	}

	public function get_list_for_me($member_uid){
		$sql = "SELECT * FROM ". table('rebate_log') ." WHERE home_uid='". (int)$member_uid ."' AND `type` != 'master' ORDER BY id DESC";
		$sql = page_moyo($sql);
		$res = dbc(DBCMax)->query($sql)->done();
		if( $res ){
			return $res;
		}else{
			return array();
		}
	}

	public function get_percent($uid){
		$res = dbc(DBCMax)->select('members')->in('buy_pre,sell_pre')->where('`uid`='. (int)$uid)->limit(1)->done();
		if( $res ){
			return array(
				'buy_pre'=>$res['buy_pre'],
				'sell_pre'=>$res['sell_pre'],
			);
		}else{
			return 0;
		}
	}
	public function get_sum_list($uid){
		$sql = 'SELECT uid, uname, SUM(deal_money) AS total_money, SUM(salary_money) AS salary_money FROM ';
		$sql .= table('rebate_log') .' WHERE `home_uid`='. (int)$uid .' AND `type` != "master" GROUP BY `uid`';
		$sql = page_moyo($sql);
		$res = dbc(DBCMax)->query($sql)->done();
		if( !$res ) return 0;
		$uid_list = array();
		foreach ($res as $v) {
			$uid_list[] = $v['uid'];
		}
		$userInfo = dbc(DBCMax)->select('members')->in('uid, regdate, phone')->where('`uid` IN('. implode(',' , $uid_list) .')')->done();
		$sellInfo = dbc(DBCMax)->select('seller')->in('userid, sellername')->where('`userid` IN('. implode(',' , $uid_list) .')')->done();
		$ulist = array();
		$slist = array();
		foreach ($userInfo as $v) {
			$ulist[ $v['uid'] ] = $v;
		}
		if( $sellInfo ){
			foreach ($sellInfo as $v) {
				$slist[ $v['userid'] ] = $v;
			}
		}
		foreach ($res as &$v) {
			$v['regtime'] = $ulist[ $v['uid'] ]['regdate'];
			$v['regtime'] = date('Y/m/d',$v['regtime']);
			$v['phone'] = $ulist[ $v['uid'] ]['phone'];
			if( $slist && $slist[ $v['uid'] ]) $v['is_seller'] = '商户';
			else $v['is_seller'] = '普通会员';
		}
		unset($userInfo);
		unset($ulist);
		unset($slist);
		return $res;
	}
	public function get_anybody_list($uid,$who){
		$sql = 'SELECT * FROM '. table('rebate_log') .' WHERE `uid`='. (int)$who .' AND `home_uid`='. (int)$uid .' AND `type` != "master"';
		$sql = page_moyo($sql);
		$res = dbc(DBCMax)->query($sql)->done();
		if( $res ){
			return $res;
		}else{
			return array();
		}
	}
	public function get_my_rebate_list($where = ''){
		return $this->get_rebate_list($where, MEMBER_ID);
	}

	public function get_rebate_list($where, $uid = null) {
		$sql = "SELECT r.*,t.number,t.mutis,t.usetime,p.flag FROM ". table('rebate_log') ." r
				LEFT JOIN ". table('ticket') ." t ON r.ticketid = t.ticketid
				LEFT JOIN ". table('order') ." o ON r.orderid = o.orderid
				LEFT JOIN ". table('product') ." p ON o.productid = p.id
			WHERE ".(is_null($uid) ? ' 1 ' : " r.uid='". (int) $uid . "' ")." AND (r.type='master' OR r.type='pay_bill') ". $where ." ORDER BY r.id DESC";
        $sql = page_moyo($sql);
		$res = dbc(DBCMax)->query($sql)->done();
		if( $res ) {
			return $res;
		}else{
			return array();
		}
	}

	private function _check_exists($data) {
		return (false != dbc(DBCMax)->select('rebate_log')->where(array('orderid'=>$data['orderid'], 'ticketid'=>$data['ticketid'], 'uid'=>$data['uid'], 'type'=>$data['type']))->limit(1)->done());
	}

	private function _first_time() {
		static $_first_time=null;
		if(is_null($_first_time)) {
			$row1 = dbc(DBCMax)->select('rebate_log')->order(' `id` ASC ')->limit(1)->done();
			$_first_time = $row1['addtime'];
		}
		return $_first_time;
	}
}
