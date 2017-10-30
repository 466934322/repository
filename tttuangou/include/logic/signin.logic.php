<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name signin.logic.php
 * @date 2015-01-30 15:10:50
 */
 




class SigninLogic {

	public $signin_open = false;

	public $notice = array();

	public function SigninLogic() {
		$this->signin_open = (ini('signin.open') && ini('settings.api_version'));
	}

	public function html() {
		if(false == $this->signin_open) {
			return ;
		}
		include handler('template')->file('@html/signin');
	}

	public function is_signin($uid = 0, $timestamp = 0) {
		return (true == self::signin($uid, $timestamp, true));
	}

	public function signin($uid = 0, $timestamp = 0, $check = false) {
		if(false == $this->signin_open) {
			return 0;
		}

				if(0 === $uid) {
			$uid = user()->get('id');
		} else {
			$uid = user((int) $uid)->get('id');
		}
		if($uid < 1) {
			return -1;
		}

				$timestamp = ($timestamp ? (is_numeric($timestamp) ? $timestamp : strtotime($timestamp)) : time());
		$day = date('Y-m-d', $timestamp);
				$user = self::user()->get_one($uid);
		if($day == $user['day']) {
			$this->notice['signin_day'] = $user['count'];
			$nextday_rule = self::rule()->get_one($user['count'] + 1, true);
			$this->notice['nextday_credit'] = $nextday_rule['credit'];
			return -2;
		} else {
			if($check) {
				return 0;
			}
		}

		$yesterday = date('Y-m-d', ($timestamp - 86400));
				$signin_day = (int) ($yesterday == $user['day'] ? $user['count'] : 0) + 1;
				$rule = self::rule()->get_one($signin_day, true);
		

				$this->notice['signin_day'] = $signin_day;
		$this->notice['today_credit'] = $rule['credit'];
		$nextday_rule = self::rule()->get_one($signin_day + 1, true);
		$this->notice['nextday_credit'] = $nextday_rule['credit'];

				$data = array(
			'uid' => $uid,
			'day' => $day,
			'credit' => $rule['credit'],
			'timestamp' => $timestamp
		);
		self::log()->save($data);

		self::user()->update(array_merge($data, array(
				'total' => $user['total'] + 1,
				'count' => $signin_day,
				'credit' => $user['credit'] + $rule['credit']
			)));

				logic('credit')->log($uid, $rule['credit'], '签到送积分：连续签到第 <b>'.$signin_day.'</b> 天，获得 <b>'.$rule['credit'].'</b> 积分', 'signin');

		return 1;
	}

	public function rule() {
		return loadInstance('logic.signin.ext.rule', 'SigninLogic_ext_Rule');
	}

	public function user() {
		return loadInstance('logic.signin.ext.user', 'SigninLogic_ext_User');
	}

	public function log() {
		return loadInstance('logic.signin.ext.log', 'SigninLogic_ext_Log');
	}

}

class SigninLogic_ext_Rule {

	public function get_all() {
		return dbc(DBCMax)->select('signin_rule')->order(' `day` ASC, `id` ASC ')->done();
	}

	public function get_one($id, $is_day = false) {
		$id = max(0, (int) $id);
		$where = '';
		if($is_day) {
			$where = " `day`<='{$id}' ";
		} else {
			$where = " `id`='{$id}' ";
		}
		return dbc(DBCMax)->select('signin_rule')->where($where)->order(' `day` DESC, `id` DESC ')->limit(1)->done();
	}

	public function save($list) {
		foreach($list as $id=>$row) {
			$row['day'] = max(0, (int) $row['day']);
			$row['credit'] = (int) $row['credit'];
			if($row['day']) {
				if(is_numeric($id)) {
					dbc(DBCMax)->update('signin_rule')->data(array('day'=>$row['day'], 'credit'=>$row['credit']))->where(array('id'=>$id))->done();
				} else {
					$one = dbc(DBCMax)->select('signin_rule')->where(array('day'=>$row['day']))->limit(1)->done();
					if(!$one) {
						dbc(DBCMax)->insert('signin_rule')->data(array('day'=>$row['day'], 'credit'=>$row['credit']))->done();
					} else {
						dbc(DBCMax)->update('signin_rule')->data(array('credit'=>$row['credit']))->where(array('day'=>$row['day']))->done();
					}
				}
			} else {
				if(is_numeric($id)) {
					dbc(DBCMax)->delete('signin_rule')->where(array('id'=>$id))->done();
				}
			}
		}
	}

}

class SigninLogic_ext_User {

	public function get_one($uid, $fix = 'auto') {
		$uid = (int) $uid;
		$where = array('uid'=>$uid);
		$ret = dbc(DBCMax)->select('signin_user')->where($where)->limit(1)->done();
		if('auto' == $fix && empty($ret)) {
			dbc(DBCMax)->insert('signin_user')->data($where)->done();
			$ret = dbc(DBCMax)->select('signin_user')->where($where)->limit(1)->done();
		}
		return $ret;
	}

	public function update($data) {
		return dbc(DBCMax)->update('signin_user')->data($data)->where(array('uid'=>$data['uid']))->done();
	}

}

class SigninLogic_ext_Log {

	public function save($data) {
		return dbc(DBCMax)->insert('signin_log')->data($data)->done();
	}

}