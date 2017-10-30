<?php

/**
 * 逻辑区：积分商城管理
 * @copyright (C)2015 Cenwor Inc.
 * @author 狐狸<foxis@qq.com>
 * @package creditmall.logic.php
 * @version $Id$
 */

class CreditmallLogic {

	public $is_open = false;

	public function CreditmallLogic() {
		$this->is_open = (ini('creditmall.open') && ini('settings.api_version'));
	}

	public function goods() {
		return loadInstance('logic.creditmall.ext.goods', 'CreditmallLogic_ext_Goods');
	}

	public function order() {
		return loadInstance('logic.creditmall.ext.order', 'CreditmallLogic_ext_Order');
	}

	public function order_action() {
		return loadInstance('logic.creditmall.ext.order_action', 'CreditmallLogic_ext_OrderAction');
	}

}

class CreditmallLogic_ext_Goods {

	public function get_list($params = array()) {
		$sql_where = '';
		$wheres = array();
		if($params['expire_min']) {
			$params['expire_min'] = (int) $params['expire_min'];
			$wheres[] = " (`expire`<1 OR `expire`>='{$params['expire_min']}') ";
		}
		if($params['price_max']) {
			$params['price_max'] = (int) $params['price_max'];
			$wheres[] = " `price`<='{$params['price_max']}' ";
		}
        if($params['category']){
            $wheres[] = " `category`='{$params['category']}' ";
        }
		if($wheres) {
			$sql_where = " WHERE " . implode(' AND ', $wheres);
		}
        if($params['sort_type']){
            $order = $params['sort_type'];
        }else{
            $order = ' `order` desc, `id` desc';
        }
		$sql = "select * from ".table('creditmall_goods')." {$sql_where} order by".$order;
        $sql = page_moyo($sql);
		return dbc(DBCMax)->query($sql)->done();
	}

	public function get_one($id, $is_name = false) {
		$id = max(0, (int) $id);
		$where = '';
		if($is_name) {
			$where = " `name`='{$id}' ";
		} else {
			$where = " `id`='{$id}' ";
		}
		return dbc(DBCMax)->select('creditmall_goods')->where($where)->limit(1)->done();
	}

	public function save($data) {
		return dbc(DBCMax)->insert('creditmall_goods')->data($data)->done();
	}

	public function update($data, $id) {
		$id = (int) $id;
		if($id > 0 && $data) {
			return dbc(DBCMax)->update('creditmall_goods')->data($data)->where(array('id'=>$id))->done();
		}
	}

	public function delete($id) {
		$id = (is_numeric($id) ? $id : 0);
		if($id) {
			dbc(DBCMax)->delete('creditmall_goods')->where(array('id'=>$id))->done();
		}
	}

}

class CreditmallLogic_ext_Order {

	public function get_list($params = array()) {
		$sql_where = '';
		$wheres = array();
		if($params['goods_id']) {
			$params['goods_id'] = (int) $params['goods_id'];
			$wheres[] = " `goods_id`='{$params['goods_id']}' ";
		}
		if($params['uid']) {
			$params['uid'] = (int) $params['uid'];
			$wheres[] = " `uid`='{$params['uid']}' ";
		}
		if($params['sn']) {
			$params['sn'] = (is_numeric($params['sn']) ? $params['sn'] : 0);
			$wheres[] = " `sn`='{$params['sn']}' ";
		}
		if($wheres) {
			$sql_where = " WHERE " . implode(' AND ', $wheres);
		}

		$sql = "select * from ".table('creditmall_order')." {$sql_where} order by `id` desc";
		$sql = page_moyo($sql);
		return dbc(DBCMax)->query($sql)->done();
	}

	public function get_one($id, $is_sn = false) {
		$id = (is_numeric($id) ? $id : 0);

		$key = ($is_sn ? 'sn' : 'id');
		return dbc(DBCMax)->select('creditmall_order')->where(array($key=>$id))->limit(1)->done();
	}

	public function get_last($uid = 0) {
		$uid = (is_numeric($uid) ? $uid : 0);

		return dbc(DBCMax)->select('creditmall_order')->where(array('uid'=>$uid))->order(' `id` DESC ')->limit(1)->done();
	}

	
	public function create($params) {
		$params['address'] = trim(strip_tags($params['address']));
		if(empty($params['address'])) {
			return '地址不能为空';
		}
		$params['tel'] = (is_numeric($params['tel']) ? $params['tel'] : 0);
		if(empty($params['tel'])) {
			return '电话不能空';
		}

		$uid = ($params['uid'] ? $params['uid'] : user()->get('id'));
		if($uid < 1) {
			return '您需要先登录或注册一个账号才能继续操作';
		}
		$user = user($uid)->get();
		if(false == $user) {
			return '用户已经不存在了，请重新登录';
		}

		$params['goods_id'] = (int) $params['goods_id'];
		if($params['goods_id'] < 1) {
			return '请指定一个您要兑换的商品';
		}
		$goods = logic('creditmall')->goods()->get_one($params['goods_id']);
		if(false == $goods) {
			return '您要兑换的商品已经不在了';
		}
		if($goods['total'] < 1) {
			return '您要兑换的商品已经兑完了';
		}
		if($goods['expire'] > 0 && $goods['expire'] < time()) {
			return '您要兑换的商品已经下架了';
		}
		if($goods['price'] <= 0) {
			return '您要兑换的商品积分错误';
		}
		if($user['scores'] <= 0 || $user['scores'] < $goods['price']) {
			return '您的积分不够了';
		}

		$params['goods_num'] = 1;

		$data = array(
			'sn' => date('YmdHis') . $uid . mt_rand(100000, 999999),
			'uid' => $uid,
			'username' => $user['name'],
			'goods_id' => $goods['id'],
			'goods_name' => $goods['name'],
			'goods_price' => $goods['price'],
			'goods_num' => $params['goods_num'],
			'pay_money' => round($goods['price'] * $params['goods_num']),
			'address' => $params['address'],
			'tel' => $params['tel'],
			'qq' => (is_numeric($params['qq']) ? $params['qq'] : ''),
			'add_time' => time(),
			'pay_time' => '0',
		);
		$order_id = dbc(DBCMax)->insert('creditmall_order')->data($data)->done();
		if($order_id < 1) {
			return '订单创建失败';
		}

				$this->status('0', $order_id);

				logic('credit')->log($uid, - $data['pay_money'], '积分商城：兑换商品 <b>'.$data['goods_name'].'</b>，兑换数量 <b>'.$data['goods_num'].'</b> 份', 'creditmall');

		$nuser = dbc(DBCMax)->select('members')->where(array('uid'=>$uid))->limit(1)->done();
		if($nuser['scores'] + $data['pay_money'] == $user['scores']) {
						$this->update(array(
					'id' => $order_id,
					'pay_time' => time(),
				));

						logic('creditmall')->goods()->update(array(
					'total' => $goods['total'] - $data['goods_num'],
					'count' => $goods['count'] + $data['goods_num'],
					'order_count' => $goods['order_count'] + 1,
				), $data['goods_id']);

						notify($uid, 'logic.creditmall.order.create', array(
					'username' => user($uid)->get('name'),
					'time' => date('Y-m-d H:i:s'),
				));
		} else {
			return '积分扣减失败';
		}
		
	}

	
	private function cancle($order) {
		if(false == $order || 2 == $order['status']) {
			return ;
		}
				logic('credit')->log($order['uid'], $order['pay_money'], '积分商城：取消订单 <b>'.$order['sn'].'</b>，退还积分', 'creditmall');

				$this->update(array(
				'id' => $order['id'],
				'pay_time' => '0',
			));

		$goods = logic('creditmall')->goods()->get_one($order['goods_id']);

				logic('creditmall')->goods()->update(array(
				'total' => $goods['total'] + $order['goods_num'],
				'count' => $goods['count'] - $order['goods_num'],
			), $order['goods_id']);
	}

	
	public function status($status, $id) {
		$status = (is_numeric($status) ? $status : 0);
		$data = array('id'=>$id, 'status'=>$status);
		return $this->update($data);
	}

	public function update($data) {
		$id = (is_numeric($data['id']) ? $data['id'] : 0);
		unset($data['id']);
		if($id && $data) {
			if(false == ($order = $this->get_one($id))) {
				return ;
			}
			if(isset($data['status'])) {
				$data['status_time'] = time();
				logic('creditmall')->order_action()->save(array(
						'uid' => user()->get('id'),
						'order_id' => $id,
						'status' => $data['status'],
						'msg' => '',
						'dateline' => $data['status_time']
					));
				if(2 == $data['status']) {
					$this->cancle($order);
				}
				if($data['status'] != $order['status']) {
					notify($order['uid'], 'logic.creditmall.order.status', array(
							'username' => $order['username'],
							'orderid' => $order['sn'],
							'time' => date('Y-m-d H:i:s'),
							'status' => (1 == $data['status'] ? '已发货' : (2 == $data['status'] ? '已取消' : '已确认')),
						));
				}
			}
			return dbc(DBCMax)->update('creditmall_order')->data($data)->where(array('id'=>$id))->done();
		}
	}

}

class CreditmallLogic_ext_OrderAction {

	public function save($data) {
		return dbc(DBCMax)->insert('creditmall_order_action')->data($data)->done();
	}

}