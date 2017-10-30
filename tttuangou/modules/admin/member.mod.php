<?php
/**
 * 文件名：member.mod.php
 * 版本号：1.0
 * 最后修改时间：2006年8月16日 1:59:36
 * 作者：狐狸<foxis@qq.com>
 * 功能描述：用户管理模块
 */

class ModuleObject extends MasterObject
{


	
	var $Code = array();


	
	var $ID = 0;

	
	var $IDS;

	
	function ModuleObject($config)
	{
		$this->MasterObject($config);

		if(isset($this->Get['code']))
		{
			$this->Code = $this->Get['code'];
		}elseif(isset($this->Post['code']))
		{
			$this->Code = $this->Post['code'];
		}

		if(isset($this->Get['id']))
		{
			$this->ID = (int)$this->Get['id'];
		}elseif(isset($this->Post['id']))
		{
			$this->ID = (int)$this->Post['id'];
		}

		if(isset($this->Get['ids']))
		{
			$this->IDS = $this->Get['ids'];
		}elseif(isset($this->Post['ids']))
		{
			$this->IDS = $this->Post['ids'];
		}

		$this->FormHandler = new FormHandler();

		Load::moduleCode($this);$this->Execute();
	}

	
	function Execute()
	{
		switch($this->Code)
		{
			case 'list':
			$this->Main();
			break;

			case 'add':
			$this->Add();
			break;

			case 'doadd':
			$this->DoAdd();
			break;

			case 'delete':
			case 'dodelete':
			$this->DoDelete();
			break;

			case 'search':
			$this->search();
			break;
			case 'dosearch':
			$this->DoSearch();
			break;

			case 'modify':
			$this->Modify();
			break;
			case 'domodify':
			$this->DoModify();
			break;

			case 'moneylog':
			$this->moneylog();
			break;

			default:
			$this->Main();
			break;
		}
	}

	
	function Main()
	{
		$this->CheckAdminPrivs('memberedite');
		$this->DoSearch();
	}

	
	function Add()
	{
		$this->CheckAdminPrivs('memberadd');
		
        $title = "添加";
        $city = logic('misc')->CityList();
		$action = "admin.php?mod=member&code=doadd";
                $city_arr = dbc(DBCMax)->select('city')->where(array('display'=>1))->done();
        if($_GET['area_id']){
            $area_id = $_GET['area_id'];
            $places = logic('city')->get_places($area_id);
        }
		include handler('template')->file('@admin/member_add');
	}

	
	function DoAdd()
	{
        		$this->CheckAdminPrivs('memberadd');
		$data = array();
		$data['username'] = trim($this->Post['username']);
		$data['password'] = md5(trim($this->Post['password']));
		$data['secques'] = random(24);
		$data['email'] = trim($this->Post['email']);
		$data['role_type'] = in_array($this->Post['role_type'],array('normal','admin')) ? $this->Post['role_type'] : 'normal';
		$data['role_id'] = 0;
		$data['privs'] = '';
		$data['regdate'] = time();
        $data['area'] = $this->Post['manage_area'];
        $data['city_place_region'] = $this->Post['__cplace_region'];
		if ($data['username']=='' or $data['password']=='')
		{
			$this->Messager("用户名或密码不能为空");
		}
		$this->DatabaseHandler->SetTable(TABLE_PREFIX.'system_members');
		$is_exists = $this->DatabaseHandler->Select('', "username='{$data['username']}'");

		if($is_exists != false)
		{
			$this->Messager("用户名　{$data['username']}　已经被注册");
		}
		$result = $this->DatabaseHandler->Insert($data);
		if($result != false)
		{
						account()->Validated($result);
			$this->Messager("添加成功", 'admin.php?mod=member');
		}
		else
		{
			$this->Messager("添加失败");
		}
	}

	
	function Search()
	{
		$this->CheckAdminPrivs('memberedite');
		$action = "admin.php?mod=member&code=dosearch";
		
		$role_type = array(
			'normal' => array('name' => '普通用户', 'value' => 'normal'),
			'seller' => array('name' => '合作商家', 'value' => 'seller'),
			'admin' => array('name' => '管理员', 'value' => 'admin')
		);
		$role_count = count($role_list) + 1;
		include handler('template')->file('@admin/member_search');
	}

	
	function DoSearch()
	{
		$this->CheckAdminPrivs('memberedite');
		extract($this->Get);
		$where_list = array();
		if(in_array($ssrc,array('username','phone','email')) && $sstr != ''){
						ENC_IS_GBK && $sstr = get_safe_val($sstr);
			$_GET['iscp_input_value'] = $sstr;
			$where_list[$ssrc] = $ssrc." like '%{$sstr}%'";
		}
		if(in_array($ssrc, array('uid')) && $sstr != '') {
			$where_list[$ssrc] = $ssrc." = '" . (int) $sstr . "'";
		}
		if ($lower_money != '')
		{
			$lower_money = is_numeric($lower_money) ? $lower_money : 0;
			$where_list['money_lower'] = "money <= {$lower_money}";
		}
		if ($higher_money != '')
		{
			$higher_money = is_numeric($higher_money) ? $higher_money : 0;
			$where_list['money_higher'] = "money >= {$higher_money}";
		}
		if ($totalpay != '')
		{
			$totalpay = is_numeric($totalpay) ? $totalpay : 0;
			$where_list['totalpay'] = "totalpay >= {$totalpay}";
		}
		if($regip != '')
		{
			$where_list['regip'] = "regip like '{$regip}%'";
		}
		if($lastip != '')
		{
			$where_list['lastip'] = "lastip like '%{$lastip}'";
		}
		if($iscp_tvbegin_member_main != '')
		{
			$where_list['begintime'] = "regdate >".strtotime($iscp_tvbegin_member_main);
		}
		if($iscp_tvfinish_member_main != '')
		{
			$where_list['overtime'] = "regdate <".strtotime($iscp_tvfinish_member_main);
		}
		if(is_string($role_types)==false)
		{
			if($role_type[0] != 'all' and is_array($role_type) and count($role_type) > 0)
			{
				$where_list['role_type'] = $this->DatabaseHandler->BuildIn($role_type, 'role_type');
				$_GET['role_types']=implode(",",$role_type);
			}
			if($role_type[0]=='all')
			{
				unset($where_list['role_type']);
			}
		}
		else
		{
			$role_arrays = explode(',',$role_types);
			$where_list['role_type'] = $this->DatabaseHandler->BuildIn($role_arrays, 'role_type');
		}
		if($where_list!=false)
		{
			$where = "WHERE ".implode(" AND \n\t", $where_list);
		}
				$roletype_list = array(
			'normal' => array('name' => '普通用户'),
			'seller' => array('name' => '合作商家'),
			'admin' => array('name' => '管理员')
		);

				$sql = "
		  SELECT
			 count(1) total
		  FROM
			  " . TABLE_PREFIX.'system_members' . "
		  $where";
		$query = $this->DatabaseHandler->Query($sql);
		extract($query->GetRow());

		$page_num=20;
		$p=max($p,1);
		$offset=($p-1)*$page_num;
		$pages=page($total,$page_num,'',array('var'=>'p'));
		$sql = "
		  SELECT
			  *
		  FROM
			  " . TABLE_PREFIX.'system_members' . "
		  $where
		  ORDER BY uid ASC LIMIT $offset,$page_num";
		$query = $this->DatabaseHandler->Query($sql);

		foreach($this->Config as $field => $name)
		{
			if(strpos($field, 'credits') !== false)
			{
				if($name != '')
				{
					$credit_list[$field] = $name;
				}
			}
		}
		while($row = $query->GetRow())
		{
			foreach($credit_list as $field => $val)
			{
				$row['credit_value_list'][] = $row[$field];
			}
			$roletype = $roletype_list[$row['role_type']];
			if($roletype != false){
				if($row['uid'] == 1){
					$row['roletype_name'] = '<font color="red"><b>网站创始人</b></font>';
				}elseif($row['role_type']=='admin'){
					$row['roletype_name'] = '<font color="red">'.$roletype['name'].'</font>';
				}elseif($row['role_type']=='seller'){
					$row['roletype_name'] = '<font color="blue">'.$roletype['name'].'</font>';
				}else{
					$row['roletype_name'] = $roletype['name'];
				}
			}
			$member_list[] = $row;
		}

		$action = 'admin.php?mod=member&code=delete';
		include handler('template')->file('@admin/member_search_list');
	}

	
	function DoDelete()
	{
		$this->CheckAdminPrivs('memberedite');
		$this->IDS = (array) ($this->IDS ? $this->IDS : $this->ID);
		foreach ($this->IDS as $key=>$val) {
			if(1 > ($this->IDS[$key] = (int) $val)) {
				unset($this->IDS[$key]);
			}
		}
		if (!$this->IDS) {
			$this->Messager("请先指定一个要删除的用户ID",null);
		}
		$query = $this->DatabaseHandler->Query("select * from `".TABLE_PREFIX."system_members` where `uid` in('".implode("','",$this->IDS)."')");

		$member_ids = array();
		$admin_list = array();
		$member_ids_count = 0;
		while ($row = $query->GetRow())
		{
			if(1==$row['uid'] || $row['role_type']!='normal') {
				$seller = dbc(DBCMax)->select('seller')->where(array('userid'=>$row['uid']))->limit(1)->done();
				if('seller' == $row['role_type'] && false == $seller) {
					;
				} else {
					$admin_list[$row['uid']] = $row['username'];
					continue;
				}
			}

						if(true === UCENTER && $row['ucuid'] > 0) {
				include_once(UC_CLIENT_ROOT . './client.php');

				uc_user_delete($row['ucuid']);
			}
			$member_ids[$row['uid']] = $row['uid'];
		}
		if(isset($member_ids[1])) unset($member_ids[1]);

		if (0 < ($member_ids_count =  count($member_ids))) {
						$this->DatabaseHandler->Query("delete from `".TABLE_PREFIX."system_members` where `uid` in ('".implode("','",$member_ids)."')");
						$this->DatabaseHandler->Query("delete from `".TABLE_PREFIX."system_memberfields` where `uid` in('".implode("','",$member_ids)."')");
						$this->DatabaseHandler->Query("delete from `".TABLE_PREFIX."system_log` where `uid` in('".implode("','",$member_ids)."')");
						$this->DatabaseHandler->Query("delete from `".TABLE_PREFIX."tttuangou_phone` where `uid` in('".implode("','",$member_ids)."')");
						foreach ($member_ids as $i => $uid)
			{
				$aliuid = meta('luid_'.$uid);
				meta('luid_'.$uid, null);
				meta('token_'.$aliuid, null);
				meta('ul.alipay.'.$aliuid, null);
			}
		}

		$msg = '';
		$msg .= "成功删除<b>{$member_ids_count}</b>位会员";
		if($admin_list) {
			$msg .= "，其中<b>".implode(' , ',$admin_list)."</b>是管理员或商家，不能直接删除";
		}
		$this->Messager($msg);
	}

	
	function Modify()
	{
		$this->CheckAdminPrivs('memberedite');
		$this->Title="编辑用户";
		$action="admin.php?mod=member&code=domodify";
				$sql="
		 SELECT
			 *
		 FROM
			 ".TABLE_PREFIX.'system_members'." M LEFT JOIN ".TABLE_PREFIX.'system_memberfields'." MF ON(M.uid=MF.uid)
		 WHERE
			 M.uid={$this->ID}";

		$query = $this->DatabaseHandler->Query($sql);

		$member_info=$query->GetRow();
		if($member_info==false)
		{
			$this->Messager("用户已经不存在");
		}

		extract($member_info);
		$uid=$this->ID;
		
		$roletype_list = array(
			'normal' => array('name' => '普通用户', 'value' => 'normal'),
			'admin' => array('name' => '管理员', 'value' => 'admin')
		);

        if('admin'== $member_info['role_type']  ){
                        $city_arr = dbc(DBCMax)->select('city')->where(array('display'=>1))->done();
            if($_GET['area_id']){
                $area_id = $_GET['area_id'];
                $places = logic('city')->get_places($area_id);
            }
                        $smember = dbc(DBCMax)->query('select uid,area,city_place_region from '.table('members')." where uid='".$uid."'")->limit(1)->done();
            $area = $smember['area'];
            if($area){
                $places = logic('city')->get_places($area);
            }
        }
				foreach($this->Config as $field => $name)
		{
			if(strpos($field, 'credits') !== false)
			{
				if($name != '')
				{
					$credit_list[$field] = $name;
				}
			}
		}
		$roletype_select = $this->FormHandler->Select('role_type', $roletype_list,$role_type);
				$gender_radio=$this->FormHandler->Radio('gender',array(
		array('name'=>"男",'value'=>'1'),
		array('name'=>"女",'value'=>'2'),
		array('name'=>"保密",'value'=>'0'),
		),$gender);
		list($year,$month,$day)=explode('-',$bday);
		$year_select=$this->FormHandler->NumSelect('year','1920','2006',$year!='0000'?$year:1980);
		$month_select=$this->FormHandler->NumSelect('month','1','12',$month);
		$day_select=$this->FormHandler->NumSelect('day','1','31',$day);
		$validate_radio = $this->FormHandler->YesNoRadio('validate',$member_info['validate']);
		$_options = array(
			'0' => array(
				'name' => '请选择',
				'value' => '0',
			),
			'身份证' => array(
				'name' => '身份证',
				'value' => '身份证',
			),
			'学生证' => array(
				'name' => '学生证',
				'value' => '学生证',
			),
			'军官证' => array(
				'name' => '军官证',
				'value' => '军官证',
			),
			'护照' => array(
				'name' => '护照',
				'value' => '护照',
			),
			'其他' => array(
				'name' => '其他',
				'value' => '其他',
			),
		);
		$validate_card_type_select = $this->FormHandler->Select('validate_card_type',$_options,$member_info['validate_card_type']);

				$log = logic('me')->money()->log($this->ID, '*');
		$money = logic('me')->money()->count($this->ID);

		include handler('template')->file('@admin/member_info');
	}

	
	function DoModify()
	{
		$this->CheckAdminPrivs('memberedite');

		$uid = post('uid', 'int');
		if(1 == $uid && MEMBER_ID != 1) {
			$this->Messager("您不能对此管理员的权限进行任何操作");
		}
		$userinfo = dbc(DBCMax)->select('members')->where(array('uid'=>$uid))->limit(1)->done();
		if(false == $userinfo) {
			$this->Messager("该用户不存在");
		}

		$data = array();

		
		$password = post('password');
		if($password) {
						if(in_array($userinfo['email2'], array('zuitu', 'astuan'))) {
				$data['email2'] = $userinfo['email2'] = '';
			}
			$password_hash = account()->password($password, $userinfo);
						if($password_hash && $userinfo['password'] != $password_hash) {
				$data['password'] = $password_hash;
				$data['secques'] = random(24);
					            if ( true === UCENTER )
	            {
	                include_once (UC_CLIENT_ROOT . './client.php');
	                $result = uc_user_edit($userinfo['username'], '', $password, '', 1);
	                if($result ==0 || $result ==1)
					{
						;
					}
					elseif($result ==-8)
					{
						$this->Messager('您的帐号在UC里是管理员，请到UC里修改密码！');
					}
					else
					{
	                    $this->Messager('通知UC修改密码失败，请检查你的UC配置！');
	                }
	            }
			}
		}

		
		$username = post('username', 'txt');
		if($username != $userinfo['username']) {
			if(false != dbc(DBCMax)->select('members')->where(array('username'=>$username))->limit(1)->done()) {
				$this->Messager("{$username}已经存在");
			}
			$data['username'] = $username;
		}

		
		$email = post('email', 'txt');
		if($email != $userinfo['email']) {
			if(false != dbc(DBCMax)->select('members')->where(array('email'=>$email))->limit(1)->done()) {
				$this->Messager("{$email}已经存在");
			}
			$data['email'] = $email;
		}

		
		$phone = post('phone', 'number');
		if($phone) {
						if(false != ($ret = logic('phone')->Check($phone, $userinfo['uid']))) {
				$this->Messager($ret);
			}
		} else {
			$data['phone_validate'] = 0;
		}
		$data['phone'] = $phone;
		if($phone != $userinfo['phone']) {
			logic('phone')->clear_invalid($userinfo['uid']); 		}

		
		$moneyMoved = post('moneyMoved', 'float');
		if ($moneyMoved != '')
		{
			if(false === admin_priv('quickrecharge')) {
				$this->Messager('您没有充值/扣费的权限');
			}

						Load::logic('me');
			$this->MeLogic = new MeLogic();

			$moneyOps = post('moneyOps');
			if ($moneyOps == 'plus')
			{
								$rs = logic('me')->money()->add($moneyMoved, $uid, array(
					'name' => '后台编辑(增加)',
					'intro' => '管理员（'.MEMBER_NAME.'）增加了您的余额，详情请联系！'
				));
			}
			elseif ($moneyOps == 'less')
			{
				if($moneyMoved > $userinfo['money']){
					$this->messager("操作失败，您的扣费金额过大，请重新操作！");
				}
				                $rs = logic('me')->money()->less($moneyMoved, $uid, array(
					'name' => '后台编辑(减少)',
					'intro' => '管理员（'.MEMBER_NAME.'）减少了您的余额，详情请联系！'
				));
			}
		}

		
		$role_type = post('role_type');
		if(1 == $uid) {
			$data['role_type'] = 'admin';
		} else {
			if('seller' == $userinfo['role_type']) {
				$data['role_type'] = 'seller';
			} else {
				$data['role_type'] = (in_array($role_type, array('normal', 'admin')) ? $role_type : 'normal');
			}
		}
		
		if('normal' == $data['role_type']) {
			$data['privs'] = '';
		}

		
		$home_uid = post('home_uid', 'int');
		$data['home_uid'] = (($home_uid > 0 && $home_uid != $userinfo['uid'] && $home_uid == user($home_uid)->get('id')) ? $home_uid : 0);

		
		$data['gender'] = post('gender', 'int');
		$data['qq'] = post('qq', 'number');
		$data['sell_pre'] = min(100, max(0, post('sell_pre', 'float')));
		$data['buy_pre'] = min(100, max(0, post('buy_pre', 'float')));
		$data['regip'] = post('regip', 'txt');
        $data['area'] = post('manage_area', 'int');
        $data['city_place_region'] = post('__cplace_region', 'int');
        $data['get_share_rebate'] = post('get_share_rebate', 'int');
				$ret = dbc(DBCMax)->update('members')->where(array('uid'=>$uid))->data($data)->done();

		        if($ret || $rs){
            $this->Messager("编辑成功");
        }else{
            $this->Messager("编辑失败");
        }
			}

	
	function moneylog() {
		$this->CheckAdminPrivs('member_moneylog');

		$list = logic('me')->money()->mlog(get('uid', 'int'), get('class', 'txt'), get('type', 'txt'));

		include handler('template')->file('@admin/member_moneylog');
	}

}