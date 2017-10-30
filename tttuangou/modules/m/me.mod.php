<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name me.mod.php
 * @date 2016-08-16 17:36:22
 */





class ModuleObject extends MasterObject
{
    var $city;
    var $config;
    function ModuleObject( $config )
    {
        $this->MasterObject($config);         if (MEMBER_ID < 1)
        {
            $this->Messager(__('请先登录！'), '?mod=account&code=login');
        }
        Load::logic('product');
        $this->ProductLogic = new ProductLogic();
        Load::logic('pay');
        $this->PayLogic = new PayLogic();
        Load::logic('me');
        $this->MeLogic = new MeLogic();
        Load::logic('order');
        $this->OrderLogic = new OrderLogic();
        $this->config = $config;
        $this->ID = ( int )($this->Post['id'] ? $this->Post['id'] : $this->Get['id']);
        $this->CacheConfig = ConfigHandler::get('cache');         $this->ShowConfig = ConfigHandler::get('show');         $runCode = Load::moduleCode($this, $this->Code);
        $this->$runCode();
    }
    
    function index(){
        $this->Title = __('个人中心');
        $conf = ini('credit_transfer');
                        if(MEMBER_ROLE_TYPE == 'seller'){
            $transfer = $conf['seller_transfer'];
        }else{
            $transfer = $conf['user_transfer'];
        }

        include handler('template')->file('@m/my_3g');
    }
    function Coupon()
    {
        $this->Title = __('我的自提券');
        $status = $this->Get['status'];
        if ($status == '')
        {
            $status = -1;
        }
        else
        {
            $status = (int)$status;
        }
        $ticket_all = logic('coupon')->GetList(user()->get('id'), ORD_ID_ANY, $status);

                $_s1=$_s2=$_s3= $_s4='';
        if($status==-1) $_s1='cur';
        if($status== 0) $_s2='cur';
        if($status== 1) $_s3='cur';
        if($status== 2) $_s4='cur';

        include handler('template')->file('@m/my_coupon');
    }
    
    function Order()
    {
		$this->Title = __('我的订单');
        $pay = $this->Get['pay'];
		$pid = (int)$this->Get['pid'];
		$comment = $this->Get['comment'];
        if ($pay == '')
        {
            $pay = -1;
        }
        else
        {
            $pay = (int)$pay;
        }
		if(!in_array($comment,array('1','2'))){
			$comment = '';
		}
		if((int)$this->Get['refund'] == 1){
			$roids = array();
			$refund_ids = dbc(DBCMax)->select('refund')->in('orderid')->where(array('uid'=>user()->get('id')))->done();
			if($refund_ids){
				foreach($refund_ids as $ok => $ov){
					$roids[] = $ov['orderid'];
				}
				$order_all = logic('order')->GetList(user()->get('id'), -1, -1, 'orderid IN('.implode(',',$roids).') AND productid!="99999" ');
			}
		}elseif($comment){
			if($comment == 2){
				$order_all = logic('order')->GetList(user()->get('id'), -1, 1, " comment IN('2','3') AND productid!='99999' ");
			}else{
				$order_all = logic('order')->GetList(user()->get('id'), -1, 1, " comment ='".$comment."' AND productid!='99999' ");
			}
		}elseif($pid > 0){
			$order_all = logic('order')->GetList(user()->get('id'), -1, 1, " comment > 0 and productid = '".$pid."' ");
		}else{
			$order_all = logic('order')->GetList(user()->get('id'), -1, $pay, " ( `productid`!='99999' OR (`productid`='99999' AND `pay`!=0) ) ");
		}

        $oids = '';
		if($order_all){
			foreach ($order_all as $key => $value) {
				$oids .= '\''.$value['orderid'].'\',';
				if($value['expresstype']){
					$order_all[$key]['express'] = logic('express')->OrderToExpress($value['expresstype']);
				}
				if($value['process'] == 'TRADE_FINISHED'){
					$order_all[$key]['tickets'] = logic('coupon')->GetList(user()->get('id'), $value['orderid'], -1);
				}
				if($value['product']['type'] == 'ticket'){
					$coupons = logic('coupon')->SrcList(user()->get('id'), $value['orderid']);
					if (count($coupons) === 0) {
						$order_all[$key]['cannot_refund'] = true;
					}
				}
                                if($value['productid']==99999 && $value['sid']){
                    $seller = logic('seller')->GetOne($value['sid']);
                    $order_all[$key]['sellername'] = $seller['sellername'];
                }

                if($value['sid'] > 0 && '99999'==$value['productid']) {
                    $seller = logic('seller')->GetOne($value['sid']);
                    $order_all[$key]['sellerimgs'] = $seller['imgs'];
                    $order_all[$key]['sellername'] = $seller['sellername'];
                }
			}
		}
        $oids = trim($oids,',');
		if($oids){
        $r_list = dbc(DBCMax)->select('refund')->where("orderid in ({$oids})")->done();
		}
        if ($r_list) {
            foreach ($r_list as $k => $v) {
                foreach ($order_all as $key => $value) {
                    if ($v['orderid'] == $value['orderid']) {
                        $order_all[$key]['refund_process'] = $v['process'];
						$order_all[$key]['refund_demand_reason'] = $v['demand_reason'];
						$order_all[$key]['refund_op_money'] = $v['op_money'];
						$order_all[$key]['refund_demand_money'] = $v['demand_money'];
						$order_all[$key]['refund_op_reason'] = $v['op_reason'];
						$order_all[$key]['refund'] = true;
                    }
                }
            }
        }

                $_s1= $_s2 = $_s3 = $_s4 = $_s5 = $_s6 = 3;
		if((int)$this->Get['refund'] == 1){
			$_s4 = 2;
		}elseif($comment){
			if($comment==2 ) $_s5=2;
			if($comment==1 ) $_s6=2;
		}else{
			if($pay==-1) $_s1=2;
			if($pay==1 ) $_s2=2;
			if($pay==0 ) $_s3=2;
		}
        include handler('template')->file('@m/my_order');
    }
    
    function Bill()
    {
        $this->Title = __('收支明细');
        $uid = api_uid();
        if($uid < 1) {
            $uid = MEMBER_ID;
        }
        $user_money = user($uid)->get('money');
        $usermoney = logic('me')->money()->log($uid);
        include handler('template')->file('@m/my_bill');
    }
    
    function Setting()
    {
        $this->Title = __('账户设置');
        $user = user()->get();
        $user_phone = logic('phone')->view($user['phone']);
        include handler('template')->file('@m/my_setting');
    }
    function Phone() {
        $this->Title = __('手机号设置');
        $action = '?mod=me&code=phone&act=dobind';
        $user = user()->get();
        $user_phone = logic('phone')->view($user['phone']);
        $uid = $user['id'];
        $act = ($_GET['act'] ? $_GET['act'] : $_POST['act']);

        if(false == in_array($act, array('rebind', 'dorebind'))) {
            if($user['phone_validate'] && $user['phone']) {
                                $act = 'rebind';
            }
        } else {
            if(false == $user['phone_validate'] || empty($user['phone'])) {
                                $act = 'bind';
            }
        }

        $phone = post('phone');
        $vfcode = post('vfcode');
        if('dobind' == $act) {
            $ret = logic('phone')->Vfcode($phone, $vfcode, $uid);
            if($ret) {
                $this->Messager($ret, -1);
            } else {
                $this->Messager('绑定成功', '?mod=me&code=setting');
            }
        } elseif ('rebind' == $act) {
            $action = '?mod=me&code=phone&act=dorebind';

            $ret = logic('phone')->rebind2($uid);
            if($ret) {
                $this->Messager($ret, 0);
            }

            
            $message = "您现在绑定的手机号为 {$user_phone} ；请输入完整的手机号码";

        } elseif ('dorebind' == $act) {
            $mvfcode = post('mail_vfcode');

            $ret = logic('phone')->dorebind2($mvfcode, $phone, $vfcode, $uid);
            if($ret) {
                $this->Messager($ret, -1);
            } else {
                $this->Messager('绑定成功', '?mod=me&code=setting');
            }
        }
        include handler('template')->file('@m/my_phone');
    }
    
    function Address()
    {
        $this->Title = __('收货地址');
        $addressList = logic('address')->GetList(user()->get('id'));
        include handler('template')->file('@m/my_address');
    }


    function Cancel()
    {
                extract($this->Get);
        $this->OrderLogic->orderType($orderid, '0');
        $this->Messager("您已经成功取消该订单！", "?mod=me&code=order");
    }
    function Doinfo()
    {
        extract($this->Post);
        $ary = array();
        if ( $newpwd == $confirmpwd && $newpwd != '' )
        {
                        if ( true === UCENTER )
            {
                include_once (UC_CLIENT_ROOT . './client.php');
                $result = uc_user_edit(MEMBER_NAME, '', $newpwd, '', 1);
                if($result ==0 || $result ==1)
				{
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

                        $ary['password'] = account()->password($newpwd, MEMBER_ID);
			$ary['secques'] = random(24);
        }

        $sql = 'select `email` from ' . MEMBER_TABLE_PREFIX . 'system_members where uid = ' . MEMBER_ID;
        $query = $this->DatabaseHandler->Query($sql);
        $user = $query->GetRow();
        if ($qq == '' || is_numeric($qq))
        {
            $ary['qq'] = $qq;
        }
        else
        {
            $this->Messager('QQ号码输入不正确，请重新输入！');
        }
        if ( $user['email'] != $email )
        {
            if (check_email($email))
            {
                $ary['email'] = $email;
            }
            else
            {
                $this->Messager('邮件地址不正确，请重新输入！');
            }
            if ( $this->config['default_emailcheck'] )
            {
                $ary['checked'] = 0;
            }
            if (logic('account')->Exists('mail', $email))
            {
                return $this->Messager('Email 地址已经被使用！');
            }
        }

        $this->DatabaseHandler->SetTable(MEMBER_TABLE_PREFIX . 'system_members');
        $result = $this->DatabaseHandler->Update($ary, 'uid = ' . MEMBER_ID);

                if ($from[1] == 'qq') {
            account()->login(MEMBER_NAME, md5($newpwd), true);
        }

        $this->Messager("资料修改成功！", "?mod=me&code=setting");
    }
    function Printticket()
    {
        extract($this->Get);
        $order = $this->OrderLogic->GetTicket($id);
        $pwd = $order['password'];
        if ( $order == '' || $pwd == '' ) $this->Messager("读取" . TUANGOU_STR . "券出现错误！", "?mod=me");
        include (template("tttuangou_printticket"));
    }

    function Addmoney()
    {
		exit();
        $money = $this->MeLogic->moneyMe();
        $pay = $this->PayLogic->payType(intval($id), $this->city);
        $action = '?mod=me&code=doaddmoney';
        include (template("tttuangou_addmoney"));
    }
    function Topay( $mod, $returnurl, $pay )
    {
		exit();
        $payment = unserialize($pay['pay_config']);
        $returnurl .= '&pay=' . $mod;
        include_once ('./modules/' . $mod . '.pay.php');
        $bottom = payTo($payment, $returnurl, $pay);
        return $bottom;
    }
    function Doaddmoney()
    {
		exit();
        $this->Post['money'] = round($this->Post['money'], 2);
        if ( $this->Post['paytype'] == '' ) $this->Messager("您没有选择支付方式或者没有适合的支付接口！");
        if ( ! is_numeric($this->Post['money']) || $this->Post['money'] <= 0 ) $this->Messager("充值金额必须大于0！");
        $pay = $this->PayLogic->payChoose(intval($this->Post['paytype']));
        $pay['orderid'] = time() . rand(10000, 99999);
        $pay['price'] = $this->Post['money'];
        $pay['name'] = '账户充值';
        $pay['show_url'] = $this->Config['site_url'] . '/?mod=me&code=money';
        $returnurl = $this->Config['site_url'] . '/index.php?mod=me&code=readdmoney';
        $bottom = $this->Topay($pay['pay_code'], $returnurl, $pay);
        include (template('@m/tttuangou_doaddmoney'));
    }
    function Readdmoney()
    {
		exit();
        $pay_code = (isset($_POST['pay']) ? $_POST['pay'] : $_GET['pay']);
        if ( $pay_code == '' )
        {
            $this->Messager('参数传递错误！');
        }
        if ( isset($_GET['pay']) )
        {
            $is_notify = false;
            $userID = MEMBER_ID;
        }
        elseif ( isset($_POST['pay']) )
        {
            $is_notify = true;
            $userID = $_POST['uid'];
        }
        include_once ('./modules/' . $pay_code . '.pay.php');
        $msg = addmymoney($userID);
        $oid = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : $_POST['out_trade_no'];
        $trade_status = isset($_GET['trade_status']) ? $_GET['trade_status'] : $_POST['trade_status'];
        $pay = $this->PayLogic->payChoose($pay_code);
        $pay = unserialize($pay['pay_config']);
        $ifTrust = true;
        if ( $pay['alipay_iftype'] == 'distrust' )
        {
            $ifTrust = false;
        }
        if ( is_array($msg) )
        {
            if ( $is_notify )
            {
                $trade_no = $_POST['trade_no'];
                if ( $ifTrust || $trade_status == 'TRADE_FINISHED' )
                {
                                        $this->Dopay($msg['price'], $msg['orderid'], $userID, $trade_no);
                }
                if ( ! $ifTrust && $trade_status == 'WAIT_SELLER_SEND_GOODS' )
                {
                                        $url = sendGoods($trade_no, $pay);
                    $doc = new DOMDocument();
                    $doc->load($url);
                                     }
                exit('success');
            }
            if ( $pay_code != 'alipay' )
            {
                if ( $pay_code == 'tenpay' )
                {
                    $trade_no = $_REQUEST['transaction_id'];
                }
                elseif ( $pay_code == 'kuaiqian' )
                {
                    $trade_no = $_REQUEST['dealId'];
                }
                $result = $this->Dopay($msg['price'], $msg['orderid'], $userID, $trade_no);
                $this->Messager($result, '?mod=me&code=money');
            }
            else
            {
                if ( ! $ifTrust && $trade_status != 'TRADE_FINISHED' )
                {
                                        $this->Messager('支付还没有完成，请您先确认收货，之后系统便会自动完成本次交易！', 'http:/' . '/lab.alipay.com/consume/record/buyerConfirmTrade.htm?tradeNo=' . $_GET['trade_no'], 5);
                }
            }
            $this->Messager('充值成功！', '?mod=me&code=money');
        }
        else
        {
            if ( $is_notify )
            {
                exit('success');
            }
                        if ( $pay_code == 'alipay' && $msg == '您不能重复充值同一订单，充值失败！' )
            {
                $this->Messager('充值成功！', '?mod=me&code=money');
            }
            $this->Messager($msg, '?mod=me&code=money');
        }
        ;
    }
    function Dopay( $price, $orderid, $userID, $trade_no )
    {
		exit();
        if ( $price == '' || $orderid == '' )
        {
            return "支付失败!";
        }
        ;
                if ( $price > 0 )
        {
            $pay = $this->MeLogic->moneyAdd($price, $userID);
            $ary = array(
                'userid' => $userID, 'type' => 1, 'name' => '直接充值', 'intro' => '您为账户充值' . $price . '元', 'money' => $price, 'time' => time(), 'trade_no' => $trade_no
            );
            $this->MeLogic->moneyLog($ary);
        }
        ;
                $ary = array(
            'id' => $orderid, 'money' => $price, 'userid' => $userID, 'paytime' => date('Y-m-d H:i:s')
        );
        $this->DatabaseHandler->SetTable(TABLE_PREFIX . 'tttuangou_addmoney');
        $result = $this->DatabaseHandler->Insert($ary);
        return '充值成功！';
    }

    function Face()
    {
		exit();
        $sql = 'select ucuid from ' . MEMBER_TABLE_PREFIX . 'system_members where uid = ' . MEMBER_ID;
        $query = $this->DatabaseHandler->Query($sql);
        $usr = $query->GetRow();
        if ( UCENTER )
        {
            include_once (UC_CLIENT_ROOT . './client.php');
            $face = uc_avatar($usr['ucuid']);
        }
        else
        {
            ;
        }
        include (template("tttuangou_face"));
    }

    
    function __AddressCheckOns( $id )
    {
        return false;
        $sql = 'SELECT COUNT(orderid) AS CNT FROM ' . TABLE_PREFIX . 'tttuangou_order WHERE addressid=' . $id . ' AND status IN(1,4)';
        $query = $this->DatabaseHandler->Query($sql)->GetRow();
        if ( $query['CNT'] > 0 )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function favorite()
    {
		$this->Title = __('我的收藏');
        $status = $this->Get['status'];
        if ($status == '')
        {
            $status = -1;
        }
        else
        {
            $status = (int)$status;
        }
        $timestamp = time();
        $list = logic('favorite')->get_list($status);

                $_s1=$_s2=$_s3=$_s4=3;
        if($status==-1) $_s1=2;
        if($status== 0) $_s2=2;
        if($status== 1) $_s3=2;
		if($status== 2) $_s4=2;
        include handler('template')->file('@m/my_favorite');
    }

    function favorite_cancle(){
        $product_id = get('id', 'int');
        MEMBER_ID == 0 && exit(0);
        $product_id > 0  && logic('favorite')->delete($product_id);
        $this->Messager(__('成功删除收藏！'), '?mod=me&code=favorite');
    }

    function favorite_add(){
        $product_id = get('id', 'int');
        MEMBER_ID == 0 && exit(0);
        $product_id > 0  && logic('favorite')->create($product_id);
        exit('已收藏');
    }

    function rebate(){
        $rebate_list = logic('rebate')->get_list_for_me(MEMBER_ID);
		$total_m = 0;
        if( $rebate_list ){
            foreach ($rebate_list as $k => &$v) {
                $v['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
				$v['type'] = $v['type'] == 'buy' ? '兑换返利' : '卖出返利';
				$v['salary_pre'] = $v['salary_pre'].'%';
                $v['orderid'] = preg_replace("/^(\d{4}).*(\d{2})$/i", "\$1*****\$2", $v['orderid']);
				$total_m += $v['salary_money'];
            }
        }
        include handler('template')->file('@m/my_rebate');
    }

	function credit(){
        $credit_list = logic('credit')->get_list_for_me(MEMBER_ID);

		$total_credit = user()->get('scores');

        $this->Title = '积分记录';

        include handler('template')->file('@m/my_credit');
    }

	function ajaxscore(){
		$pid = get('pid','int');
		$uid = get('uid','int');
		$share = get('share','txt');
		if($pid > 0 && $uid > 0 && $uid == user()->get('id') && $share){
			logic('credit')->add_score($pid,$uid,0,'forward',$share);		}
	}

    function growth(){

                $growth= user()->get('total_cost');
        $level = ini('credits.level');
        $level = explode(',', $level);
        $last_level = 0;
        $vips = 0;
        if ($level) {
            foreach ($level as $key => $value) {
                $last_level = $value;
                if ($growth > $value) {
                    $vips = $key+1;
                }
            }
        }
                        $nextlevel = $vips ;
        $distance  = $level[$nextlevel] - (int)$growth;
    }





	
    function ask(){
        $this->Title = __('在线问答');
        $action = '?mod=me&code=doask';
        include handler('template')->file('@m/ask_3g');
    }

    
    function doask(){
        $question = post('question', 'txt');
        if ( MEMBER_ID < 1 ) $this->Messager(__('您必须先登录才能发表您的提问！'));
        if ( $question == '' ) $this->Messager(__('问题不可以为空哦！'));
        if ( $a = filter($question) ) $this->Messager($a);
        $ary = array(
            userid => MEMBER_ID, username => MEMBER_NAME, content => $question, time => time()
        );
        $this->DatabaseHandler->SetTable(TABLE_PREFIX . 'tttuangou_question');
        $result = $this->DatabaseHandler->Insert($ary);
        $ary['time'] = date('Y-m-d H:i:s', $ary['time']);
        notify(MEMBER_ID, 'list.ask.new', $ary);
        $this->Messager(__("提问成功，请等待管理员的回复！"), "?mod=me&code=ask");
        exit();
    }


	
    function invite(){
        $this->Title = __('邀请有奖');
        include handler('template')->file('@m/invite_3g');
    }


	
    function transfer(){
        $this->Title = __('积分转赠');

        
        $userid = api_uid() ? api_uid() : MEMBER_ID;
        if($userid < 1) {
            $this->Messager('请先登录');
        }
        $user = user($userid)->get();
        $seller = logic('seller')->GetOne(null, $userid);
        $conf = ini('credit_transfer');
                if($seller){
            $proportion = $conf['seller_transfer'];        }else{
            $proportion = $conf['user_transfer'];
        }
        if(false == $user) {
            $this->Messager('用户已经不存在了');
        }
        $conf = ini('credit_transfer');
        if($conf['open_credit_transfer'] == 0) {
            $this->Messager('网站后台未开启积分转赠功能');
        }
        $act = ($_GET['act'] ? $_GET['act'] : $_POST['act']);
        $do = ($_GET['do'] ? $_GET['do'] : $_POST['do']);
        $act = in_array($act, array('step1', 'step2', 'step3', )) ? $act : 'step1';
                if('step1' == $act){

        }
        if('step2' == $act) {
            $to_phone = get('to_phone', 'number')?get('to_phone', 'number'):post('to_phone');

            $give_credit = get('give_credit', 'number')?(int)get('give_credit', 'number'):(int)post('give_credit');

                        $sql = 'SELECT * FROM '.table('members').' WHERE phone='.$to_phone;
            $other_user = dbc()->Query($sql)->GetRow();
            $have_phone = $other_user ? 1 : 0;
                        if($have_phone == 1){
                $user_get = $give_credit * (100-$proportion)/100;
            }else{
                                if(post('vfcode')){
                    $vfcode = post('vfcode');

                                                                                                $rets = logic('account')->RegisterPhone($to_phone, $vfcode, $vfcode, 0, false);
                        if($rets['error']) {
                            exit($rets['result']);
                        }
                        $this->Messager('新建完成！', '?mod=me&code=transfer&act=step2&to_phone='.$to_phone. '&give_credit='.$give_credit);

                }
            }
            if($do){
                $password = post('password');
                if(md5($password) != $user['password']) {
                    $error_msg = '您输入密码错误，请重试！';
                }else{
                                        logic('credit')->add_score(0,$userid,$give_credit,'give_credit','','','',$proportion,$other_user['uid']);
                    logic('credit')->add_score(0,$other_user['uid'],$give_credit,'get_credit','','','',$proportion,$userid);
                    $this->Messager('转赠完成！', '?mod=me&code=bill');
                }
            }
        }
        include handler('template')->file('@m/transfer_integral');
    }

    public function check_credit(){
        $userid = $_GET['userid'];
        $user = user($userid)->get();
        $give_credit = get('give_credit', 'number')?(int)get('give_credit', 'number'):(int)post('give_credit');
        $error = false;
        if($give_credit < 0) {
            $error = '输入的积分数为非0整数';
        } elseif($user['scores'] < $give_credit){
            $error = '输入的积分必须小于等于用户当前积分';
        }
        echo $error;exit;
    }
    
    public function vfsend() {
                $phone = post('to_phone', 'txt');

        

        
        if(true == logic('seccode')->verify(post('seccode'))) {
            $ret = logic('phone')->VfSend($phone, false);
        } else {
            $ret = '请输入正确的图片验证码！';
        }
        exit($ret);
    }

    public function yungou() {
        $this->Title = __('我的云购');

        $yungou_list = logic('yungou')->getYunGouList_byUid_withinPage(MEMBER_ID);

        include template('@m/my_yungou');
    }


}
?>