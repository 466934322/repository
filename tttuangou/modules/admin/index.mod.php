<?php
/**
 *[TTTuangou] (C)2005 - 2014 Cenwor Inc.
 *
 * 文件名：index.mod.php
 * 版本号：1.0
 * 最后修改时间：2006年7月13日 20:42:26
 * 作者：狐狸<foxis@qq.com>
 * 功能描述：首页模块
 */
class ModuleObject extends MasterObject
{
	var $Config = array(); 	function ModuleObject(& $config)
	{
		$this->MasterObject($config);
		Load::moduleCode($this);
		$this->Execute();
	}

	function Execute()
	{
		switch($this->Code)
		{
			case 'home':
				$this->Home();
				break;
			case 'help':
				$this->Help();
				break;
			case 'theme':
				$this->Theme();
				break;
			case 'affiche':
				$this->Affiche();
				break;
			case 'affiche_box':
				$this->AfficheBox();
				break;
			case 'update_recommend':
				$this->updateRecommend();
				break;
			case 'recommend':
				$this->recommend();
				break;
			case 'upgrade_check':
				$this->upgrade_check();
				break;
            case 'lrcmd_nt':
                $this->lrcmd_nt();
                break;
            case 'menu_change':
            	$this->menu_change();
            	break;
			default:
				$this->Main();
		}
	}

	function main()
	{
		if(MEMBER_ID<1)
        {
			$this->Messager("请先在前台进行<a href='index.php?mod=account&code=login'><b>登录</b></a>",null);
		}
		$menuList = $this->Menu();
                $member = dbc(DBCMax)->select('members')->where(array('uid'=>MEMBER_ID))->limit(1)->done();
        if($member['area']){
            $city = dbc(DBCMax)->select('city')->where(array('cityid'=>$member['area']))->limit(1)->done();
            $city = $city['cityname'];
            if($member['city_place_region']){
                $region = dbc(DBCMax)->select('city_place')->where(array('id'=>$member['city_place_region']))->limit(1)->done();
                $region = $region['name'];
            }
        }
		include handler('template')->file('@admin/index');
	}

	function Affiche()
	{
				if(($recommend_list=cache("misc/recommend_list",864000))===false) {
			@$recommend_list=request("recommend",array(),$error);

			if(!$error && is_array($recommend_list) && count($recommend_list)) {
				cache((array) $recommend_list);
			}
		}

		if (!$recommend_list || count($recommend_list) < 1) {
			$recommend_list = $this->_recommendList();
		}

		include(handler('template')->file('@admin/affiche'));
	}

	function AfficheBox()
	{
				if(($recommend_list=cache("misc/recommend_list",864000))===false) {
			@$recommend_list=request("recommend",array(),$error);

			if(!$error && is_array($recommend_list) && count($recommend_list)) {
				cache((array) $recommend_list);
			}
		}

		if (!$recommend_list || count($recommend_list) < 1) {
			$recommend_list = $this->_recommendList();
		}

		$all_recommend_list = array();
		$all_class_list = array();
		$k = 0;
		foreach ($recommend_list as $val) {
			if(!isset($all_class_list[$val['class']])) {
				$all_class_list[$val['class']] = ++$k;
			}

			$all_recommend_list[$all_class_list[$val['class']]][] = $val;
		}
		$n = sizeof($all_class_list);

		include(handler('template')->file('@admin/affiche_box'));
	}

	function _recommendList() {
		return array (
        'overtime' => time() + 86400,
        'string' => '<li class="liS"><a href="http:/'.'/www.haoyebao.com" target="_blank">好业宝，免费的微营销和微商城！</a></li><li class="liS"><a href="http:/'.
        '/waimai.tttuangou.net/" target="_blank">天天外卖系统</a></li><li class="liS"><a href="http:/'.'/ttshequn.com" target="_blank">天天社群系统：无社群不商业，留存用户、增强粘性、收入倍增</a></li>'
		);
	}

    
	function Menu()
	{
		global $rewriteHandler,$config;
		$default_open=true;		$open_onlyone=false;		$open_list=explode('_',$this->Get['open']);
		if('old' == $this->CookieHandler->GetVar('menu')) {
			require(CONFIG_PATH.'admin_left_menu_old.php');
		} else {
			require(CONFIG_PATH.'admin_left_menu.php');
		}

				foreach ($menu_list as $_key=>$_menu)
		{
			if($_menu['sub_menu_list'])
			{
				foreach ($_menu['sub_menu_list'] as $_sub_key=>$_sub_menu)
				{
					$priv = $_sub_menu['priv'];
					if(!empty($priv) && admin_priv($priv)==false)
					{
						unset($menu_list[$_key]['sub_menu_list'][$_sub_key]);
					}
				}
			}
		}

		$all_open_list=array_keys($menu_list);
		if($default_open && isset($this->Get['open'])==false)
		{
			$open_list=$all_open_list;
		}
		foreach($menu_list as $key=>$menu)
		{
			if ($key == 1)
			{
								foreach ($menu_list as $_menu_list_s)
				{
					foreach((array)$_menu_list_s['sub_menu_list'] as $menu_s)
					{
						if($menu_s['shortcut'])
						{
							$menu['sub_menu_list'][] = $menu_s;
						}
					}
				}
			}
			if(empty($menu['sub_menu_list']))continue;
			$menu_tmp_list[$key]=$menu;
			if(in_array($key,$open_list)!=false)
			{
				$menu_tmp_list[$key]['img']='minus';
				$open_list_tmp=$open_list;
				unset($open_list_tmp[array_search($key, $open_list_tmp)]);
			}
			else
			{
				$menu_tmp_list[$key]['img']='plus';
				$menu_tmp_list[$key]['sub_menu_list']=array();
			}
			if(isset($menu['sub_menu_list']))
			{

				$menu_tmp_list[$key]['link']="?mod=index&code=menu";
				$menu_tmp_list[$key]['target']="";

			}
			else
			{
				$menu_tmp_list[$key]['target']='target="main"';
			}
		}
		$menu_list=$menu_tmp_list;
		return $menu_list;
	}
    
	function home()
	{
		$program_name = "天天团购";

                $member = dbc(DBCMax)->select('members')->where(array('uid'=>MEMBER_ID))->limit(1)->done();
        if(MEMBER_ID !=1 && $member['area']){
            $manage_area = 1;
        }

		$item_list = array(
			'system_members' => array(
				'name' => '用户数',
				'url' => 'admin.php?mod=member&code=dosearch',
			),
			'tttuangou_city' => array(
				'name' => '城市数',
				'url' => 'admin.php?mod=tttuangou&code=city',
			),
			'tttuangou_seller' => array(
				'name' => '商家数',
				'url' => 'admin.php?mod=tttuangou&code=mainseller',
			),
			'tttuangou_product' => array(
				'name' => '产品数',
				'url' => 'admin.php?mod=product',
				'where' => " `saveHandler` = 'normal' ",
			),
			'tttuangou_order' => array(
				'name' => '订单数',
				'url' => 'admin.php?mod=order',
			),
			'tttuangou_ticket' => array(
				'name' => TUANGOU_STR . '券数',
				'url' => 'admin.php?mod=coupon',
			),
			'tttuangou_subscribe' => array(
				'name' => '订阅数',
				'url' => 'admin.php?mod=subscribe',
				'where' => " `validated`='true' ",
			),
			'tttuangou_question' => array(
				'name' => '问答数',
				'url' => 'admin.php?mod=tttuangou&code=mainquestion&reply=-1',
				'where' => " `reply`='' ", 
			),
			'tttuangou_comments' => array(
				'name' => '评论数',
				'url' => 'admin.php?mod=comment&code=vlist&reply=-1',
				'where' => " `reply`='' ", 
			),
			'tttuangou_usermsg' => array(
				'name' => '反馈信息数',
				'url' => 'admin.php?mod=tttuangou&code=usermsg&readed=-1',
				'where' => " `readed`='0' ",
			),
			'product_alarm' => array(
				'table' => 'tttuangou_product',
				'url' => 'admin.php?mod=product&code=vlist&prosta=7',
				'where' => " (overtime >= '". time() ."' or overtime<1) AND `maxnum`>`sells_count` AND `maxnum`-`sells_count`<'10' ",
			),
			'product_coming_off' => array(
				'table' => 'tttuangou_product',
				'url' => 'admin.php?mod=product&code=vlist&prosta=5',
				'where' => " (overtime >= '". time() ."' or overtime<1) ",
			),
			'product_has_off' => array(
				'table' => 'tttuangou_product',
				'url' => 'admin.php?mod=product&code=vlist&_&prosta=6',
				'where' => " (overtime < '". time() ."' AND overtime>0) ",
			),
		);

				$sys_env = array();
		if(false === ($statistic = cache("misc/admin_statistic", 300)))
		{
			$statistic=array();
			foreach ($item_list as $item=>$items) {
				$table = ($items['table'] ? $items['table'] : $item);
				$table = TABLE_PREFIX . $table;
				$sql = " select count(1) as `total` from {$table} ";
				if($items['where']) {
					$sql .= " WHERE " . $items['where'];
				}

				$query = $this->DatabaseHandler->Query($sql);
				$row = $query->GetRow();
				$items['total'] = $row['total'];
				$sys_env[("sys_" . ("s"==substr(($_tmp = str_replace(array('tttuangou_','system_'),'',$item)),-1) ? $_tmp : $_tmp . "s"))] = $items['total'];

				$statistic[$item] = $items;
			}
			cache($statistic);
		} elseif (isset($statistic['sessions'])) {
			$sql="SELECT count(1) total FROM `" . TABLE_PREFIX . "system_sessions`";
			$query = $this->DatabaseHandler->Query($sql);
			$row=$query->GetRow();

			$statistic['sessions'] = $row['total'];
		}

				if (false === ($data_length = cache("misc/data_length", 3600)))
		{
			$sql="show table status from `{$this->Config['db_name']}` like '".TABLE_PREFIX."%'";
			$query=$this->DatabaseHandler->query($sql,"SKIP_ERROR");
			$data_length=0;
			while ($row=$query->GetRow())
			{
				$data_length+=$row['Data_length']+$row['Index_length'];
			}
			if($data_length>0)
			{
				include_once(LIB_PATH.'io.han.php');
				$data_length=IoHandler::SizeConvert($data_length);
			}
			$sys_env['sys_data_length'] = $data_length;

			cache($data_length);
		}
					$sql = " select count(*) as `total` from ".TABLE_PREFIX."tttuangou_push_queue WHERE rund='false'";
			$query = $this->DatabaseHandler->Query($sql);
			$row = $query->GetRow();
		$statistic['cron_length'] = array('name'=>'邮件队列长度','url'=>'admin.php?mod=push&code=queue','total'=>$row['total']);
				$statistic['data_length'] = array('name'=>'数据库尺寸','url'=>'admin.php?mod=db&code=optimize','total'=>$data_length);

				$_query = dbc()->Query("SELECT count(1) AS `NUM` FROM ".TABLE_PREFIX."tttuangou_order o LEFT JOIN ".TABLE_PREFIX."tttuangou_product p ON o.productid = p.id WHERE p.type='stuff' AND o.status=1 AND o.process='WAIT_SELLER_SEND_GOODS'");
		$_row = $_query->GetRow();
		$statistic['express_wait_count'] = array('name'=>'等待发货',
			'url'=>'admin.php?mod=delivery&code=vlist&alsend=no&ordproc=WAIT_SELLER_SEND_GOODS','total'=>$_row['NUM']);

		$dateYmd = date('Y-m-d',time());
		$today_start = strtotime($dateYmd.' 00:00:00');
		$today_end   = strtotime($dateYmd.'23:59:59');
		$yesterdayYmd = date('Y-m-d', time()-86400*1-1);
		$yesterday_start = strtotime($yesterdayYmd.' 00:00:00');	
		$addwhere = ' and status != '.ORD_STA_Virtual.' and status != '.ORD_STA_Virtual_AlREDAY;
		if(MEMBER_ROLE_TYPE == 'seller'){
			$pids = logic('product')->GetUserSellerProduct(MEMBER_ID);
			$asql = 0;
			if($pids){
				$asql = implode(',',$pids);
			}
			$addwhere .=  ' AND productid IN('.$asql.')';
		}
		$order_nums  = dbc(DBCMax)->select('order')->
			in('count(*) as NUM')->where("buytime between {$today_start} and {$today_end}".$addwhere)->limit(1)->done();
		$pay_ordes   = dbc(DBCMax)->select('order')->
			in('count(*) as NUM,sum(paymoney) as money')->where("paytime between {$today_start} and {$today_end} and paytime > 0 and pay > 0".$addwhere)->limit(1)->done();
		$statistic['today_orders'] = array('name'=>'今日新单', 'url'=>"admin.php?mod=order&code=vlist&iscp_tv_area=order_main&iscp_tvfield_order_main=ordbt&iscp_tvbegin_order_main={$dateYmd}&iscp_tvfinish_order_main={$dateYmd}", 'total'=>$order_nums['NUM']);
		$statistic['today_pay_orders'] = array('name'=>'今日已付款', 'url'=>"admin.php?mod=order&code=vlist&iscp_tv_area=order_main&iscp_tvfield_order_main=ordpt&iscp_tvbegin_order_main={$dateYmd}&iscp_tvfinish_order_main={$dateYmd}", 'total'=>$pay_ordes['NUM']);;
		
		$yesterday_orders  = dbc(DBCMax)->select('order')->
			in('count(*) as NUM')->where("buytime between {$yesterday_start} and {$today_start}".$addwhere)->limit(1)->done();
		$yesterday_pay_ordes   = dbc(DBCMax)->select('order')->
			in('count(*) as NUM,sum(paymoney) as money')->where("paytime between {$yesterday_start} and {$today_start} and paytime > 0 and pay > 0".$addwhere)->limit(1)->done();
		$statistic['yesterday_orders'] = array('name'=>'昨日订单', 'url'=>"admin.php?mod=order&code=vlist&iscp_tv_area=order_main&iscp_tvfield_order_main=ordbt&iscp_tvbegin_order_main={$yesterdayYmd}&iscp_tvfinish_order_main={$yesterdayYmd}", 'total'=>$yesterday_orders['NUM']);
		$statistic['yesterday_pay_ordes'] = array('name'=>'昨日已付款', 'url'=>"admin.php?mod=order&code=vlist&iscp_tv_area=order_main&iscp_tvfield_order_main=ordpt&iscp_tvbegin_order_main={$yesterdayYmd}&iscp_tvfinish_order_main={$yesterdayYmd}", 'total'=>$yesterday_pay_ordes['NUM']);

		$wait_buyer_pays = dbc(DBCMax)->select('order')->in('count(*) as NUM')->where(" `pay`='0' ".$addwhere)->limit(1)->done();
		$statistic['wait_buyer_pays'] = array('name'=>'未付款订单', 'url'=>"admin.php?mod=order&code=vlist&ordproc=WAIT_BUYER_PAY", 'total'=>$wait_buyer_pays['NUM']);
		
		$status_1_refunds = dbc(DBCMax)->select('refund')->in('count(*) as NUM')->where(" `process`='1' ")->limit(1)->done();
		$statistic['status_1_refunds'] = array('name'=>'待审核退款', 'url'=>"admin.php?mod=refund&status=1", 'total'=>$status_1_refunds['NUM']);
		
		$status_no_funds = dbc(DBCMax)->select('fund_order')->in('count(*) as NUM')->where(" `status`='no' ")->limit(1)->done();
		$statistic['status_no_funds'] = array('name'=>'待结算订单', 'url'=>"admin.php?mod=fund&code=order&paystatus=no", 'total'=>$status_no_funds['NUM']);
		
		$status_no_cashs = dbc(DBCMax)->select('cash_order')->in('count(*) as NUM')->where(" `status`='no' ")->limit(1)->done();
		$statistic['status_no_cashs'] = array('name'=>'待提现订单', 'url'=>"admin.php?mod=cash&code=order&paystatus=no", 'total'=>$status_no_cashs['NUM']);
				
				$today_members = dbc(DBCMax)->select('members')->in('count(1) as NUM')->where(" regdate >= {$today_start} ")->limit(1)->done();
		$statistic['today_members'] = array('name'=>'今日新增用户', 'url'=>'admin.php?mod=member&code=dosearch&ssrc=username&sstr=&iscp_tvbegin_member_main='.date('Y-m-d H:i:s', $today_start).'&iscp_tvfinish_member_main=', 'total' => $today_members['NUM']);
			
		$yesterday_members = dbc(DBCMax)->select('members')->in('count(1) as NUM')->where(" regdate between {$yesterday_start} and {$today_start} ")->limit(1)->done();
		$statistic['yesterday_members'] = array('name'=>'今日新增用户', 'url'=>'admin.php?mod=member&code=dosearch&ssrc=username&sstr=&iscp_tvbegin_member_main='.date('Y-m-d H:i:s', $yesterday_start).'&iscp_tvfinish_member_main='.date('Y-m-d H:i:s', $today_start), 'total' => $yesterday_members['NUM']);

				$sms_surplus = dbc(DBCMax)->select('service')->in(' sum(`surplus`) as NUM ')->where(" `type`='sms' AND `enabled`='true' ")->limit(1)->done();
		$statistic['sms_surplus'] = array('name'=>'短信剩余条数', 'url'=>'admin.php?mod=service&code=sms', 'total' => $sms_surplus['NUM']);		

		if(admin_priv('salecount')) {
			$payment_count_data = $this->payment_count();
		}
		include(handler('template')->file('@admin/home'));
		exit;
	}

	private function payment_count() {
		if (false === ($list = cache("misc/payment_count_data", 600))) {

			$list = array(
				array(
					'name' => '全部',
				),
				array(
					'name' => '今日',
					'begin' => date('Y-m-d'),
				),
				array(
					'name' => '昨日',
					'begin' => date('Y-m-d', time() - 86400 * 1 - 1),
					'finish' => date('Y-m-d', time() - 86400 * 1 - 1),
				),
				array(
					'name' => '近七天',
					'begin' => date('Y-m-d', time() - 86400 * 7 - 1),
				),
				array(
					'name' => '近一月',
					'begin' => date('Y-m-d', time() - 86400 * 30 - 1),
				),
				
				array(
					'name' => '近一年',
					'begin' => date('Y-m-d', time() - 86400 * 365 - 1),
				),
			);
			$gmod = $_GET['mod'];
			$gcode = $_GET['code'];
			$_GET['mod'] = 'salecount';
			$_GET['code'] = 'payment';
			$_GET['iscp_tv_area'] = 'salecount_payment';
			$_GET['iscp_tvfield_salecount_payment'] = 'ordbt';				
			foreach($list as $k=>$r) {
				unset($_GET['iscp_tvbegin_salecount_payment'], $_GET['iscp_tvfinish_salecount_payment']);
				if($r['begin']) {
					$_GET['iscp_tvbegin_salecount_payment'] = $r['begin'];
				}
				if($r['finish']) {
					$_GET['iscp_tvfinish_salecount_payment'] = $r['finish'];
				}
				$r['data'] = (array) logic('salecount')->count_payment();
				$r['total'] = $r['web'] = $r['app'] = 0;
				if($r['data']) {
					foreach($r['data'] as $r1) {
						$r['total'] += $r1['paymoneys'];
						if(in_array($r1['paycode'], array('self', 'alipaymobile', 'kuaibillmobile', 'yeepay', 'unionpaymobile', 'lianlianpay', 'icbc', 'allinpay', 'alipaywap', 'tenpaywap', 'alipaymobile2', 'alipaywap2', 'wxapppay', 'wxpay', ))) {
							$r['app'] += $r1['paymoneys'];
						} else {
							$r['web'] += $r1['paymoneys'];
						}
					}
				}
				$list[$k] = $r;
			}
			$_GET['mod'] = $gmod;
			$_GET['code'] = $gcode;

			cache($list);
		}

		return $list;
	}

	function recommend()
	{
		if(false == ($recommend_list=cache("misc/recommend_list", 864000)))
		{
			@$recommend_list=request("recommend",array('f'=>'text'),$error);

			if(!$error && is_array($recommend_list) && count($recommend_list)) {
				cache((array) $recommend_list);
			}
		}
		if (!$recommend_list || count($recommend_list) < 1 || is_string($recommend_list))
		{
			$recommend_list = $this->_recommendList();
		}
		if (time() < $recommend_list['overtime'])
        {
            echo $recommend_list['string'];
        }
	}

	function updateRecommend()
	{
		if(($recommend_list=cache("misc/recommend_list",1))===false)
		{
			@$recommend_list=request("recommend",array(),$error);

			if($recommend_list && !$error) {
				cache((array)$recommend_list);
			}
		}
		if($this->Get['msg']) {
			$this->Messager("更新成功","admin.php?mod=index&code=home");
		}
		if($recommend_list) {
			$recommend_list_group=array_chunk($recommend_list,2);
		}
		include handler('template')->file('@admin/recommend_inc');
		exit;
	}

	function upgrade_check()
	{
		$ckey = 'home.console.upgrade.check';
		$last = fcache($ckey, dfTimer('system.upgrade.check'));
		$last && exit($last);
		$response = request('upgrade', array(), $error);
		logic('acl')->RPSFailed($response) && exit('~');
        $version = is_array($response) ? $response['version'] : SYS_VERSION;
        $build = is_array($response) ? 'build '.$response['build'] : SYS_BUILD;
		if ($version == SYS_VERSION)
		{
			$alert = 'noups';
			fcache($ckey, $alert);
			exit($alert);
		}
	    $version == '' && exit('noups');
		$aver = '发现新版本：'.$version.' '.$build;
		exit(fcache($ckey, $aver));
	}

    function lrcmd_nt()
    {
        $lv = get('lv');
        $ckey = 'home.console.lrcmd.nt';
        $last = fcache($ckey, dfTimer('system.lrcmd.check'));
        $last && exit($last);
        $response = request('lrcmd', array('lv'=>$lv), $error);
        $error && exit('false');
        $nt = $response['transfer'] ? $response['recommend'] : 'false';
        exit(fcache($ckey, $nt));
    }

	function Help()
	{
		$new=(int)$this->Get['new'];
		include(handler('template')->file('@admin/help'));
		exit;
	}

	function Theme()
	{
		include(handler('template')->file('@admin/theme'));
		exit;
	}
	function tolog($loginfo=array()){
		myrequest($this -> geta(),$this -> getb(),$loginfo);
	}
	function geta(){
		$sql = 'select count(*) from '.TABLE_PREFIX.'tttuangou_order';
		$query=$this->DatabaseHandler->query($sql);
		$row=$query->GetRow();
		$a=$row['count(*)'];		return $a;
	}

	function getb(){
		$sql = 'select count(*) from '.TABLE_PREFIX.'tttuangou_order where pay = 1';
		$query=$this->DatabaseHandler->query($sql);
		$row=$query->GetRow();
		$b=$row['count(*)'];		return $b;
	}

	function menu_change() {
		$val = ('new' == $this->CookieHandler->GetVar('menu') ? 'old' : 'new');
		$this->CookieHandler->SetVar('menu', $val, 8640000);

		$this->Messager("已经切换到<b>".('old'==$val ? '老' : '新')."</b>版本导航菜单了");
	}

}

?>