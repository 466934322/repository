<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name fund.mod.php
 * @date 2016-07-25 18:45:53
 */





class ModuleObject extends MasterObject
{
    public function ModuleObject( $config )
    {
        $this->MasterObject($config);
        $runCode = Load::moduleCode($this);
        $this->$runCode();
    }

	function main()
	{
		$this->CheckAdminPrivs('fundorder');
		$sid = get('sid', 'int');
		$seller_info = logic('seller')->GetOne($sid);
		if(!$seller_info){
			$this->Messager('错误的操作，系统不支持！', -1);
		}
		$bank = ini('bank');
		$upcfg = ini('recharge');
		$payaddress = $upcfg['payaddress'] ? $upcfg['payaddress'] : '请电话联系商家确认后再进行操作，否则钱财两空';
		$max_money = $seller_info['account_money'] ? $seller_info['account_money'] : 0;
		include handler('template')->file('@admin/fund');
	}

	function order_done()
	{
		$sid = post('sid','int');
		$seller_info = logic('seller')->GetOne($sid);
		if(!$seller_info){
			$this->Messager('错误的操作，系统不支持！', -1);
		}
		$bank = ini('bank');
		$money = round((float)post('money'), 2);
		if (!$money || $money <= 0)
		{
			$this->Messager('结算金额无效,必须是一个有效数字！', -1);
		}
		$account_money = $seller_info['account_money'];
		if ($money > $account_money)
		{
			$this->Messager('结算金额过大，您的帐户最大可结算金额为'.$account_money.'元！', -1);
		}
		$paytype = post('paytype','txt');
		if (!in_array($paytype,array('alipay','money','bank')))
		{
			$this->Messager('您必须选择一种结算方式！', -1);
		}
		$alipay = post('alipaynumber','txt');
		$bankname = post('bankname','txt');
		$bankcard = post('banknumber','number');
		$bankusername = post('bankusername','txt');
		$aliusername = post('aliusername','txt');
		if($paytype == 'alipay'){
			if(empty($alipay)){
				$this->Messager('请输入您的支付宝帐号！', -1);
			}elseif(strlen($alipay) < 6){
				$this->Messager('您的支付宝帐号填写错误！', -1);
			}elseif(empty($aliusername)){
				$this->Messager('请输入收款人姓名！', -1);
			}elseif(strlen($aliusername) < 4){
				$this->Messager('收款人姓名填写错误！', -1);
			}
			$bankusername = $aliusername;
		}elseif($paytype == 'bank'){
			if(empty($bankname)){
				$this->Messager('请选择一个转帐银行！', -1);
			}elseif(!in_array($bankname,array_keys($bank))){
				$this->Messager('转帐银行错误！', -1);
			}elseif(empty($bankcard)){
				$this->Messager('请输入银行卡号！', -1);
			}elseif(strlen($bankcard) < 8 || !is_numeric($bankcard)){
				$this->Messager('您的银行卡号填写错误！', -1);
			}elseif(empty($bankusername)){
				$this->Messager('请输入开户人姓名！', -1);
			}elseif(strlen($bankusername) < 4){
				$this->Messager('开户人姓名填写错误！', -1);
			}
			if('~other~' == $bankname) {
				$other_bank_name = post('other_bank_name', 'txt');
				if(strlen($other_bank_name) < 8) {
					$this->Messager('其他银行的银行名称填写错误！');
				}
				$bank[$bankname] = $other_bank_name;
			}
		}
		$data = array(
			'money' => $money,
			'paytype' => $paytype,
			'alipay' => $alipay,
			'bankname' => $bank[$bankname],
			'bankcard' => $bankcard,
			'bankusername' => $bankusername,
			'sellerid' => $seller_info['id'],
			'from' => 'admin'
		);
		$orderid = logic('fund')->GetFree($seller_info['userid'],$data);
		if($orderid){
			logic('fund')->MakeSuccessed($orderid);
			$return = logic('fund')->Orderdone($orderid,'yes','');
		}
		if($orderid && $return){
			$this->Messager('操作成功！');
		}else{
			$this->Messager('操作失败！');
		}
	}

    function order()
    {
        $this->CheckAdminPrivs('fundorder');
		$orderid = get('orderid', 'number');
		if($orderid)
		{
			$order = logic('fund')->GetOne($orderid);
			if($order){
				if($order['status'] =='doing'){
					$action = "?mod=fund&code=order&op=save";
				}
				$order_log = logic('fund')->Getlog($orderid);
			}else{
				$this->Messager('操作错误！');
			}
		}
		else
		{
			$paystatus = get('paystatus');
			if (in_array($paystatus,array('no','yes','doing','error')))
			{
				$where = "status = '{$paystatus}'";
			}
			else
			{
				$where = '1';
			}
                        if(MEMBER_ROLE_TYPE == 'seller'){
                $where .= ' AND userid ='.MEMBER_ID;
            }
            if('admin'== MEMBER_ROLE_TYPE  ){
                $smember = dbc(DBCMax)->query('select uid,area,city_place_region from '.table('members')." where uid='".MEMBER_ID."'")->limit(1)->done();
                                if($smember['area']){
                    if($smember['city_place_region']){
                        $sids = logic('seller')->area_seller($smember['area'],$smember['city_place_region']);
                        $where .= ' AND sellerid IN('.$sids.')';
                    }else{
                        $sids = logic('seller')->area_seller($smember['area']);
                        $where .= ' AND sellerid IN('.$sids.')';
                    }
                }
            }
			$list = logic('fund')->GetList($where);
		}
        include handler('template')->file('@admin/fund_order');
    }
	public function order_save(){
		$this->CheckAdminPrivs('fundorder');
		$orderid = post('orderid', 'number');
		$status = post('status', 'txt');
		$info = strip_tags(post('info', 'txt'));
		if(!in_array($status,array('yes','error'))){
			$this->Messager('操作失败，请选择一个操作结果！');
		}
		$return = logic('fund')->Orderdone($orderid,$status,$info);
		if($return){
			$this->Messager('操作成功！');
		}else{
			$this->Messager('操作失败！');
		}
	}
	
    public function order_confirm()
    {
        $this->CheckAdminPrivs('fundorder','ajax');
		$orderid = get('orderid', 'number');
        if ($orderid)
        {
            $r = logic('fund')->MakeSuccessed($orderid);
            if(false === X_IS_AJAX) {
            	$this->Messager(null, 'admin.php?mod=fund&code=order&orderid=' . $orderid);
            }
			if($r){exit('ok');}else{exit($r);}
        }
        else
        {
            exit('结算记录流水号不正确');
        }
    }
	function Config()
    {
        $this->CheckAdminPrivs('fundset');
		$upcfg = ini('fund');
        include handler('template')->file('@admin/fund_config');
    }
    function Config_save()
    {
        $this->CheckAdminPrivs('fundset');
		$least = post('least', 'int');
		$per = post('per', 'int');
		$least = intval(max(0,$least));
		$per = intval(max(0,$per));
		$least = $least < $per ? $per : $least;
		$upcfg = array(
			'least' => $least,
            'per' => $per
        );
        ini('fund', $upcfg);
        $this->Messager('保存成功！');
    }
	function money_save()
	{
		$this->CheckAdminPrivs('fundorder','ajax');
		$id = get('id','int');
		$money = get('money','float');
		$moneyz = get('moneyz','float');
		if(!is_numeric($_GET['moneyz']) || $moneyz < 0 || !is_numeric($_GET['money']) || $money < 0){
			exit('金额输入错误，修改失败！');
		}
		$data = array('account_money' => $money,'total_money' => $moneyz);
		dbc()->SetTable(table('seller'));
		$r = dbc()->Update($data,'id='.intval($id));
		exit($r ? (string)$r : '修改失败！');
	}
	function iphonesave()
    {
		$this->CheckAdminPrivs('appmanage');
		$url = post('url', 'txt');
		$from = post('from', 'txt');
		$from = $from == 'app' ? '?mod=app' : '?mod=api&code=release';
		if($url && false === strpos($url,'https://itunes.apple.com/cn/')){
			$this->Messager('下载地址填写错误！',$from);
		}
		$cfg = array(
			'url' => $url
        );
        ini('iphone', $cfg);
        $this->Messager('保存成功！',$from);
    }
	function moneyupdate()
	{
		$this->CheckAdminPrivs('fundorder');
		$this->CheckAdminPrivs('seller');
		$length = 10;
		$offset = max(0, get('offset', 'int'));
		$sql = "select `id` from ".table('seller')." order by `id` asc limit {$offset}, {$length}";
	 	$sids = dbc(DBCMax)->query($sql)->done();
		if($sids){
			foreach($sids as $sid){
				$sql = dbc(DBCMax)->select('product')->in('id')->where(array('sellerid'=>$sid['id'],'saveHandler'=>'normal'))->order('id.asc')->sql();
				$pids = dbc(DBCMax)->query($sql)->done();
				if($pids){
					$c_pid = array();
					foreach($pids as $pid){
						$c_pid[] = $pid['id'];
					}
					$sql = dbc(DBCMax)->select('order')->in('SUM(totalprice-expressprice) AS money,count(*) AS ordernum')->where('paytime > 0 AND productid IN('.implode(',',$c_pid).')')->sql();
					$orders = dbc(DBCMax)->query($sql)->done();
					$money = $orders[0]['money'];
					$successnum = $orders[0]['ordernum'];
					$productnum = count($pids);
				}else{
					$money = $successnum = $productnum = 0;
				}
				dbc(DBCMax)->update('seller')->data(array('money'=>$money,'successnum'=>$successnum,'productnum'=>$productnum))->where(array('id' => $sid['id']))->done();
			}
			$this->Messager('【'.$offset.'】正在更新，请不要关闭窗口','?mod=fund&code=moneyupdate&offset='.($offset + $length));
		} else {
			$this->Messager('更新完成了，请稍后...','?mod=tttuangou&code=mainseller');
		}
	}

	private function __create_moneyfixtb() {
		$sql = "CREATE TABLE IF NOT EXISTS ".table("rebate_log_fix")." (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `home_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `uname` varchar(45) NOT NULL DEFAULT '',
  `deal_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `fund_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `salary_pre` decimal(6,2) unsigned NOT NULL DEFAULT '0.00',
  `salary_money` decimal(11,3) unsigned NOT NULL DEFAULT '0.000',
  `orderid` bigint(11) unsigned NOT NULL DEFAULT '0',
  `ticketid` int(10) unsigned NOT NULL DEFAULT '0',
  `type` enum('master','sell','buy') NOT NULL DEFAULT 'master',
  `addtime` char(10) NOT NULL DEFAULT '0',
  `rec_all` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `home_uid` (`home_uid`),
  KEY `type` (`type`),
  KEY `addtime` (`addtime`),
  KEY `uid` (`uid`),
  KEY `ticketid` (`ticketid`),
  KEY `orderid` (`orderid`)
) ENGINE=MyISAM CHARSET=".('gbk'==strtolower(ini('settings.charset'))?'gbk':'utf8');
			dbc()->Query($sql);
			handler('cookie')->SetVar('moneyfixtb', 'ok', 86400000);
	}
	private function __find_repeat() {
		$ids = array();
		$rows = dbc()->Query("SELECT * FROM ".table("rebate_log")." GROUP BY ticketid HAVING COUNT(ticketid)>1")->GetAll();
		if($rows) {
			foreach($rows as $row) {
				$row6 = dbc()->Query("SELECT `id` FROM ".table("rebate_log")." WHERE `uid`='{$row['uid']}' AND `orderid`='{$row['orderid']}' AND `deal_money`='{$row['deal_money']}' AND `ticketid`='{$row['ticketid']}' AND `type`='{$row['type']}' AND `id`!='{$row['id']}'")->GetAll();
				if($row6) {
					foreach($row6 as $_row) {
						if($_row['id'] != $row['id']) {
							$ids[] = $_row['id'];
						}
					}
				}
			}
		}
		return $ids;
	}
	private function __copy_log($fix_ids) {
		if($fix_ids) {
			$sql = "INSERT INTO ".table("rebate_log_fix")." (`id`, `home_uid`, `uid`, `uname`, `deal_money`, `fund_money`, `salary_pre`, `salary_money`, `orderid`, `ticketid`, `type`, `addtime`, `rec_all`)
				SELECT `id`, `home_uid`, `uid`, `uname`, `deal_money`, `fund_money`, `salary_pre`, `salary_money`, `orderid`, `ticketid`, `type`, `addtime`, `rec_all`
				FROM ".table("rebate_log")." WHERE `id` IN ('".implode("','", $fix_ids)."') ORDER BY id ASC";
			dbc()->Query($sql);
		}
	}
	function moneyfixupdate() {
		$this->CheckAdminPrivs('fundorder');
		$this->CheckAdminPrivs('seller');
		if(1 !== MEMBER_ID) {
			$this->Messager('您没有权限访问此页面', null);
		}

		$num = 1000;
		$page = max(0, (int) get('page'));
		$last_id = max(0, get('last_id', 'int'));
		$last_time = max(0, get('last_time', 'int'));
		$fix_ids = array();

		if($page < 1) {
			dbc()->Query("DROP TABLE IF EXISTS ".table("rebate_log_fix"));
			$this->__create_moneyfixtb();
			dbc()->Query("DELETE FROM ".table("rebate_log_fix")." WHERE 1");

			$fix_ids = $this->__find_repeat();
		}

		$sql = "SELECT r.id,r.addtime,r.ticketid,t.usetime FROM ".table("rebate_log")." r LEFT JOIN ".table("ticket")." t ON t.ticketid = r.ticketid WHERE 1 ORDER BY r.`id` ASC LIMIT ".max(0, $page * $num).",{$num}";
		$rows = dbc()->Query($sql)->GetAll();

		if(false == $rows) {
			$this->Messager('可疑记录查找完毕！', "admin.php?mod=fund&code=moneyfix");
		}

		foreach($rows as $row) {
			$fix = 0;
			if($row['id'] > $last_id && $row['addtime'] < $last_time) {
				$fix = 1;
			}
			if(!$fix && $row['ticketid'] > 0 && !is_null($row['usetime'])) {
				if(($row['addtime'] - strtotime($row['usetime'])) > 60) {
					$fix = 1;
				}
			}

			if($fix) {
				$fix_ids[] = $row['id'];
			} else {
				$last_id = $row['id'];
				$last_time = $row['addtime'];
			}
		}

		$this->__copy_log($fix_ids);

		$npage = $page+1;
		$this->Messager('【'.$npage.' '.count($fix_ids).'】正在查找可疑记录，请稍候。。。', "admin.php?mod=fund&code=moneyfixupdate&page=".($npage)."&last_id=$last_id&last_time=$last_time", 0);
	}

	function moneyfix() {
		$this->CheckAdminPrivs('fundorder');
		$this->CheckAdminPrivs('seller');
		if(1 !== MEMBER_ID) {
			$this->Messager('您没有权限访问此页面', null);
		}

		if('ok' != handler('cookie')->GetVar('moneyfixtb')) {
			$this->__create_moneyfixtb();
		}

		if(get('fix')) {
			if(false == ($ids = post('ids'))) {
				$this->Messager("请先指定要删除的可疑记录ID");
			}
			$fix_datas = dbc()->Query("SELECT * FROM ".table("rebate_log_fix")." WHERE `id` IN('".implode("','", $ids)."')")->GetAll();
			if($fix_datas) {
				$this->__do_moneyfix($fix_datas, true);
				$this->Messager("您指定要删除的可疑记录已经删除成功");
			} else {
				$this->Messager("您指定要删除的可疑记录已经不存在了");
			}
		}

		$where = '';
		$url = 'admin.php?mod=fund&code=moneyfix';
		$day = get('day', 'int');
		if($day) {
			if(-1 == $day) {
				$time_begin_txt = date('Y-m-d 00:00:00', time());
				$time_begin = strtotime($time_begin_txt);
			} elseif (1 == $day) {
				$time_begin_txt = date('Y-m-d 00:00:00', time()-86399);
				$time_begin = strtotime($time_begin_txt);
				$time_end_txt = date('Y-m-d 23:59:59', time()-86399);
				$time_end = strtotime($time_end_txt);
			} elseif (7 == $day) {
				$time_begin_txt = date('Y-m-d 00:00:00', time()-86400*7-1);
				$time_begin = strtotime($time_begin_txt);
			}
		} else {
			$time_begin_txt = $time_begin = get('time_begin', 'txt');
			$time_end_txt = $time_end = get('time_end', 'txt');
			if($time_begin) {
				$time_begin = strtotime($time_begin);
			}
			if($time_end) {
				$time_end = strtotime($time_end);
			}
		}
		if($time_begin) {
			$where .= " AND r.`addtime`>='{$time_begin}' ";
		}
		if($time_end) {
			$where .= " AND r.`addtime`<='{$time_end}' ";
		}
		$kvar = get('kvar', 'txt');
		if(in_array($kvar, array('uname', 'orderid', 'ticketid'))) {
			$kval = trim(strip_tags(get('kval', 'txt')));
			if($kval) {
				$where .= " AND (r.`{$kvar}`='{$kval}' OR r.`{$kvar}` LIKE '%{$kval}%') ";
			}
		}

		page_moyo_max_io(500);

		$sql = "SELECT r.*,t.number,t.mutis,t.usetime,p.flag FROM ". table('rebate_log_fix') ." r
				LEFT JOIN ". table('ticket') ." t ON r.ticketid = t.ticketid
				LEFT JOIN ". table('order') ." o ON r.orderid = o.orderid
				LEFT JOIN ". table('product') ." p ON o.productid = p.id
			WHERE 1 ". $where ." ORDER BY r.id DESC";
		$sql = page_moyo($sql);
		if(false != ($list = dbc(DBCMax)->query($sql)->done())){
			foreach($list as $key => $val){
				if(($val['rec_all'] && $val['fund_money'] >= 0) || (!$val['rec_all'] && $val['fund_money'] > 0)){
					$list[$key]['salary_money'] = $val['fund_money'];
					$list[$key]['salary_pre'] = '按结算价';
				}else{
					$list[$key]['salary_money'] = round(($val['deal_money'] - $val['salary_money']),2);
					$list[$key]['salary_pre'] = $val['salary_pre'].'%';
				}
			}
		}
		include handler('template')->file('@admin/fund_money');
	}

	private function __do_moneyfix($fix_datas = array(), $update = false) {
		$this->CheckAdminPrivs('fundorder');
		$this->CheckAdminPrivs('seller');
		if(1 !== MEMBER_ID) {
			$this->Messager('您没有权限访问此页面', null);
		}

		if(!$fix_datas) return false;
		foreach($fix_datas as $row) {
			if('master' == $row['type']) {
				if($row['fundmoney'] > 0){
					$salary_money = $row['fundmoney'];
				}elseif($row['salary_pre'] > 0){
					$salary_money = $row['deal_money'] - $row['salary_money'];
				}else{
					$salary_money = $row['deal_money'];
				}
				$update && dbc(DBCMax)->update('seller')->data('account_money=account_money-'. $salary_money .',total_money=total_money-'. $salary_money)->where('userid='.$row['uid'])->done();
			} elseif ('buy' == $row['type']) {
				$update && dbc(DBCMax)->update('members')->data('salary_number=salary_number-'.$row['deal_money'])->where('uid='.(int)$row['home_uid'])->done();
				$row4 = dbc()->query("SELECT * FROM " . table('usermoney') . " WHERE `userid`='{$row['home_uid']}' AND `type`='plus' AND `money`='{$row['salary_money']}' AND `intro` LIKE '%".date('Y-m-d H:i:s', $row['addtime'])."%{$row['orderid']}%'")->GetAll();
				foreach($row4 as $_row) {
					$update && dbc()->query('UPDATE ' . table('members').' SET money = money - ' . $_row['money'] . ' WHERE uid = ' . $_row['userid']);
					$update && dbc()->query('delete from ' . table('usermoney') . ' where `id`='.$_row['id']);
				}
				unset($row4);
			} elseif ('sell' == $row['type']) {
				$row5 = dbc()->query("SELECT * FROM " . table('usermoney') . " WHERE `userid`='{$row['home_uid']}' AND `type`='plus' AND `money`='{$row['salary_money']}' AND `intro` LIKE '%".date('Y-m-d H:i:s', $row['addtime'])."%{$row['orderid']}%'")->GetAll();
				foreach($row5 as $_row) {
					$update && dbc()->query('UPDATE ' . table('members').' SET money = money - ' . $_row['money'] . ' WHERE uid = ' . $_row['userid']);
					$update && dbc()->query('delete from ' . table('usermoney') . ' where `id`='.$_row['id']);
				}
				unset($row5);
			}
			$update && dbc()->query('delete from ' . table('rebate_log') . ' where `id`='.$row['id']);
			$update && dbc()->query('delete from ' . table('rebate_log_fix') . ' where `id`='.$row['id']);
		}
		unset($fix_datas);

		return $update;
	}

	function money() {
		$this->CheckAdminPrivs('fundorder');
		$this->CheckAdminPrivs('seller');

		$where = '';
		$url = 'admin.php?mod=fund&code=money';
		$day = get('day', 'int');
		if($day) {
			if(-1 == $day) {
				$time_begin_txt = date('Y-m-d 00:00:00', time());
				$time_begin = strtotime($time_begin_txt);
			} elseif (1 == $day) {
				$time_begin_txt = date('Y-m-d 00:00:00', time()-86399);
				$time_begin = strtotime($time_begin_txt);
				$time_end_txt = date('Y-m-d 23:59:59', time()-86399);
				$time_end = strtotime($time_end_txt);
			} elseif (7 == $day) {
				$time_begin_txt = date('Y-m-d 00:00:00', time()-86400*7-1);
				$time_begin = strtotime($time_begin_txt);
			}
		} else {
			$time_begin_txt = $time_begin = get('time_begin', 'txt');
			$time_end_txt = $time_end = get('time_end', 'txt');
			if($time_begin) {
				$time_begin = strtotime($time_begin);
			}
			if($time_end) {
				$time_end = strtotime($time_end);
			}
		}
		if($time_begin) {
			$where .= " AND r.`addtime`>='{$time_begin}' ";
		}
		if($time_end) {
			$where .= " AND r.`addtime`<='{$time_end}' ";
		}

		$kvar = get('kvar', 'txt');
		if(in_array($kvar, array('uname', 'orderid', 'ticketid'))) {
			$kval = trim(strip_tags(get('kval', 'txt')));
			if($kval) {
                if($kvar == 'ticketid'){
                                        $where .= " AND (t.`number`='{$kval}' OR t.`number` LIKE '%{$kval}%') ";
                }else{
                    $where .= " AND (r.`{$kvar}`='{$kval}' OR r.`{$kvar}` LIKE '%{$kval}%') ";
                }
			}
		}

        if('admin'== MEMBER_ROLE_TYPE  ){
            $smember = dbc(DBCMax)->query('select uid,area,city_place_region from '.table('members')." where uid='".MEMBER_ID."'")->limit(1)->done();
                        if($smember['area']){
                if($smember['city_place_region']){
                    $where .=  " AND p.`city`='{$smember['area']}' AND p.`city_place_region`='{$smember['city_place_region']}' ";
                }else{
                    $where .= " AND p.`city`='{$smember['area']}' ";
                }
            }
        }

                if(MEMBER_ROLE_TYPE == 'seller'){
            $list = logic('rebate')->get_rebate_list($where,MEMBER_ID);
        }else{
            $list = logic('rebate')->get_rebate_list($where);
        }

		if($list){
			foreach($list as $key => $val){
				if(($val['rec_all'] && $val['fund_money'] >= 0) || (!$val['rec_all'] && $val['fund_money'] > 0)){
					$list[$key]['salary_money'] = $val['fund_money'];
					$list[$key]['salary_pre'] = '按结算价';
                    if($val['type'] == 'pay_bill'){
                        $list[$key]['salary_pre'] = $val['salary_pre'].'%';
                    }
				}else{
					$list[$key]['salary_money'] = round(($val['deal_money'] - $val['salary_money']),2);
					$list[$key]['salary_pre'] = $val['salary_pre'].'%';
				}
			}
		}
		include handler('template')->file('@admin/fund_money');
	}

    
    public function update_fund(){
        $time_begin = strtotime("2015-08-20 00:00:00");
        $where = " where r.`addtime`>='{$time_begin}' ";
        $where .= " AND t.`usetime`='0000-00-00 00:00:00'";
        $sql = "SELECT r.*,t.usetime FROM ". table('rebate_log_fix') ." r
				LEFT JOIN ". table('ticket') ." t ON r.ticketid = t.ticketid";
        $sql .= $where;
        $list = dbc(DBCMax)->query($sql)->done();
        foreach($list as $val){
            $addtime = date('Y-m-d H:i:s',$val['addtime']);
            $sqlu = "UPDATE ". table('ticket') ." SET `usetime`='".$addtime."'
			WHERE ticketid =". $val['ticketid'];
            dbc(DBCMax)->query($sqlu)->done();
        }
    }
    
    public function update_fund_status(){
        $where = " where status=0 ";
        $where .= " AND `usetime`!='0000-00-00 00:00:00'";
                $sql = "UPDATE ". table('ticket') ." SET `status`=1";
        $sql .= $where;
        $rs = dbc(DBCMax)->query($sql)->done();
        if($rs){
            echo 'Success!';
        }
    }
}
?>