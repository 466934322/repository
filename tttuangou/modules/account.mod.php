<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name account.mod.php
 * @date 2016-04-12 18:33:12
 */




session_start();

class ModuleObject extends MasterObject
{
	public $Username = '';
	public $Password = '';
	public $Secques = '';
	public $IsAdmin = false;
	public $Rebind = true;

	function ModuleObject( $config )
	{
		$this->MasterObject($config);
						$this->Username = post('username', 'string');
		$this->Password = post('password', 'string');
		$this->Secques = quescrypt($this->Post['question'], $this->Post['answer']);
		if ( MEMBER_ID > 0 )
		{
			$this->IsAdmin = true;
		}
		if(strlen($_GET['code']) == 32 && strlen($_GET['state']) == 32) {
			if (   !empty($_GET['from']) && ($_GET['from'] == 'WeChat')
				|| !empty($_SESSION['wx_state'])
			) {
				$this->Code  = 'wxlogin';
			}
			else {
				$this->Code  = 'qqlogin';
			}
		}
				$runCode = Load::moduleCode($this);
		$this->$runCode();
	}

	public function Main()
	{
		header('Location: '.rewrite('?mod=me'));
	}

	public function qqlogin()
	{
		account('ulogin')->qqopenid();
	}
	public function wxlogin()
	{
		account('ulogin')->wxopenid();
	}

	public function qqgetuserinfo()
	{
		account('ulogin')->reg_and_login('qq');
		if(account('ulogin')->is_first_login) {
			$this->Messager('登录成功！这是您首次使用QQ登录，请务必设置登录密码，该密码会在您下单支付的时候使用到。', '?mod=me&code=setting', 5);
		} else {
			$ref = account()->loginReferer();
			$ref || $ref = '?mod=me';
			$this->Messager(__('登录成功！').$loginR['result'], $ref);
		}
	}

	public function wxgetuserinfo()
	{
		account('ulogin')->wx_reg_and_login();
		if (false != ($nuid = account('ulogin')->is_first_login)) {
			if(false == $this->Rebind) {
				$this->Messager('登录成功！这是您首次使用微信登录，请务必设置登录密码，该密码会在您下单支付的时候使用到。', '?mod=me&code=setting', 5);
			} else {
				$this->Messager(null, ini('settings.site_url') . '/?mod=account&code=wxrebind&nuid='.$nuid);
			}
		} else {
			$ref = account()->loginReferer();
			$ref || $ref = '?mod=me';
			$this->Messager(__('登录成功！'). $loginR['result'], $ref);
		}
	}

	
	public function wxrebind() {
		if(post('act')) {
			$p = $_POST;
			$inajax = (get('inajax') || post('inajax'));
			$dorebind = true;
			if($inajax) {
				$dorebind = false;
				$p = array_iconv('utf-8', ini('settings.charset'), $p);
			}
			$ret = account('ulogin')->wxrebind($p, $dorebind);
			if($ret) {
				if($inajax) {
					exit($ret);
				} else {
					$this->Messager($ret);
				}
			}
			if(false == $inajax) {
				$ref = account()->loginReferer();
				$ref || $ref = '?mod=me';
				$this->Messager(__('绑定成功！'), $ref);
			}
			exit;
		} else {
			$nuid = get('nuid');

			include template('account_wxrebind');
		}
	}

	public function Hometel()
	{
		$value = get('value', 'txt');
		if (false != $f = filter($value))
		{
			exit(jsonEncode(array('status'=>'failed','result'=>$f)));
		}
		$r = account()->Exists('phone', $value);
		if($r){
			$user = account()->search('phone', $value);
			$r = $user[0]['username'];
		}
		$ops = array('status' => 'ok','result' => $r);
		exit(jsonEncode($ops));
	}

	public function Exists()
	{
		$field = get('field', 'txt');
		$value = get('value', 'txt');
		if (false != ($f = filter($value)))
		{
			exit(jsonEncode(array('status'=>'failed','result'=>$f)));
		}
		$allows = array(
			'email', 'name', 'phone'
		);
		if (false !== array_search($field, $allows))
		{
						$r = false;
			if('name' == $field) {
				$r = account()->invaidAccount($value);
			} elseif ('email' == $field) {
				$r = account()->invaidAccount(null, null, $value);
			}
			if($r) {
				$ops = array('status'=>'failed','result' => $r);
			} else {
				if ($field == 'phone' && !ini('member.phone.unique')){
					$r = false;
				}else{
					$r = account()->Exists($field, $value);
				}
				$ops = array('status' => 'ok','result' => $r);
			}
		}else{
			$ops = array('status'=>'failed','result' => __('未允许字段'));
		}

		exit(jsonEncode($ops));
	}
	function Activate()
	{
		$this->Messager("您还没有通过邮箱验证呢！<a href='?mod=account&code=sendcheckmail&uname=" . urlencode(user()->get('name')) . "'>点这里重新发送认证邮件  </a>", 0);
	}

	function Confirm()
	{
		$pwd = get('pwd', 'txt');
		if ( $pwd == '' ) $this->Messager(__("错误！"));
		$pwdT = authcode(urldecode($pwd), 'DECODE', ini('settings.auth_key'));
		if ($pwdT == '')
		{
						$pwd = authcode($pwd, 'DECODE', ini('settings.auth_key'));
		}
		else
		{
			$pwd = $pwdT;
		}
		if ($pwd == '') $this->Messager('邮箱认证失败，请重发认证邮件或联系网站管理员进行人工审核！', 0);
		$sql = 'select * from ' . TABLE_PREFIX . 'system_members where truename = \'' . $pwd . '\'';
		$query = $this->DatabaseHandler->Query($sql);
		$user = $query->GetRow();
		if ( $user == '' || $user['checked'] == 1 ) $this->Messager(__("用户不存在或已经通过验证！"));
		$ary = array(
			'checked' => 1
		);
		$this->DatabaseHandler->SetTable(TABLE_PREFIX . 'system_members');
		$result = $this->DatabaseHandler->Update($ary, 'truename = \'' . $pwd . '\'');
		$this->Messager(__("邮箱认证成功！请重新登录"), rewrite('?mod=account&code=login'));
	}

	
	function Login()
	{
		if ( (MEMBER_ID != 0 and false == $this->IsAdmin) || MEMBER_ID > 0)
		{
			$this->Messager("您已经使用用户名 " . MEMBER_NAME . " 登录系统，无需再次登录！", null);
		}
		$loginperm = $this->_logincheck();
		if ( ! $loginperm )
		{
			$this->Messager("累计 5 次错误尝试，15 分钟内您将不能登录。", null);
		}
		$this->Title = "用户登录";
		
		$action = "?mod=account&code=login&op=done";
		$question_select = handler('form')->Select("question", ConfigHandler::get("member", "question_list"), 0);
		$role_type_select = handler('form')->Radio("role_type", ConfigHandler::get("member", "role_type_list"), "normal");
		account()->loginReferer($_SERVER['HTTP_REFERER']);
		include (template("account_login"));
	}

	
	function Login_done()
	{
		$loginperm = $this->_logincheck();
		if ( ! $loginperm )
		{
			$this->Messager("累计 5 次错误尝试，15 分钟内您将不能登录。", null);
		}
		$user = account()->Search('name', $this->Username, 1);
				if ( $user && $user['role_type'] != 'admin' && $user['checked'] == 0 && ini('product.default_emailcheck') )
		{
			header('Location: '.rewrite('?mod=account&code=activate'));exit;
		}
		$loginR = account()->Login($this->Username, $this->Password, ($_POST['keeplogin'] == 'on'));
		if ($loginR['error'])
		{
			$this->_loginfailed($loginperm);
			$this->Messager($loginR['result'], -1);
		}
		$ref = account()->loginReferer();
		$ref || $ref = '?mod=me';
		$this->Messager(__('登录成功！').$loginR['result'], $ref);
	}

	function Login_union()
	{
		$flag = get('flag', 'txt');
		if (!$flag || !ini('alipay.account.login.source.'.$flag)) exit('ERROR: no Union Login Request');

		$html = account('ulogin')->linker($flag);
		include handler('template')->file('@account/login/redirect');
	}

	function Login_callback()
	{
		$from = get('from', 'txt');
		$uuid = account('ulogin')->verify($from);
		if ($uuid !== false)
		{
			if (meta($uuid))
			{
								$result = account('ulogin')->login($uuid);
				$ref = account()->loginReferer();
				$ref || $ref = '?mod=me';
				$this->Messager(__('登录成功！').$result, $ref);
			}
			else
			{
								$data = account('ulogin')->ddata($from);
								include handler('template')->file('account_active');
			}
		}
		else
		{
			$this->Messager(__('快捷登录验证出错！'));
		}
	}
	function Login_active()
	{
		$uuid = post('uuid', 'txt');
		$username = post('username');
		$password = post('password');
		$mail = post('mail', 'txt');
		if (!$mail || !check_email($mail))
		{
			$this->Messager(__('请输入正确的Email地址！'), -1);
		}
		$phone = post('phone', 'txt');
		if($phone) {
			if (false != ($ret = logic('phone')->Check($phone, false)))
			{
				$this->Messager($ret, -1);
			}
			if (account()->Exists('phone', $phone))
			{
				$this->Messager(__('您输入的手机号码已被使用，请重新输入！'), -1);
			}
		}
		$subs = post('subs');
				$result = account('ulogin')->active($uuid, $username, $password, $mail);
		if (!$result)
		{
			$this->Messager(__('帐号激活失败！'));
		}
				if ($subs)
		{
			logic('subscribe')->Add(logic('misc')->City('id'), 'mail', $mail, 'true');
		}
				if ($phone && strlen($phone) == 11)
		{
			user($result)->set('phone', $phone);
			if ($subs)
			{
				logic('subscribe')->Add(logic('misc')->City('id'), 'sms', $phone, 'true');
			}
		}
				$result = account('ulogin')->login($uuid);
		$ref = account()->loginReferer();
		if(empty($ref) || stristr($ref, 'account')) {
			$ref = '?mod=me';
		}
		$this->Messager(__('登录成功！').$result, $ref);
	}

	
	function Logout()
	{
		$this->_fix_failedlogins();
		$logoutR = account()->Logout(MEMBER_NAME);
		$this->Messager($logoutR['result'] . '退出成功', $this->Config['site_url']);
	}

	function _logincheck()
	{
		$onlineip = $_SERVER['REMOTE_ADDR'];
		$timestamp = time();
		$query = $this->DatabaseHandler->Query("SELECT count, lastupdate FROM " . TABLE_PREFIX . 'system_failedlogins' . " WHERE ip='$onlineip'");
		if ( $login = $query->GetRow() )
		{
			if ( $timestamp - $login['lastupdate'] > 900 )
			{
				return 3;
			}
			elseif ( $login['count'] < 5 )
			{
				return 2;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return 1;
		}
	}

	function _loginfailed( $permission )
	{
		$onlineip = $_SERVER['REMOTE_ADDR'];
		$timestamp = time();
		switch ( $permission )
		{
			case 1 :
				$this->DatabaseHandler->Query("REPLACE INTO " . TABLE_PREFIX . 'system_failedlogins' . " (ip, count, lastupdate) VALUES ('$onlineip', '1', '$timestamp')");
				break;
			case 2 :
				$this->DatabaseHandler->Query("UPDATE " . TABLE_PREFIX . 'system_failedlogins' . " SET count=count+1, lastupdate='$timestamp' WHERE ip='$onlineip'");
				break;
			case 3 :
				$this->DatabaseHandler->Query("UPDATE " . TABLE_PREFIX . 'system_failedlogins' . " SET count='1', lastupdate='$timestamp' WHERE ip='$onlineip'");
				$this->DatabaseHandler->Query("DELETE FROM " . TABLE_PREFIX . 'system_failedlogins' . " WHERE lastupdate<$timestamp-901", 'UNBUFFERED');
				break;
		}
	}

	function Register_phone() {
		$this->Title = __('手机注册');

				$city = logic('misc')->CityList();
		$action = '?mod=account&code=register&op=phone_done';
		$home_uid = $_GET['u'];

		include(template('account_register_phone'));
	}

	function Register_phone_done() {
		$city = post('city', 'int');
		$home_uid = post('home_uid', 'int');
		$hometel = post('hometel', 'number');
		if ($_POST['hometel'] != '')
		{
			if($hometel && strlen($hometel) == 11 && account()->Exists('phone', $hometel))
			{
				$home_info = account()->Search('phone', $hometel);
				$home_uid = $home_info[0]['uid'];
			}else{
				$this->Messager(__('您输入的邀请人手机号码不存在，请重新输入！'), -1);
			}
		}

		$phone = post('phone');
		if(empty($phone)) {
			$this->Messager('手机号不能为空', -1);
		}

		$vfcode = post('vfcode');
		if(empty($vfcode)) {
			$this->Messager('请输入手机验证码', -1);
		}

		$pwd = post('pwd');
		$ckpwd = post('ckpwd');
				if ( $pwd != $ckpwd ) {
			$this->Messager("两次密码输入不一致！", -1);
		}

		$rets = account()->RegisterPhone($phone, $pwd, $vfcode, $home_uid, true);

		if($rets['error']) {
			$this->Messager($rets['result'], -1);
		}

				if ( post('showemail') ) {
			logic('subscribe')->Validate(logic('subscribe')->Add($city, 'sms', $phone));
		}

		$this->Messager('注册成功了');

	}

	function Register()
	{
		$this->Title = __('注册');
				$city = logic('misc')->CityList();
		$action = '?mod=account&code=register&op=done';
		$_GET['u'] = (int) strip_tags($_GET['u']);
		$home_uid = $_GET['u'];
		if(false!=ini('member.register.closemail')) {
			$this->Messager(null, 'index.php?mod=account&code=register&op=phone&u='.(int)$_GET['u']);
		}
		include (template("account_register"));
	}

	function Register_done()
	{
		if(false!=ini('member.register.closemail')) {
			$this->Messager('已经关闭了邮箱注册功能，请使用手机号进行注册', 'index.php?mod=account&code=register&op=phone&u='.(int) $_POST['home_uid']);
		}

		$pwd = post('pwd');
		$ckpwd = post('ckpwd');
				if ( $pwd != $ckpwd )
		{
			$this->Messager("两次密码输入不一致！", -1);
		}
		$truename = post('truename');
		$email = post('email');
		$phone = post('phone');
		$city = post('city', 'int');
		$home_uid = post('home_uid', 'int');
		$hometel = post('hometel', 'number');
		
		if ($_POST['hometel'] != '')
		{
			if($hometel && strlen($hometel) == 11 && account()->Exists('phone', $hometel))
			{
				$home_info = account()->Search('phone', $hometel);
				$home_uid = $home_info[0]['uid'];
			}else{
				$this->Messager(__('您输入的邀请人手机号码不存在，请重新输入！'), -1);
			}
		}
		
				$rresult = account()->Register($truename, $pwd, $email, $phone, null, null, $home_uid);
		if ($rresult['error'])
		{
			$this->Messager($rresult['result'], -1);
		}
		if ( ! ini('product.default_emailcheck'))
		{
			$keepLogin = true;
						$lresult = account()->Login($truename, $pwd, $keepLogin);
			if ($lresult['error'])
			{
				$this->Messager('注册成功，自动登录失败：'.$lresult['result'], -1);
			}
			$ref = account()->loginReferer();
			$ref || $ref = '?mod=me';
			$ucsynlogin = $lresult['result'];
						if ( post('showemail') )
			{
								logic('subscribe')->Validate(logic('subscribe')->Add($city, 'mail', $email));
				if ($phone && preg_match('/[0-9]{8,12}/', $phone))
				{
					logic('subscribe')->Validate(logic('subscribe')->Add($city, 'sms', $phone));
				}
			}
			$this->Messager("注册成功{$ucsynlogin}", $ref);
		}
				$this->registmail($truename, $email);
		$this->Messager($r['result']."感谢您的注册，我们已经给您的邮箱发送了一封邮件请您登录邮箱激活账号！", 0);
	}

		function registmail( $truename, $email )
	{
		$key = authcode($truename, 'ENCODE', ini('settings.auth_key'));
		$mail['title'] = $this->Config['site_name'] . '欢迎您！';
		$mail['content'] = $this->Config['site_name'] . '欢迎您的注册 ，<a href="' . $this->Config['site_url'] . '/?mod=account&code=confirm&pwd=' . urlencode($key) . '">请点击这里激活账号</a>，或者复制 <br/>' . $this->Config['site_url'] . '/?mod=account&code=confirm&pwd=' . urlencode($key) . '到浏览器中';
		logic('service')->mail()->Send($email, $mail['title'], $mail['content']);
	}

	function Sendcheckmail()
	{
		$uname = get('uname', 'string');
		$uname = preg_replace('/[\s\(\)\=]/', '', $uname);
		if( strlen($uname) > 100) die('非法调用');
						$user = dbc(DBCMax)->select('members')->where(array('username' => $uname, 'checked' => 0))->limit(1)->done();
		if ( $user != '' )
		{
			$this->registmail($uname, $user['email']);
			$this->Messager("已经发送一封确认信件到您的邮箱去了，请注意查收！", 0);
		}
		$this->Messager("错误，该用户已确认信箱或不存在！", 0);
	}

	function _fix_failedlogins($uid = MEMBER_ID) {
		$onlineip = $_SERVER['REMOTE_ADDR'];
		$timestamp = time();
		$this->DatabaseHandler->Query("DELETE FROM ".TABLE_PREFIX.'system_failedlogins'." WHERE lastupdate<$timestamp-901", 'UNBUFFERED');
		if($uid > 0) {
			$this->DatabaseHandler->Query("DELETE FROM ".TABLE_PREFIX.'system_failedlogins'." WHERE `ip`='{$onlineip}'", 'UNBUFFERED');
		}
	}

	
	public function quickSignIn() {

		$signInType = get('signInType', 'string');					$signInPhone = get('signInPhone', 'string');				$signInDynamicCode = get('signInDynamicCode', 'string');					$signInPassword = get('signInPassword', 'string');							$home_id = get('home_id') ? get('home_id') : 0;									if (!isset($signInType) || !in_array($signInType, array('password', 'dynamicCode'))) {

			exit('数据验证失败，请通过正常途径提交表单！');

		}

			if ($signInType == 'dynamicCode') {
						$result = logic('phone')->Check($signInPhone, false);
			if ($result) {
				exit($result);
			}
		}

				if ($signInType == 'password') {		
			$this->quickLogin($signInPhone, $signInPassword);

		}
		else if ($signInType == 'dynamicCode') {	
						if (!isset($signInDynamicCode)) {

				exit('手机动态码不能为空！');

			}

						$user = logic('account')->Search('phone', $signInPhone, 1);					if ($user) {

								$result = logic('phone')->Vfcode($signInPhone, $signInDynamicCode);
				if($result) {
					exit($result);
				}

								$this->quickLogin($signInPhone, $user['password']);
			}
			else {

								$rets = logic('account')->RegisterPhone($signInPhone, $signInDynamicCode, $signInDynamicCode, $home_id, false);

				if($rets['error']) {
					exit($rets['result']);
				}

								$this->quickLogin($signInPhone, $signInDynamicCode);
			}

		}


	}

	
	private function quickLogin($phone, $password) {

				$loginperm = $this->_logincheck();
		if ( ! $loginperm )
		{
			exit('累计 5 次错误尝试，15 分钟内您将不能登录。');
		}

		
		$loginR = logic('account')->Login($phone, $password, ($_POST['keeplogin'] == 'on'));

				if ($loginR['error'])
		{
			$this->_loginfailed($loginperm);

			if ($loginR['result'] == '无法登录，用户不存在，您可以有至多 5 次尝试。') {

				$loginR['result'] = '账户不存在，请使用手机动态码方式登录，您可以有至多 5 次尝试。';

			}
			else if ($loginR['result'] == '无法登录，用户密码错误，您可以有至多 5 次尝试。') {

				$loginR['result'] = '密码错误，您可以有至多 5 次尝试。';

			}

			exit($loginR['result']);
		}

		exit('登录成功');

	}
}
?>