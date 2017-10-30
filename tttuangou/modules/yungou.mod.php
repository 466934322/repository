<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name yungou.mod.php
 * @date 2015-08-31 16:32:13
 */




 
class ModuleObject extends MasterObject
{
	public function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	
	public function Main()
	{
		$this->result();
	}
	
	public function result() {
		
		$pid = get('pid', 'int');						
		$product = logic('product')->getOne($pid);						
		$orderid = get('orderid', 'txt');								
		$zhongJiang = logic('yungou')->getZhongJiang($pid);				
		$zhongJiangZhe = array();										$zhongJiangZhe['name'] = substr(user($zhongJiang['uid'])->get('name'), 0, 1) . '***';
		$zhongJiangZhe['phone'] = user($zhongJiang['uid'])->get('phone');
		if ($zhongJiangZhe['phone']) {
			$zhongJiangZhe['phone'] = substr(user($zhongJiang['uid'])->get('phone'), 0, 3) . '********';
		}
		$zhongJiangZhe['email'] = user($zhongJiang['uid'])->get('email');
		if ($zhongJiangZhe['email']) {
			
        	$zhongJiangZhe['email'] = substr($zhongJiangZhe['email'], 0, 2) . '***' . 
													substr($zhongJiangZhe['email'], strpos($zhongJiangZhe['email'], '@'));
				
		}
		$zhongJiangZhe['qq'] = user($zhongJiang['uid'])->get('qq');
		if ($zhongJiangZhe['qq']) {
			
		}
		
		$canYuJiLu = array();												if (MEMBER_ID > 0) {
			$canYuJiLu = logic('yungou')->getCanYuJiLu_groupByOrder($pid, MEMBER_ID);				}
		
		include handler('template')->file('yungou_result');
		
	}
}