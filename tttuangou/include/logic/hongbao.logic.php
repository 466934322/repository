<?php

/**
 * 逻辑区：红包管理
 * @copyright (C)2015 Cenwor Inc.
 * @author 狐狸<foxis@qq.com>
 * @package hongbao.logic.php
 * @version $Id$
 */

class HongbaoLogic {

	public $hongbao_enable = false;

	public $notice = array();

	public function HongbaoLogic() {
		$this->hongbao_enable = (ini('hongbao.enable') && ini('settings.api_version'));
	}

	public function html() {
		if(false == $this->hongbao_enable) {
			return ;
		}
		include handler('template')->file('@html/hongbao');
	}

	
	public function lottery($hongbao_id = null, $uid = null) {
		$uid = (null === $uid ? user()->get('id') : (int) $uid);
		if($uid < 1) {
			return '请登录后再操作';
		}

		if(user($uid)->get('id') < 1) {
			return '用户UID错误';
		}

		$hongbao_id = (int) $hongbao_id;
		if($hongbao_id < 1) {
			$data = $this->get();
		} else {			
			$data = $this->get_one($hongbao_id);
		}
		if(false == $data) {
			return '指定的红包已经不存在了';
		}
		
		$hongbao_id = (int) $data['id'];
		if($hongbao_id < 1) {
			return '请指定红包ID';
		}

		if(false == $data['enable']) {
			return '该派送红包活动还未启用';
		}

		if($data['time_start'] && $data['timestamp_start'] > time()) {
			return '该派送红包活动还未开始';
		}

		if($data['time_end'] && $data['timestamp_end'] < time()) {
			return '该派送红包活动已经结束了';
		}

		if($data['total_num'] <= $data['send_num']) {
			return '所有的红包都已经派发完了';
		}

		if($data['total_money'] > 0 && $data['total_money'] <= $data['send_money']) {
			return '该红包已经派发完毕';
		}

		$up = array();
		$time = time();
		$day = date('Y-m-d', $time);

		$total_join_num = $this->count(" `uid`='{$uid}' AND `hongbao_id`='{$hongbao_id}' ");
		if($total_join_num >= $data['limit_total_join_num']) {
			return '您已经使用完所有的抽奖次数';
		}
		if($total_join_num < 1) {
			$up['total_pp'] = max(0, (int) $data['total_pp']) + 1;
		}

		if($data['limit_day_join_num'] > 0) {
			$day_join_num = $this->count(" `uid`='{$uid}' AND `hongbao_id`='{$hongbao_id}' AND `day`='{$day}' ");
			if($day_join_num >= $data['limit_day_join_num']) {
				return '您今天的抽奖次数已经用完了';
			}
		}

		if($data['limit_total_num'] > 0) {
			$total_num = ($total_join_num > 0 ? $this->count(" `uid`='{$uid}' AND `hongbao_id`='{$hongbao_id}' AND `money`>'0' ") : 0);
			if($total_num >= $data['limit_total_num']) {
				return '您的中奖次数已经达到上限';
			}
		}

		if($data['limit_day_num'] > 0) {
			$day_num = $this->count(" `uid`='{$uid}' AND `hongbao_id`='{$hongbao_id}' AND `day`='{$day}' AND `money`>'0' ");
			if($day_num >= $data['limit_day_num']) {
				return '您今天的中奖次数已经达到上限';
			}
		}

				$pow = 1000000;
		$all_pp = round($data['total_num'] / ($data['all_pp'] * $data['limit_total_join_num']), 6);
		if($all_pp < 1 && mt_rand(0, $pow) > ($all_pp * $pow)) {
			$money = 0;
		} else {
						$money = $this->lottery_money($data);
		}

				if($money > 0) {
			$up['send_num'] = max(0, (int) $data['send_num']) + 1;
			$up['send_money'] = round((float) $data['send_money'] + $money, 2);
		}

				dbc(DBCMax)->insert('hongbao_log')->data(array(
				'hongbao_id'=>$hongbao_id,
				'uid' => $uid,
				'day' => $day,
				'money' => $money,
				'timestamp' => $time,
			))->done();

		$msg = '请继续努力';
				if($up) {
			dbc(DBCMax)->update('hongbao')->data($up)->where(array('id'=>$hongbao_id))->done();

						if($money > 0) {
				$msg = '恭喜您，在<b>'.$data['name'].'</b>活动抽中红包<b>'.$money.'元</b>';
				logic('me')->money()->add($money, $uid, array(
					'name' => '【红包】' . $data['name'],
					'intro' => $msg
				));
			}
		}

		return $msg;
	}

	

	private function lottery_money($data) {
		$surplus_num = (int) ($data['total_num'] - $data['send_num']); 		if($surplus_num <= 0) { 			return 0;
		}

		$min = (float) $data['money_min']; 		$max = (float) $data['money_max']; 		if($min == $max) { 			return ($min > 0 ? $min : $this->random_money(0, 1)); 		}

		if($data['total_money'] <= 0) { 			return $this->random_money($min, $max);
		}

		$surplus_money = (float) ($data['total_money'] - $data['send_money']); 		if($surplus_money <= 0) { 			return 0;
		}
		
		if($max < $min) { 			$max = $surplus_money;
		}		
		if($surplus_num <= 1) { 			return ($surplus_money <= $max ? $surplus_money : $this->random_money($min, $max));
		}

		$smax = (($surplus_money - $surplus_num * $min) / $surplus_num); 		$smax = $smax * max(1, $this->random_money(1, 5) * $surplus_num / $data['total_num']); 		$lmax = ($smax > $max ? $max : $smax);

		return $this->random_money($min, ($lmax > $min ? $lmax : $min + 0.1));
	}
	private function random_money($min, $max) {
		return round(mt_rand($min * 100, $max * 100) / 100, 2);
	}

	private function count($where) {
		$sql = "SELECT COUNT(1) AS `CNT` FROM ".table('hongbao_log')." WHERE {$where} ";
		$query = dbc()->Query($sql);
		$row = $query->GetRow();
		return max(0, (int) $row['CNT']);
	}

	public function get($enable = false) {
		$where = array();
		if($enable) {
			$where['enable'] = 1;
		}
		$ret = dbc(DBCMax)->select('hongbao')->where($where)->order(' `id` DESC ')->limit(1)->done();
		if(false == $ret) {
			$id = dbc(DBCMax)->insert('hongbao')->data(array('name'=>'送红包', 'enable'=>0))->done();
			$ret = $this->get_one($id);
		}
		return $ret;
	}

	public function get_one($id) {
		$ret = array();
		$id = (int) $id;
		if($id > 0) {
			$ret = dbc(DBCMax)->select('hongbao')->where(array('id'=>$id))->limit(1)->done();
		}
		return $ret;
	}

	public function save($post) {

		$id = (int) $post['id'];
		if($id < 1) {
			return 'ID不能为空';
		}
		$one = $this->get_one($id);
		if(false == $one) {
			return '要修改的内容已经不存在了';
		}

		$post['name'] = trim(strip_tags($post['name']));
		if(empty($post['name'])) {
			return '名称不能为空';
		}

		$post['total_num'] = (int) $post['total_num'];
		if($post['total_num'] < 1) {
			return '请设置总的红包个数';
		}

		$money_min = round(max(0, min((float) $post['money_min'], (float) $post['money_max'])), 2);
		$money_max = round(max(0, max((float) $post['money_min'], (float) $post['money_max'])), 2);
		if(!$money_min && !$money_max) {
			return '请设置单个红包的金额';
		}

		$post['all_pp'] = (int) $post['all_pp'];
		if($post['all_pp'] < 1) {
			return '请设置预计参与人数';
		}

		$post['limit_total_join_num'] = (int) $post['limit_total_join_num'];
		if($post['limit_total_join_num'] < 1) {
			return '请设置个人最多允许抽奖次数';
		}

		$data = array(
			'enable' => ($post['enable'] ? 1 : 0),
			'name' => $post['name'],
			'intro' => trim(strip_tags($post['intro'])),
			'time_start' => $post['time_start'],
			'timestamp_start' => ($post['time_start'] ? strtotime($post['time_start']) : 0),
			'time_end' => $post['time_end'],
			'timestamp_end' => ($post['time_end'] ? strtotime($post['time_end']) : 0),
			'total_money' => round((float) $post['total_money'], 2),
			'total_num' => $post['total_num'],
			'money_min' => $money_min,
			'money_max' => $money_max,
			'all_pp' => $post['all_pp'],
			'limit_total_join_num' => $post['limit_total_join_num'],
			'limit_day_join_num' => $post['limit_day_join_num'],
			'limit_total_num' => (int) $post['limit_total_num'],
			'limit_day_num' => (int) $post['limit_day_num'],
		);

		dbc(DBCMax)->update('hongbao')->data($data)->where(array('id'=>$id))->done();
	}	

	public function log() {
		;
	}

}
