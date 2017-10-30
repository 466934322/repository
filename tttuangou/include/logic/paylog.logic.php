<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name paylog.logic.php
 * @date 2014-12-26 09:13:55
 */
 


 

class PaylogLogic {

	public function get_one($id, $is = 'sign') {
		$id = (is_numeric($id) ? $id : 0);
		if($id < 1) {
			return false;
		}
		$is = (in_array($is, array('id', 'sign', 'trade_no')) ? $is : 'sign');

		return dbc(DBCMax)->select('paylog')->where(array($is => $id))->order(" `id` DESC ")->limit(1)->done();
	}

	public function payfrom($id, $is = 'sign') {
		$one = self::get_one($id, $is);
		return ($one ? $one['payfrom'] : false);
	}

	public function save($data) {
		$sign = (is_numeric($data['sign']) ? $data['sign'] : 0);
		if($sign < 1) {
			return false;
		}
		if(!isset($data['time'])) {
			$data['time'] = time();
		}
		if(!isset($data['status'])) {
			$data['status'] = '__CREATE__';
		}
		if('__CREATE__' == $data['status']) {
			$data['trade_no'] = '__NULL__';
		}
		$where = " `sign`='{$sign}' AND `status`='{$data['status']}' ";
		dbc()->SetTable(table('paylog'));
		if(false == (dbc(DBCMax)->select('paylog')->where($where)->limit(1)->done())) {
			dbc()->Insert($data);

					}
		
	}

}