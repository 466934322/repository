<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name salecount.logic.php
 * @date 2015-09-23 18:07:18
 */





class SalecountLogic
{
	public $data_need_fix = false;

		public function count_product($where = '1')
	{
		$sql = "SELECT o.productid as id,p.flag as productname,s.sellername,m.username,COUNT(*) as ordercount,SUM(o.productnum) as productcount,SUM(o.totalprice) as moneycount FROM ".table('order')." o LEFT JOIN ".table('product')." p ON o.productid = p.id LEFT JOIN ".table('seller')." s ON p.sellerid = s.id LEFT JOIN ".table('members')." m ON s.userid = m.uid WHERE o.totalprice > 0 AND o.pay = 1 AND ".$where." GROUP BY o.productid ORDER BY o.productid ASC";
		logic('isearcher')->Linker($sql);
		$sql = page_moyo($sql);
		$data = dbc(DBCMax)->query($sql)->done();
		if($data){
			foreach($data as $key => $val){				if(empty($val['productname'])){
					unset($data[$key]);
				}
			}
		}
		return $data;
	}

		public function count_payment($where = '1')
	{
		$sql = "SELECT o.paytype as id,p.code as paycode,p.name as payname,SUM(o.paymoney) as paymoneys FROM ".table('order')." o LEFT JOIN ".table('payment')." p ON o.paytype = p.id WHERE o.paymoney > 0 AND o.pay = 1 AND ".$where." GROUP BY o.paytype";
		logic('isearcher')->Linker($sql);
		$sql = page_moyo($sql);
		$data = dbc(DBCMax)->query($sql)->done();
		if($data){
			foreach($data as $key => $val){				if(empty($val['payname'])){
					unset($data[$key]);
				}
			}
		}
		return $data;
	}

		public function count_user($where = '1')
	{
		$sql = "SELECT o.userid as id,m.username,m.money as moneyless,SUM(o.money) AS rechargemoneys FROM ".table('recharge_order')." o LEFT JOIN ".table('members')." m ON o.userid = m.uid WHERE o.status = 1 GROUP BY o.userid ORDER BY SUM(o.money) DESC,o.userid ASC";
		logic('isearcher')->Linker($sql);
		$sql = page_moyo($sql);
		$recharge_data = dbc(DBCMax)->query($sql)->done();
		$sql = "SELECT o.uid as id,m.username,m.money as moneyless,SUM(o.money*o.mutis) AS trademoneys FROM ".table('ticket')." o LEFT JOIN ".table('members')." m ON o.uid = m.uid WHERE o.status = 1 AND ".$this->query_format_sql()." GROUP BY o.uid ORDER BY SUM(o.money*o.mutis) DESC,o.uid ASC";
		$sql = page_moyo($sql);
		$trade_data = dbc(DBCMax)->query($sql)->done();
		$sql = "SELECT o.userid as id,m.username,m.money as moneyless,SUM(o.totalprice) as paymoneys FROM ".table('order')." o LEFT JOIN ".table('members')." m ON o.userid = m.uid WHERE o.totalprice > 0 AND o.pay = 1 AND ".$where." GROUP BY o.userid ORDER BY SUM(o.totalprice) DESC,o.userid ASC";
		logic('isearcher')->Linker($sql);
		$sql = page_moyo($sql);
		$order_data = dbc(DBCMax)->query($sql)->done();
		if($order_data){
			if($trade_data){
				foreach($trade_data as $key => $val){
					$f = true;
					foreach($order_data as $k => $v){
						if($val['id'] == $v['id']){
							$order_data[$k]['trademoneys'] = $val['trademoneys'];
							$f = false;
						}
					}
					if($f){
						$order_data[] = $val;
					}
				}
			}
			if($recharge_data){
				foreach($recharge_data as $key => $val){
					$f = true;
					foreach($order_data as $k => $v){
						if($val['id'] == $v['id']){
							$order_data[$k]['rechargemoneys'] = $val['rechargemoneys'];
							$f = false;
						}
					}
					if($f){
						$order_data[] = $val;
					}
				}
			}
		}else{
			$order_data = $recharge_data;
		}
		if($order_data){
			foreach($order_data as $key => $val){
				if(empty($val['username'])){					unset($order_data[$key]);
					continue;
				}
				$order_data[$key]['paymoneys'] = $val['paymoneys'] ? $val['paymoneys'] : '0.00';
				$order_data[$key]['rechargemoneys'] = $val['rechargemoneys'] ? $val['rechargemoneys'] : '0.00';
				$order_data[$key]['trademoneys'] = $val['trademoneys'] ? $val['trademoneys'] : '0.00';
			}
		}
		return $order_data;
	}

		public function count_fund($where = '1', $fix_data = false)
	{
		$fund_data = array();
		if(count($_GET) < 4) {
			$sql = "SELECT p.id,o.productid,p.flag as productname,p.nowprice as productprice,
				p.fundprice,p.type,s.sellername,s.profit_pre,m.username,
				COUNT(*) as ordercount,SUM(o.productnum) as productcount,
				SUM(o.totalprice) as moneycount,SUM(o.expressprice) as expressprices,
				SUM(o.totalprice-o.expressprice-o.productnum*o.productprice) as arrtsmoney
			FROM ".table('order')." o LEFT JOIN ".table('product')." p ON o.productid = p.id
				LEFT JOIN ".table('seller')." s ON p.sellerid = s.id
				LEFT JOIN ".table('members')." m ON s.userid = m.uid
			WHERE o.totalprice > 0 AND o.pay = 1 AND o.status != ".ORD_STA_Virtual." AND o.status != ".ORD_STA_Virtual_AlREDAY." AND ".$where."
			GROUP BY o.productid
			ORDER BY o.productid ASC";
			logic('isearcher')->Linker($sql);
			$sql = page_moyo($sql);
			$fund_data = dbc(DBCMax)->query($sql)->done();
		} else {
			
			$sql1 = "SELECT p.id,
			  p.flag       AS productname,
			  p.nowprice   AS productprice,
			  p.fundprice,
			  p.type,
			  s.sellername,
			  s.profit_pre,
			  m.username
			 FROM ".table('product')." p
			  LEFT JOIN ".table('seller')." s
			    ON p.sellerid = s.id
			  LEFT JOIN ".table('members')." m
			    ON s.userid = m.uid
			 WHERE {$where}
			 ORDER BY p.id DESC";
	    	logic('isearcher')->Linker($sql1);
			$sql1 = page_moyo($sql1);
			$datas = dbc(DBCMax)->query($sql1)->done();
			if(empty($datas)) {
				return $fund_data;
			}
			$pids = array();
			$products = array();
			foreach($datas as $row) {
				$pids[$row['id']] = $row['id'];
				$products[$row['id']] = $row;
			}
			unset($datas);

			
			$sql2 = "SELECT o.productid,
			  COUNT(1)    AS ordercount,
			  SUM(o.productnum) AS productcount,
			  SUM(o.totalprice) AS moneycount,
			  SUM(o.expressprice) AS expressprices,
			  SUM(o.totalprice-o.expressprice-o.productnum*o.productprice) AS arrtsmoney
			 FROM ".table('order')." o
			 WHERE o.productid IN('".implode("','", $pids)."')
				AND o.totalprice > 0
			    AND o.pay = 1
			    AND o.status != ".ORD_STA_Virtual."
			    AND o.status != ".ORD_STA_Virtual_AlREDAY."
			    AND ".$this->query_format_sql('o.paytime', 'fund', 0)."
			 GROUP BY o.productid";
			$datas = dbc(DBCMax)->query($sql2)->done();
			$orders = array();
			foreach($datas as $row) {
				$orders[$row['productid']] = $row;
			}
			unset($datas);

			
			foreach($products as $key => $val) {
				$val = array_merge($products[$val['id']], (array) $orders[$val['id']]);
				if(empty($val['productname'])) { 					continue;
				}
				$fund_data[$key] = $val;
			}
		}

		if($fund_data){
			foreach($fund_data as $key => $val) {
				if(empty($val['productname'])) { 					continue;
				}

				$__fundprice = floatval($val['fundprice']);
				$__profit_pre = floatval($val['profit_pre']);
				$fund_data[$key]['fundprice'] = $__fundprice < 0 ? '--' : $__fundprice;
				$fund_data[$key]['profit_pre'] = $val['profit_pre'] > 0 ? $__profit_pre.'%' : '--';
				$orderids = array();
				if($val['productid'] > 0) {
					$orderids = $this->get_query_order($val['productid']);
				}
				$numbers = array();
				$ticketids = array();
				$used_orderids = array();
				if($val['type'] == 'stuff'){
					$sq = "SELECT orderid, productnum FROM ".table('order')." WHERE orderid IN('".implode("','",$orderids)."') AND `process` = 'TRADE_FINISHED'";
					$dat = array();
					if($orderids) {
						$dat = dbc(DBCMax)->query($sq)->done();
					}
					if($dat) {
						foreach($dat as $row) {
							$fund_data[$key]['ticketused'] += $row['productnum'];
							$used_orderids[$row['orderid']] = $row['orderid'];
						}
					}
					
				}elseif($val['type'] == 'ticket'){
					$where_usetime = $this->query_format_sql('t.usetime', 'fund', 0);

					

										$fund_data[$key]['ticketused'] = 0;
					$fund_data[$key]['expendcount'] = 0;
					$sq = "SELECT `ticketid`, `orderid`, `mutis`, (`mutis` * `money`) as `mutis_money`, `number` FROM ".table('ticket')." t WHERE t.`productid`='{$val['id']}' AND t.status = '".TICK_STA_Used."' AND " . $where_usetime;
										$dat = dbc(DBCMax)->query($sq)->done();
					if($dat) {
						foreach($dat as $row) {
							$numbers[$row['ticketid']] = $row['number'];
							$ticketids[] = $row['ticketid'];
							$used_orderids[$row['orderid']] = $row['orderid'];
							$fund_data[$key]['ticketused'] += $row['mutis'];
							$fund_data[$key]['expendcount'] += $row['mutis_money'];
						}
						
					}
										
				}else{
					$fund_data[$key]['waitsendgoods'] = '--';
					$fund_data[$key]['ticketused'] = '--';
					$fund_data[$key]['ticketunused'] = '--';
					$fund_data[$key]['ticketfailed'] = '--';
					$fund_data[$key]['ticketoverdue'] = '--';
				}
				$fund_data[$key]['expendcount'] = round($fund_data[$key]['expendcount'], 2);

				$sqa = '';
				if('ticket' == $val['type']) {
					if($ticketids) {
						$sqa = "SELECT ticketid,deal_money,fund_money,salary_money,rec_all FROM ".table('rebate_log')." WHERE `ticketid` IN('".implode("','",$ticketids)."') AND `type` = 'master'";
					}
				} else {
					if($used_orderids) {
						$sqa = "SELECT orderid,deal_money,fund_money,salary_money,rec_all FROM ".table('rebate_log')." WHERE `orderid` IN('".implode("','",$used_orderids)."') AND `type` = 'master'";
					}
				}
				$dat = array();
				if($sqa) {
					$dat = dbc(DBCMax)->query($sqa)->done();
				}
				$refundmoneys = $rebatmoneys = 0;
				if($dat){
					foreach($dat as $v){
						if(($v['rec_all'] && $v['fund_money'] >= 0) || (!$v['rec_all'] && $v['fund_money'] > 0)){
							$refundmoneys += $v['fund_money'];
							$rebatmoneys = $rebatmoneys + ($v['deal_money']-$v['fund_money']);
						}elseif($v['salary_money'] > 0){
							$refundmoneys = $refundmoneys + ($v['deal_money']-$v['salary_money']);
							$rebatmoneys += $v['salary_money'];
						} else {
							$refundmoneys += $v['deal_money'];
						}

						if('ticket' == $val['type']) {
							unset($numbers[$v['ticketid']]);
						} else {
							unset($used_orderids[$v['orderid']]);
						}
					}
				}
				$fund_data[$key]['refundmoneys'] = round($refundmoneys, 2); 				$fund_data[$key]['rebatmoneys'] = round($rebatmoneys, 2); 
				$__need_fix = (abs($fund_data[$key]['expendcount'] - $fund_data[$key]['refundmoneys'] - $fund_data[$key]['rebatmoneys']) > 0.1);

								if('stuff' == $val['type']) {
					$fund_data[$key]['expendcount'] = $fund_data[$key]['refundmoneys'] + $fund_data[$key]['rebatmoneys'];
					if(($__need_fix || ($fund_data[$key]['ticketused']>0 && $fund_data[$key]['refundmoneys']<=0)) && $used_orderids && $fix_data) {
						$this->_rebate_log_data_fix_by_order($used_orderids);
					}
				} elseif ('ticket' == $val['type']) {
										if($__need_fix) {
						if($__fundprice >= 0) {
							$fund_data[$key]['refundmoneys'] = round($__fundprice * $fund_data[$key]['ticketused'], 2);
							$fund_data[$key]['rebatmoneys'] = max(0, round($fund_data[$key]['expendcount'] - $fund_data[$key]['refundmoneys'], 2));
						} elseif ($__profit_pre > 0) {
							$fund_data[$key]['rebatmoneys'] = round($fund_data[$key]['expendcount'] * $fund_data[$key]['profit_pre'] / 100, 2);
							$fund_data[$key]['refundmoneys'] = $fund_data[$key]['expendcount'] - $fund_data[$key]['rebatmoneys'];
						} else {
							$fund_data[$key]['refundmoneys'] = $fund_data[$key]['expendcount']; 							$fund_data[$key]['rebatmoneys'] = 0; 						}
						if($numbers && $fix_data) {
							$this->_rebate_log_data_fix_by_ticket($numbers);
						}
					}
				}

								if($fund_data[$key]['productcount'] < 1 && $fund_data[$key]['ticketused'] < 1) {
					unset($fund_data[$key]);
				}

							}
		}

		$this->data_need_fix = ($__need_fix && ($numbers || $used_orderids));

		return $fund_data;
	}

	private function get_query_order($product_id)
	{
		$orderids = array();
		$sql = "SELECT o.orderid FROM ".table('order')." o WHERE o.productid ='".$product_id."' AND o.totalprice > 0 AND o.pay = 1 AND o.status = 1 AND ".$this->query_format_sql('o.paytime','fund',0);
		$orders = dbc(DBCMax)->query($sql)->done();
		if($orders){
			foreach($orders as $val){
				$orderids[$val['orderid']] = $val['orderid'];
			}
		}
		return $orderids;
	}

	private function query_format_sql($field = 'o.usetime',$mcode = 'user',$ms = 1)
	{
		$begin = $_GET['iscp_tvbegin_salecount_'.$mcode];
		$finish = $_GET['iscp_tvfinish_salecount_'.$mcode];
		if(in_array($field, array('o.usetime', 't.usetime'))){
			$ts_begin = $begin ? date('Ymd',strtotime($begin)) : 0;
						$ts_finish = 0;
			if($finish) {
				$finish_time = strtotime($finish);
				$ts_finish = date('Ymd', mktime(24, 0, 0, date('m', $finish_time), date('d', $finish_time), date('Y', $finish_time)));
			}
		}else{
			$ts_begin = $begin ? strtotime($begin) : 0;
			$ts_finish = $finish ? (strtotime($finish) + 86399) : 0;
		}
		$ts[] = $ts_begin ? $field.' >= '.$ts_begin : $field.' >0 ';
		$ts_finish && $ts[] = $field.' <= '.$ts_finish;
		if($_GET['ssrc'] && $_GET['sstr'] && $ms){
			if($_GET['ssrc'] == 'uid'){
				$ts[] = 'm.uid = '.(int)$_GET['sstr'];
			}elseif($_GET['ssrc'] == 'username'){
				$ts[] = 'm.username LIKE "%'.$_GET['sstr'].'%"';
			}elseif($_GET['ssrc'] =='seller_name'){
				$ts[] = 's.sellername LIKE "%'.$_GET['sstr'].'%"';
			}elseif($_GET['ssrc'] =='seller_user_name'){
				$ts[] = 'm.username LIKE "%'.$_GET['sstr'].'%"';
			}
		}
		$sql_where = implode(' AND ', $ts);
		return $sql_where;
	}

	
	private function _rebate_log_data_fix_by_ticket($numbers) {
		if($numbers) {
			foreach($numbers as $ticketid=>$number) {
				logic('rebate')->Add_Rebate_For_Ticket(logic('coupon')->ProductGet($number), true);
			}
		}
	}

	private function _rebate_log_data_fix_by_order($orderids) {
		if($orderids) {
			foreach($orderids as $orderid) {
				logic('rebate')->Add_Rebate_For_Item(logic('order')->GetOne($orderid), true);
			}
		}
	}

}
