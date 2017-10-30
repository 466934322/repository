<?php

/**
 * 逻辑区：用户提现相关
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name cash.logic.php
 * @version 1.2
 */

class CashLogic
{
	 
	 public function GetOne($id)
	 {
	 	$id = number($id);
		$sql = '
		SELECT
			*
		FROM
			' . table('cash_order') .'
		WHERE
			orderid = ' . $id;
		$order = dbc()->Query($sql)->GetRow();
		return $order;
	 }
	 
	 public function GetList($where = '1')
	 {
	 	$sql = dbc(DBCMax)->select('cash_order')->where($where)->order('createtime.desc')->sql();
	 	logic('isearcher')->Linker($sql);
	 	$sql = page_moyo($sql);
	 	return dbc(DBCMax)->query($sql)->done();
	 }
	 
	 public function get_last_log($orderid)
	 {
	 	$orderid = (is_numeric($orderid) ? $orderid : 0);
	 	return dbc(DBCMax)->select('cash_order_log')->where(array('orderid'=>$orderid))->order(' `id` DESC ')->limit(1)->done();
	 }
	 
	 public function GetFree($uid,$data)
	 {
	 	$uid = number($uid);
		$array = array(
			'orderid' => $this->__GetFreeID(),
			'userid' => $uid,
			'money' => $data['money'],
			'createtime' => time(),
			'status' => 'no',
			'paytype' => $data['paytype'],
			'alipay' => $data['alipay'],
			'bankname' => $data['bankname'],
			'bankcard' => $data['bankcard'],
			'bankusername' => $data['bankusername']
		);
		dbc()->SetTable(table('cash_order'));
		dbc()->Insert($array);
		$moneys = doubleval($array['money']);
		$sql ='UPDATE ' . table('members').' SET money = money - ' . $moneys . ',forbid_money = forbid_money + ' . $moneys . ' WHERE uid = ' . $uid;
		$query = dbc(DBCMax)->query($sql)->done();
		return $array;
	 }
	 
	public function Where($sql_limit)
	{
		$sql = '
		SELECT
			*
		FROM
			'.table('cash_order').'
		WHERE
			'.$sql_limit.'
		';
		return dbc()->Query($sql)->GetAll();
	}
	
	private function __GetFreeID()
	{
		$id = date('YmdHis', time()). str_pad(rand('1', '99999'), 5, '0', STR_PAD_LEFT);
		$sql = '
		SELECT
			*
		FROM
			' . table('cash_order') . '
		WHERE
			orderid = ' . $id;
		$order = dbc()->Query($sql)->GetRow();
		if ( empty($order) )
		{
			return $id;
		}
		else
		{
			return $this->__GetFreeID();
		}
	}
	
	public function del($id=0)
	{
		$id = number($id);
		$order = $this->GetOne($id);
		if (!$order || $order['paytime'] > 0)
		{
			return ;
		}
		$moneys = doubleval($order['money']);
		$sql ='UPDATE ' . table('members').' SET money = money + ' . $moneys . ',forbid_money = forbid_money - ' . $moneys . ' WHERE uid = ' . $order['userid'];
		$query = dbc(DBCMax)->query($sql)->done();
		return dbc(DBCMax)->delete('cash_order')->where('orderid='.$id.' AND paytime=0')->done();
	}
	
	public function MakeSuccessed($orderid)
	{
		$orderid = number($orderid);
		$order = $this->GetOne($orderid);
		if (!$order || $order['paytime'] > 0)
		{
			return;
		}
				$return = dbc(DBCMax)->update('cash_order')->data(array('paytime'=>time(),'status'=>'doing'))->where('orderid='.$orderid)->done();
		if($return){
			$this->Writelog($orderid);
		}
		return $return;
	}
	public function Getlog($orderid)
	{
		$orderid = number($orderid);
		$sql = dbc(DBCMax)->select('cash_order_log')->where('orderid = '.$orderid)->order('id.asc')->sql();
	 	$sql = page_moyo($sql);
	 	return dbc(DBCMax)->query($sql)->done();
	}
	public function Writelog($orderid,$status='doing',$info='')
	{
		$orderid = number($orderid);
		$status = in_array($status,array('doing','yes','error')) ? $status : 'doing';
		$status_str = array('doing'=>'开始受理','yes'=>'确认提现','error'=>'拒绝提现');
		$info = $info ? $info : '管理员后台操作';
		$array = array(
			'orderid' => $orderid,
			'userid' => MEMBER_ID,
			'username' => MEMBER_NAME,
			'createtime' => time(),
			'status' => $status_str[$status],
			'info' => $info
		);
		dbc()->SetTable(table('cash_order_log'));
		dbc()->Insert($array);
	}
	public function Orderdone($orderid,$status,$info)
	{
		$orderid = number($orderid);
				$order = $this->GetOne($orderid);
		if (!$order || $order['status'] != 'doing')
		{
			$return = false;
		}else{
			$status = in_array($status,array('yes','error')) ? $status : 'error';
			$moneys = doubleval($order['money']);
			$return = dbc(DBCMax)->update('cash_order')->data(array('paytime'=>time(),'status'=>$status))->where('orderid='.$orderid)->done();
			if($status == 'error'){
				$sql ='UPDATE ' . table('members').' SET money = money + ' . $moneys . ',forbid_money = forbid_money - ' . $moneys . ' WHERE uid = ' . $order['userid'];
			}else{
				$sql ='UPDATE ' . table('members').' SET forbid_money = forbid_money - ' . $moneys . ' WHERE uid = ' . $order['userid'];
			}
			$return = dbc(DBCMax)->query($sql)->done();
			$this->Writelog($orderid,$status,$info);
			if($status == 'yes'){
				$data = array(
					'userid' => $order['userid'],
					'type' => 'minus',
					'money' => $order['money'],
					'time' => time(),
					'name' => '用户提现',
					'class' => 'usr',
					'intro' => '提现记录流水号：'.$orderid
				);
				dbc()->SetTable(table('usermoney'));
				return dbc()->Insert($data);
			}

			notify($order['userid'], 'logic.cash.orderdone', array(
					'username' => user($order['userid'])->get('name'),
					'orderid' => $orderid,
					'money' => $order['money'],
					'status' => ('yes' == $status ? '提现成功' : '提现失败'),
				));
		}
		return $return;
	}
}
?>