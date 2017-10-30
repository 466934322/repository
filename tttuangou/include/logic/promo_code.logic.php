<?php

/**
 * @copyright (C)2016 Cenwor Inc.
 * @author 狐狸<foxis@qq.com>
 * @package logic
 * @name promo_code.logic.php
 * @version 1.0
 */

class Promo_codeLogic {

	public function getInfo($params) {
		$where = '';
		if($params['id']) {
			$where .= ' AND `id`="' . (int) $params['id'] . '" ';
		}
		if($params['code']) {
			$where .= ' AND `code`="' . strtolower(trim($params['code'])) . '" ';
		}
		if(empty($where)) {
			return false;
		}
		return dbc()->FetchFirst("select * from " . table('promo_code') . " where 1 {$where} limit 1");
	}

	public function saveInfo($params) {
		$id = (int) $params['id'];

		$code = strtolower(trim($params['code']));
		if(8 != strlen($code)) {
			return derror('优惠码长度必须为8位');
		}

		$name = $params['name'];

		if($id > 0) {
			$sql = "update " . table('promo_code') . " set `code`='{$code}', `name`='{$name}' where `id`='{$id}'";
		} else {
			$sql = "insert into " . table('promo_code') . " (`code`, `name`) values ('{$code}', '{$name}')";
		}
		dbc()->Query($sql);

		if(dbc()->AffectedRows() < 1) {
			return derror('更新失败');
		}

		if($id < 1) {
			$id = dbc()->Insert_ID();
		}

		return dresult($id);
	}

	public function deleteInfo($params) {
		$ret = 0;
		$info = $this->getInfo($params);
		if(false != $info) {
			dbc()->Query('delete from ' . table('promo_code') . ' where `id`="' . $info['id'] . '"');
			$ret = dbc()->AffectedRows();
		}
		return $ret;
	}

	public function getList($params) {
		$where = '';
		if($params['id']) {
			$where .= ' AND `id`="' . (int) $params['id'] . '" ';
		}
		if($params['code']) {
			$where .= ' AND `code`="' . strtolower($params['code']) . '" ';
		}
		$sql = "select * from " . table('promo_code') . " where 1 {$where} order by `id` desc";
		if($params['limit']) {
			$sql .= ' limit ' . $params['limit'];
		} else {
			$sql = page_moyo($sql);
		}
		return dbc(DBCMax)->query($sql)->done();
	}

	public function isExists($code) {
		$info = $this->getInfo(array('code'=>$code));
		return ($info ? true : false);
	}

	public function generate() {
		return $this->saveInfo(array('code'=>$this->generateOneCode(8)));
	}

	public function massGenerate($length = 20) {
		$length = (int) $length;

		if($length < 1) {
			return false;
		}

		if(1 == $length) {
			return $this->generate();
		}

		$values = array();
		for($i = 0; $i < $length; $i++) {
			$code = $this->generateOneCode(8);
			$values[] = "('{$code}', '')";
		}
		$sql = "insert into " . table('promo_code') . " (`code`, `name`) values " . implode(', ', $values);
		dbc()->Query($sql);
		return dbc()->AffectedRows();
	}

	private function generateOneCode($length = 8, $numeric = 0) {
		$chars = 'abcdefghjkmnpqrstuvwxyz23456789';
		if($numeric) {
			$chars = '0123456789';
		}
		$max = strlen($chars) - 1;
		$hash = '';
		for($i = 0; $i < $length; $i++) {
			$hash .= $chars{mt_rand(0, $max)};
		}
		return $hash;
	}

	
	public function html($data) {
		switch (mocod()) {
			case 'buy.order':
								$ord_cuts = array();
				if(is_array($data) && 1==count($data)) {
					foreach ($data as $order) {
					    if($order['product']['promo_cut'] > 0) {
					    	$ord_cuts = $order;
					    }
					}
				}
				$ord_cuts && include template('promo_cuts_displayer');
				break;
		}
	}

	
	public function Accessed($action, &$order) {
		if ($action == 'order.show') {
			$cut = $this->order_cut($order);
			if ($cut) {
				$order['price_of_total'] -= $cut;
			}
		}
	}

	public function order_cut($order) {
		$cut = 0;
		$order = (is_array($order) ? $order : logic('order')->GetOne($order));
		if(is_array($order) && count($order)) {
			$subOrders = false;
			if($order['extmsg']) {
				$subOrders = unserialize($order['extmsg']);
			}
			if(!$subOrders && $order['promo_code']) {
				if($order['promo_cut'] > 0) {
					$cut = $order['promo_cut'];
				} else {
					if($order['product']['promo_cut']>0) {
						$promoCodeInfo = $this->getInfo(array('code'=>$order['promo_code']));
						if($promoCodeInfo) {
							$cut = $order['product']['promo_cut'] * $order['productnum'];
						}
					}
				}
			}
		}
		return $cut;
	}

	
	public function order_calc($order, &$price_total) {
		$cut = $this->order_cut($order);
		if ($cut && $price_total >= $cut) {
			$price_total -= $cut;
		}
		return $cut;
	}

	public function save($params) {
		$userid = (int) $params['userid'];
		if($userid < 1) {
			return derror('用户ID不能为空');
		}
		$orderid = (is_numeric($params['orderid']) ? $params['orderid'] : 0);
		if($orderid < 1) {
			return derror('订单号不能为空');
		}
		$code = trim($params['code']);
		if(empty($code)) {
			return derror('优惠码不能为空');
		}
		$codeIsExists = $this->isExists($code);
		if(false == $codeIsExists) {
			return derror('优惠码输入错误，本单按照原价结算');
		}
		$orderInfo = logic('order')->GetOne($orderid, $userid);
		if(false == $orderInfo) {
			return derror('订单号错误');
		}
		$orderInfo['promo_code'] = $code;
		$promo_cut = $this->order_cut($orderInfo);
		if($promo_cut > 0) {
			$data = array(
					'promo_code'=>$code,
					'promo_cut'=>$promo_cut,
				);
			logic('order')->update($orderid, $data);
			return dresult('使用优惠码立减 &yen;' . $promo_cut . '元');
		} else {
			return derror('该订单中的产品不支持优惠码立减活动');
		}
	}

}