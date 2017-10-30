<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name yungou.mod.php
 * @date 2015-11-12 16:33:06
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
		$this->CheckAdminPrivs('yungou');
		header('Location: ?mod=product&code=vlist');
	}
	
		public function vList()
	{
		$this->CheckAdminPrivs('yungou');
		
		logic('product')->Maintain();
                if('admin'== MEMBER_ROLE_TYPE  ){
            $smember = dbc(DBCMax)->query('select uid,area,city_place_region from '.table('members')." where uid='".MEMBER_ID."'")->limit(1)->done();
        }
		$yunGouArr = logic('yungou')->getYunGouList_withinPage($smember);		
		include handler('template')->file('@admin/product_yungou_list');
	}
	
		public function manager()
	{
		$this->CheckAdminPrivs('yungou');
		
		$pid = get('pid', 'int');												$product = logic('product')->GetOne($pid);							$zhongJiang = logic('yungou')->getZhongJiang($pid);		
		if (!empty($zhongJiang)) {
			$smsContent = user($zhongJiang['uid'])->get('name').'，您好，您在我们网站参与的活动有好结果了，恭喜您。详情请登录查看：'.ini('settings.site_url');
			$broadcastContent = '您好，您在我们网站参与的活动结果已出，赶快来看看吧！详情请登录：'.ini('settings.site_url');
		}
		
		include handler('template')->file('@admin/product_yungou_manager');
	}
	
		public function numberList()
	{
		$this->CheckAdminPrivs('yungou');
		
		$pid = get('pid', 'int');																$numberArr = logic('yungou')->getCanYuJiLu_withinPage($pid);							
		include handler('template')->file('@admin/product_yungou_number_list');
	}
	
		public function Ajax_getZhongJiangZhe() 
	{
		$this->CheckAdminPrivs('yungou','ajax');
		
		$pid = get('pid', 'int');																$number = get('number', 'int');												

		$canYu = logic('yungou')->getCanYuJiLu_byNumber($pid, $number);		
		if (empty($canYu))
		{
			echo('查询失败，找不到相关用户！<br/>失败原因：<br/>1，您输入的中奖号码不在可控范围内；<br/>2，您设置了虚拟购买人数，此中奖号码为虚拟占位号码；<br/>：：：不过，您仍然可以公开此中奖号码');
		}
		else
		{
			$user = user($canYu['uid']);
			echo '用户名：' . $user->get('name') . 
				 '<br/>手机：' . $user->get('phone') . 
				 '<br/>邮箱：' . $user->get('email') . 
				 '<br/>QQ：' . $user->get('qq') . '';
			echo '<br/>';
			echo '抽奖号备注：' . $canYu['remark'];
		}
		
		echo '<br/>';
		exit('<input type="button" onclick="submitZhongJiangNumber()" value="公开中奖号码" />');
 
	}
	
		public function Ajax_submitZhongJiangNumber()
	{
		$this->CheckAdminPrivs('yungou','ajax');
		
		$pid = get('pid', 'int');						
				$zhongJiangJiLu = logic('yungou')->getZhongJiangJiLu($pid);				if (!empty($zhongJiangJiLu))
		{
			exit('此抽奖活动已经公开过中奖号码，无法再次提交！');
		}
		
		$number = get('number', 'int');					
		$result = logic('yungou')->setZhongJiang($pid, $number);
		
		if ($result === true) {
						logic('product')->Update($pid, array('yungou' => YUNGOU_STA_Published));
			
						logic('order')->updateOrderUnderProduct($pid, array('process' => 'TRADE_FINISHED'));
			
						$zhongJiangJiLu = logic('yungou')->getZhongJiangJiLu($pid);
			$product = logic('product')->SrcOne($pid);
			if ($product['type'] == 'ticket') {
								logic('coupon')->Create($pid, $zhongJiangJiLu['orderid'], $zhongJiangJiLu['uid'], 1, false, false, false);
								logic('order')->Processed($zhongJiangJiLu['orderid'], 'TRADE_FINISHED');
								logic('order')->clog($zhongJiangJiLu['orderid'])->sys(TUANGOU_STR . '券已经生成：交易完成', 'TRADE_FINISHED');
			}
			else if ($product['type'] == 'stuff') {
								logic('order')->Processed($zhongJiangJiLu['orderid'], 'WAIT_SELLER_SEND_GOODS');
				
				logic('order')->clog($zhongJiangJiLu['orderid'])->sys('用户已经下单：等待发货', 'WAIT_SELLER_SEND_GOODS');
			}
			
		}
		
		exit($result === true ? 'ok' : $result);
	}
	
		function Ajax_notify()
	{
		$this->CheckAdminPrivs('yungou','ajax');
		
		$phone = get('phone', 'number');								$content = post('content', 'txt');							
		logic('push')->addi('sms', $phone, array('content'=>$content));
		
		exit('ok');
	}
	
		function Ajax_broadcast()
	{
		$this->CheckAdminPrivs('yungou','ajax');
		
		$content = post('content', 'txt');													$pid = get('pid', 'int');																	$excUid = get('excuid', 'int');															$phones = logic('yungou')->GetPhoneList($pid, $excUid);				
		logic('push')->add('sms', $phones, array('content'=>$content));
		
		exit('ok');
	}
}