<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name wap.mod.php
 * @date 2016-07-25 18:45:53
 */





class ModuleObject extends MasterObject
{
    function ModuleObject( $config )
    {
		global $rewriteHandler;
		$rewriteHandler = null;

        $this->MasterObject($config);
        $runCode = Load::moduleCode($this);
        $this->$runCode();
    }
    function Main()
    {
        include handler('template')->file('@wap/index');
    }

    
    public function account_register_quick() {
    	$rets = array();
    	$inis = ini('settings.account_register_quick');
    	if(false == $inis) {
    		$rets = array('error'=>true, 'result'=>'settings is closed');
    	} else {
    		if(get('key') != $inis) {
    			$rets = array('error'=>true, 'result'=>'invalid request');
    		} else {
    			$phone = get('phone', 'number');
	    		$pwd = get('password');
	    		if(empty($phone)) {
	    			$rets = array('error'=>true, 'result'=>'phone is empty');
	    		} else {
	    			$ret = logic('phone')->Check($phone);
	    			if(false != $ret) {
	    				$rets = array('error'=>true, 'result'=>$ret);
	    			} else {
	    				if(empty($pwd)) {
	    					$rets = array('error'=>true, 'result'=>'password is empty');
	    				} else {
				    		$home_uid = get('home_uid', 'int');
				    		$rets = account()->RegisterPhone($phone, $pwd, null, $home_uid, false);
				    		if(false == $rets['error'] && $rets['result'] > 0) {
				    			$money = (float) get('money', 'number');
				    			if($money > 0 && $money <= 10) {
				    				account()->rebate($rets['result'], $money);
				    			}
				    		}
				    	}
			    	}
		    	}
    		}
    	}
    	echo json_encode($rets);
    	exit;
    }

	
    public function account_login()
	{
		include handler('template')->file('@wap/account_login');
	}
	
	public function account_logcheck()
	{
		$username = post('username', 'txt');
		$password = post('password', 'txt');
		$loginR = account()->Login($username, $password, true); 		if ($loginR['error'])
		{
			$errmsg = $loginR['result'];
			include handler('template')->file('@wap/account_login');
		}
		else
		{
			$ref = account()->loginReferer();
			$ref || $ref = rewrite('index.php?mod=wap');
			header('Location: '.$ref);
		}
	}
	
	public function account_logout()
	{
		account()->Logout(MEMBER_NAME);
		header('Location: '.rewrite('index.php?mod=wap'));
	}
	
	public function coupon_input()
	{
		$msgcode = get('msgcode', 'chars');
		$number = get('number') ? get('number', 'number') : '';
		$password = get('password') ? get('password', 'number') : '';
		if ($msgcode)
		{
			$mmaps = array(
				'ops-success' => '验证消费成功！',
				'input-blank' => '请输入号码和密码！',
				'not-found' => TUANGOU_STR . '券输入无效！',
				'access-denied' => '此券不是您的产品！',
				'password-wrong' => TUANGOU_STR . '券密码错误！',
				'be-used' => '此券已经被使用了！',
				'be-overdue' => '此券已经过期了！',
				'be-invalid' => '此券已经失效了！'
			);
			$msg = isset($mmaps[$msgcode]) ? $mmaps[$msgcode] : '未知错误';
			if ($msgcode == 'ops-success')
			{
				$product = logic('coupon')->ProductGet(get('last', 'number'));
			}
		}
		include handler('template')->file('@wap/coupon_input');
	}
	
	public function coupon_verify()
	{
		$number = post('number') ? post('number', 'number') : '';
		$password = post('password') ? post('password', 'number') : '';
		if ($number && $password)
		{
			$result = logic('coupon')->MakeUsed($number, $password);
			if ($result['error'])
			{
				$this->coupon_input_msg($result['errcode'], $number, $password);
			}
			else
			{
				$this->coupon_input_msg('ops-success', '', '', $number);
			}
		}
		else
		{
			$this->coupon_input_msg('input-blank', $number, $password);
		}
	}
	
	private function coupon_input_msg($msgcode, $number = '', $password = '', $last = '')
	{
		$url = rewrite('index.php?mod=wap&code=coupon&op=input&msgcode='.$msgcode.'&number='.$number.'&password='.$password.'&last='.$last);
		header('Location: '.$url);
	}

	public function get_password() {
		if(MEMBER_ID > 0) {
			$this->msg('您已经登录了');
		}

		$is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android');

		$act = ($_GET['act'] ? $_GET['act'] : $_POST['act']);

		if('step2' == $act) {
			$username = post('username');
			if(empty($username)) {
				$this->msg('用户名不能为空', -1);
			}
			$username = account()->username($username);
			$user = dbc(DBCMax)->select("members")->where(array('username' => $username))->limit(1)->done();
			if(false == $user) {
				$this->msg('用户已经不存在了', -1);
			}
			$uid = $user['uid'];
			if(empty($user['phone']) || false == $user['phone_validate']) {
				$this->msg('该用户没有设置手机或该号码还没有通过验证，不能通过手机方式找回密码');
			}
			$phone = substr($user['phone'], 0, 3) . '****' . substr($user['phone'], -4);
			if(true == logic('seccode')->verify(post('seccode'))) {
				$ret = logic('phone')->VfSend($user['phone'], $uid);
			} else {
				$ret = '请输入正确的图片验证码！';
			}
			if($ret) {
				$this->msg($ret);
			}
		} elseif ('step3' == $act) {
			$uid = post('uid', 'int');
			if($uid < 1) {
				$this->msg('请指定一个用户UID');
			}
			$user = user($uid)->get();
			if(false == $user) {
				$this->msg('用户已经不存在了');
			}
			$vfcode = post('vfcode');
			if(empty($vfcode)) {
				$this->msg('手机验证码不能为空', -1);
			}
			if('' == $this->Post['password'])
			{
				$this->msg('新密码不能为空', -1);
			}
			if($this->Post['password']!=$this->Post['confirm'])
			{
				$this->msg('两次输入的密码不一致', -1);
			}
			$ret = logic('phone')->Vfcode($user['phone'], $vfcode, $uid);
			if($ret) {
				$this->msg($ret, -1);
			}

			$password = account()->password($this->Post['password'], $user);
			$sql="UPDATE ".TABLE_PREFIX. 'system_members'." SET `password`='{$password}', `secques`='" . random(24) . "' WHERE uid='$uid'";
			$this->DatabaseHandler->Query($sql);
			$sql="UPDATE ".TABLE_PREFIX.'system_memberfields'." SET `authstr`='',`auth_try_times`='0' WHERE uid='$uid'";
			$this->DatabaseHandler->Query($sql);

						if ( true === UCENTER )
			{
				include_once (UC_CLIENT_ROOT . './client.php');
				$result = uc_user_edit($user['username'], '', $this->Post['password'], '', 1);
				if($result ==0 || $result ==1)
				{
					;
				}
				elseif($result ==-8)
				{
					$this->msg('您的帐号在UC里是管理员，请到UC里修改密码！');
				}
				else
				{
					$this->msg('通知UC修改密码失败，请检查你的UC配置！');
				}
			}

			$this->msg("新密码设置成功");
		}

		include handler('template')->file('@wap/get_password');
	}

	public function msg($msg, $to = '', $time = 1) {
		include handler('template')->file('@wap/msg');
		exit;
	}

	
	public function proceeds() {
		
		$seller_userid = api_uid();
		if($seller_userid < 1) {
			$this->msg('请先登录');
		}
		$seller_user = user($seller_userid)->get();
        $seller = logic('seller')->GetOne(null, $seller_userid);
		if(false == $seller_user) {
			$this->msg('用户已经不存在了');
		}
		if(false == in_array($seller_user['role_type'], array('seller', 'admin'))) {
			$this->msg('只有商家可以使用该功能');
		}
                if(ini('product.default_allow_bangding_phone') == 1){
                        $check_bangding_phone = 1;
        }
		$error_msg = '';
		$act = ($_GET['act'] ? $_GET['act'] : $_POST['act']);
		$do = ($_GET['do'] ? $_GET['do'] : $_POST['do']);
		$act = in_array($act, array('step1', 'step2', 'step3', )) ? $act : 'step1';
                if('step1' == $act){
            
        }
		if('step2' == $act) {
			$money = (float) get('money', 'number');
			if($money <= 0) {
				$error_msg = '金额必须大于0';
			} else {
				if($do) {
					$username = post('username', 'txt');
										if(empty($username)) {
						$error_msg = '账号不能为空';
					} else {
						$user = account()->Search('username', account()->username($username), 1);
						if(false == $user) {
							$error_msg = '该账号不存在，请确认后重新输入';
						} else {
							if($seller_userid == $user['uid']) {
								$error_msg = '请使用其他账号';
							}
						}
						if(false == $error_msg) {
							
                            
                            $time = time();
								$auth = authcode("{$user['uid']}|{$money}|{$time}", "ENCODE");
								header('Location: ' . 'index.php?mod=wap&code=proceeds&act=step3&auth=' . urlencode($auth));

						}
					}
				}
			}
		} elseif ('step3' == $act) {
			$auth = get('auth');
			empty($auth) && $auth = post('auth');
			if(empty($auth)) {
                if($_GET['money'] && $_GET['phone']){
                    $money = $_GET['money'];
                    $phone = $_GET['phone'];
                    $uid = $_GET['uid'];
                    $time = time();
                    $auth = authcode("{$uid}|{$money}|{$time}", "ENCODE");
                    $first_bd = 1;
                }else{
                    $error_msg = '参数传入错误';
                }
                $user = user($uid)->get();
                if($user['money'] <= 0) {
                    $error_msg = '该账号下的余额为0，请直接线下支付 <b>&yen;'.$money.'</b> 元！';
                }

            } else {
				list($uid, $money, $time) = explode('|', authcode($auth, 'DECODE'));
				if($uid < 1 || $money <= 0 || $time + 600 < time()) {
					$error_msg = '参数传入错误，请返回重试';
				} else {
					$user = user($uid)->get();
					if(false == $user || $seller_userid == $user['uid']) {
						$error_msg = '请使用其他账号';
					} else {
						if($user['money'] <= 0) {
							$error_msg = '该账号下的余额为0，请直接线下支付 <b>&yen;'.$money.'</b> 元！';
                        } else {
                            $first_bd = post('first_bd');
						}
                        $phone = ($user['phone_validate'] ? $user['phone'] : post('phone'));
                                                if($user['phone_validate']) {
                            $phone_view = logic('phone')->view($user['phone']);
                        }
                        if($do) {
                            if(post('vfcode')){
                                $vfcode = post('vfcode');
                                if(false != ($ret = (logic('phone')->Vfcode($phone, $vfcode, false)))) {
                                    $error_msg = $ret;
                                } else {
                                                                        logic('phone')->bind($phone, $uid, false);

                                    $pmoney = ($user['money'] > $money ? $money : $user['money']);
                                    $nmoney = $money - $pmoney;

                                                                        logic('seller')->proceeds($seller_userid, $uid, $pmoney);
                                                                        notify($uid, 'wap.proceeds.notify', array(
                                        'username' => $seller['sellername'],
                                        'money' => $pmoney,
                                    ));
                                }
                            }else{
                                                                $password = post('password');
                                if(md5($password) != $user['password']) {
                                    $error_msg = '您输入密码错误，请重试！';
                                }else{
                                    $pmoney = ($user['money'] > $money ? $money : $user['money']);
                                    $nmoney = $money - $pmoney;

                                                                        logic('seller')->proceeds($seller_userid, $uid, $pmoney);
                                                                        notify($uid, 'wap.proceeds.notify', array(
                                        'username' => $seller['sellername'],
                                        'money' => $pmoney,
                                    ));
                                }
                            }
                        }
					}
				}
			}
		}

		include handler('template')->file('@wap/proceeds');
	}
    
    public function refund_detail() {
        $uid = api_uid();
        $name = '商家收款';
        $sql = 'SELECT *
		FROM
			' . table('usermoney') . '
		WHERE
			userid = ' . $uid . ' AND
			name =  "'. $name .'"
		ORDER BY
			id
		DESC';
        page_moyo_max_io(50);
        $sql = page_moyo($sql);
        $usermoney = dbc(DBCMax)->query($sql)->done();
        $proceeds = array();
        $proceeds_money = 0;
        if($usermoney){
            foreach($usermoney as $val){
                if($val['type']!='minus'){
                    $val['intro'] = str_replace("<br/>商家收款的金额已经累计到您的销售账户中，如需提出该部分金额，请到商家管理进行“申请结算”","",$val['intro']);
                    $proceeds[] = $val;
                    $proceeds_money += $val['money'];
                }
            }
        }

        include handler('template')->file('@m/refund_detail');
    }

    
    public function check_bangding_phone(){
        $phone = (is_numeric($_GET['phone']) ? (string) $_GET['phone'] : 0);
        $username = (string) $_GET['username'];
        $user = dbc(DBCMax)->select('members')->where(array('username'=>$username))->limit(1)->done();
        $password = $_GET['password'];
        $money = $_GET['money'];
        if($password && account()->password($password, $user) != $user['password']) {
            echo '您输入的密码有误，请重试！';
        }else{
            $rs = logic('phone')->Check($phone, $check_exists = true, $check_ip = false);
            if($rs){
                echo $rs;
            }else{
                                $sql  = "update ".table('members')." set `phone`='$phone' where `username`='$username'";
                if(dbc()->Query($sql)){
                    
                    echo 0;
                }
            }
        }
    }
	
	public function vfsend() {
		$auth = post('auth');
		$phone = post('phone', 'txt');

		list($uid, $money, $time) = explode('|', authcode($auth, 'DECODE'));
		if($uid < 1 || $money <= 0 || $time + 600 < time()) {
			exit('参数传入错误，请返回重试');
		}

		if(empty($phone) && get('user_phone') && user($uid)->get('phone_validate')) {
			$phone = user($uid)->get('phone');
		}
		if(true == logic('seccode')->verify(post('seccode'))) {
			$ret = logic('phone')->VfSend($phone, false);
		} else {
			$ret = '请输入正确的图片验证码！';
		}

		exit($ret);
	}

    
    public function create_paybill()
    {
        $uid = MEMBER_ID;
        $sid = get('sid','int');        $bill_money = get('bill_money');
                if($uid < 1 || $sid < 1) {
            $this->msg('订单创建错误');
        }
                $totalprice = $bill_money;
        $order = logic('order')->GetFree($uid,99999);

        logic('order')->Update($order['orderid'], array('totalprice'=>$totalprice, 'paymoney'=>$totalprice, 'sid'=>$sid));

    }



	public function register_agreement() {
		$html = logic('html')->query('terms');

		include handler('template')->file('@wap/register_agreement');
	}

	
	public function recharge() {
		$uid = api_uid();
		if($uid < 1) {
			$this->msg('请先登录');
		}
		$user = user($uid)->get();
		if(false == $user) {
			$this->msg('用户已经不存在了');
		}

		$payment = logic('pay')->GetOne('recharge');
		if ($payment['enabled'] != 'true') {
			$this->msg('本站没有开放充值卡功能！');
		}

				$order = logic('recharge')->GetFree(0, 0, $uid);
		logic('recharge')->Update($order['orderid'], array('payment'=>$payment['id']));

				include handler('template')->file('@wap/recharge');
	}

    
    public function seller_settled(){
                $seller_userid = (int) (api_uid() ? api_uid() : MEMBER_ID);
        if($seller_userid < 1) {
        	$this->msg('请先登录', 'm.php?mod=account&code=login');
        }
        
                        $seller = logic('seller')->GetOne(null,$seller_userid);
        if($seller) {
        	if('true' == $seller['enabled']) {
        		$this->msg('您已经是商家，无需重复申请');
        	} else {
        	   	$this->msg('正在审核中，请耐心等待，如有问题可以联系客服');
        	}
        }
        
        $sql = 'SELECT * FROM '.table('members').' WHERE uid='.$seller_userid;
        $user = dbc()->Query($sql)->GetRow();
        if('admin' == $user['role_type']) {
        	$this->msg('管理员申请商家，请到后台商家管理添加');
        }
        if('normal' != $user['role_type']) {
        	$this->msg('只有普通会员可以申请成为商家');
        }
        
        $this->Title = __('申请成为商家');

        $action = 'index.php?mod=wap&code=do_seller_settled';
        include handler('template')->file('@wap/seller_settled');
    }

    
    public function do_seller_settled(){
        $fields = array('area','city_place_region','city_place_street','category','sellername','selleraddress','sellerphone','sellerurl','price_avg','trade_time','content','sellermap','profit_id');
        $data = array();
        foreach($fields as $f){
            if($f == 'category'){
                $data[$f] = $_POST['__catalog_subclass'] > 0 ? (int)$_POST['__catalog_subclass'] : (int)$_POST['__catalog_topclass'];
            }else{
                $data[$f] = $_POST[$f];
            }
        }
        $data['userid'] = user()->get('id');
                if (isset($_FILES['id_card']['name']) && $_FILES['id_card']['error'] == 0){
            $data['id_card'] = 'uploads/images/seller/idcard/'.$data['userid'].'_'.time().'.gif';
            logic('upload')->Save('id_card', ROOT_PATH.$data['id_card']);
        }
        if (isset($_FILES['zhizhao']['name']) && $_FILES['zhizhao']['error'] == 0){
            $data['zhizhao'] = 'uploads/images/seller/zhizhao/'.$data['userid'].'_'.time().'.gif';
            logic('upload')->Save('zhizhao', ROOT_PATH.$data['zhizhao']);
        }
        $sid = logic('seller')->Join($data);
        if($sid){            dbc()->SetTable(table('members'));
            dbc()->Update(array('role_type' => 'seller'), 'uid = '.$data['userid']);
        }
        if (!$sid) $this->msg('提交失败！请重试', -1);
        $this->msg('申请成功, <a href="m.php">点此返回首页</a>', '');

    }

    
    public function seller_map(){
        $sid = get('id');
        $seller = logic('seller')->GetOne($sid);
        $sellermap = $seller['sellermap'];

        include handler('template')->file('@wap/seller_map');
    }

}