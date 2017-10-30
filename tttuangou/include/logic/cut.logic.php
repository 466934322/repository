<?php

/**
 * 逻辑区：优惠立减管理
 * @copyright (C)2015 Cenwor Inc.
 * @author 狐狸<foxis@qq.com>
 * @package logic
 * @name cut.logic.php
 * @version 1.0
 */

class CutLogic {

	
	public function get($product_id, $uid = null) {
		$cuts = array();
		if(is_array($product_id)) {
			$product = $product_id;
			$product_id = $product['id'];
		} else {
			$product_id = (is_numeric($product_id) ? $product_id : 0);
		}
		if($product_id < 1) {
			return cuts;
		}
		$product = ($product ? $product : logic('product')->GetOne($product_id));
		if($product) {
			$this->_if_newbie_cut($product, (null === $uid ? user()->get('id') : $uid), $cuts);
			$this->_if_client_cut($product, $cuts);
		}
		return $cuts;
	}

	
	public function html($data) {
		switch (mocod()) {
			case 'buy.checkout':
								$pro_cuts = $this->snapshot_pro($data['id']);
				$pro_cuts && $pro_cuts['enable'] && include handler('template')->file('cuts_selector');
				break;
			case 'buy.order':
								$ord_cuts = array();
				foreach ($data as $order) {
				    $ord_cuts = array_merge($ord_cuts, (array) $this->order_get($order['orderid']));
				}
				$ord_cuts && include handler('template')->file('cuts_displayer');
				break;
		}
	}

	
	public function Accessed($action, &$order) {
		if ($action == 'order.save') {
						$cuts = $_POST['cut_f_'];
						if($cuts) {
				return $this->save($order, $cuts);
			}
		}

		if ($action == 'order.show') {
			$sign = $order['orderid'];
			$cut = $this->order_cut($sign);
			if ($cut) {
				$order['price_of_total'] -= $cut;
			}
		}
	}

	public function save($order, $cuts) {
		$cut = 0;
		settype($cuts, 'array');
		#取出所有该商品属性列表
		$cuts_all = $this->get($order['productid'], $order);
		if (!$cuts_all) {
			return 0;
		}

		#提交过来的属性合法性判断并保存
		$sign = (is_numeric($order['orderid']) ? $order['orderid'] : 0);
		if($sign) {
			foreach($cuts_all as $row) {
				$flag = $row['flag'];
				if($flag && $row['enable'] && (isset($cuts[$flag]) || in_array($flag, $cuts))) {
					$data = $row;
					$data['sign'] = $sign;
					$data['timestamp'] = time();
					$rr = dbc(DBCMax)->select('order_cut')->where(array('sign'=>$sign, 'flag'=>$flag))->done();
					if($rr) {
						dbc(DBCMax)->update('order_cut')->data($data)->where(array('id'=>$rr['id']))->done();
					} else {
						dbc(DBCMax)->insert('order_cut')->data($data)->done();
						$cut += $row['cut'];
					}
				}
			}
		}

		return $cut;
	}

	
	public function order_calc($sign, &$price_total,$productnum=1) {
		$cut = $this->order_cut($sign);
        if($sign) {
            $order_cut = dbc(DBCMax)->select('order_cut')->where(array('sign'=>$sign))->limit(1)->done();
        }
		if ($cut && $price_total >= $cut) {
            if($order_cut['flag'] != 'newbie_cut'){
                $cut *= $productnum;
            }
			$price_total -= $cut;
		}
		return $cut;
	}

	public function snapshot($sign, $cuts = array()) {
		$rets = array();
		$cut = 0;
		$enable = false;
		if(false != ($cuts = ($cuts ? $cuts : $this->order_get($sign)))) {
			foreach($cuts as $r) {
				$cut += $r['cut'];
				$enable = ($enable || true === $r['enable']);
			}
			$rets['cuts'] = $cuts;
			$rets['price'] = $cut;
			$rets['count'] = count($cuts);
			$rets['enable'] = $enable;
		}
		return $rets;
	}
	public function snapshot_ord($sign, $cuts = array()) {
		return $this->snapshot($sign, $cuts);
	}
	public function snapshot_pro($product_id, $uid = null) {
		return $this->snapshot(0, $this->get($product_id, $uid));
	}

	private function order_cut($sign) {
		$cut = 0;
		if(false != ($cuts = $this->order_get($sign))) {
			foreach($cuts as $r) {
				$cut += $r['cut'];
			}
		}
		return $cut;
	}

	private function order_get($sign) {
		$sign = (is_numeric($sign) ? $sign : 0);
		if($sign) {
			return dbc(DBCMax)->select('order_cut')->where(array('sign'=>$sign))->done();
		}
	}

	
	private function _if_client_cut($product, &$cuts) {
		$cut = (float) $product['client_cut'];
		if($cut > 0) {
			$cuts[] = array(
					'flag' => 'client_cut',
					'cut' => $cut,
					'enable' => ('api' == WEB_BASE_ENV_DFS::$APPNAME),
					'desc' => '在客户端购买立减'.$cut.'元',
				);
		}
	}

	
	private function _if_newbie_cut($product, $uid, &$cuts) {
		$cut = (float) $product['newbie_cut'];
		if($cut > 0) {
			$cuts[] = array(
					'flag' => 'newbie_cut',
					'cut' => $cut,
					'enable' => (true == $this->_is_newbie($uid)),
					'desc' => '新用户首单立减'.$cut.'元',
				);
		}
	}

	
	private function _is_newbie($uid) {
		static $uid_rets = array();

		$ret = false;
		$order = array();
		if(is_array($uid)) {
			$order = $uid;
			$uid = $order['userid'];
		}
		$uid = max(0, (int) $uid);
		if(empty($order) && isset($uid_rets[$uid])) {
			return $uid_rets[$uid];
		}

		if($uid > 0) {
			$row = dbc(DBCMax)->select('order')->where(array('userid'=>$uid))->order(' `buytime` ASC ')->limit(1)->done();
			if(false == $row) {
				$ret = true;
			} else {
				if(false != $order && $order['orderid'] == $row['orderid']) {
					$ret = true;
				}
			}
		} else {
						$ret = true;
		}
		if(empty($order)) {
			$uid_rets[$uid] = $ret;
		}
		return $ret;
	}

}