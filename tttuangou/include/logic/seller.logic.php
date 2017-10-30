<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name seller.logic.php
 * @date 2016-09-19 17:35:14
 */





class SellerLogic
{
	public $price_filtes = array(
		0 => array(
        	'name' => '全部',
			'sql' => ' 1 ',
        ),
		1 => array(
        	'name' => '20元以下',
			'sql' => ' s.price_avg <= 20 ',
        ),
		2 => array(
        	'name' => '20-50 元',
			'sql' => ' s.price_avg >= 20 AND s.price_avg <= 50 ',
        ),
		3 => array(
        	'name' => '50-80 元',
			'sql' => ' s.price_avg >= 50 AND s.price_avg <= 80 ',
        ),
		4 => array(
        	'name' => '80-120 元',
			'sql' => ' s.price_avg >= 80 AND s.price_avg <= 120 ',
        ),
		5 => array(
        	'name' => '120-200 元',
			'sql' => ' s.price_avg >= 120 AND s.price_avg <= 200 ',
        ),
		6 => array(
        	'name' => '200-500 元',
			'sql' => ' s.price_avg >= 200 AND s.price_avg <= 500 ',
        ),
		7 => array(
        	'name' => '500元以上',
			'sql' => ' s.price_avg >= 500 ',
        ),
	);

	public function price_navigate()
    {
    	$price = get('price', 'int');
    	$price_navs = array();
    	foreach($this->price_filtes as $k=>$r) {
    		$r['title'] = ($r['title'] ? $r['title'] : $r['name']);
    		$r['url'] = logic('url')->create('seller', array('price' => $k));
    		$r['selected'] = ($price == $k ? true : false);
    		$price_navs[$k] = $r;
    	}
        include handler('template')->file('@html/seller/price_navigate');
    }
    public function price_sql_filter()
    {
    	$price = get('price', 'int');
    	$price = isset($this->price_filtes[$price]) ? $price : 0;
    	return $this->price_filtes[$price]['sql'];
    }

	
	function GetList( $cid = -1, $category = '1', $up_sid = 0 )
	{
		$sql_limit_city = '1';
		if ( $cid > 0 )
		{
			$sql_limit_city = ' ( s.area = ' . (int) $cid . ' ) ';
		}
		$sql_limit_upseller = '1';
		$up_sid = (int) $up_sid;
		if($up_sid > 0) {
			$sql_limit_upseller = ' ( s.id = ' . $up_sid . ' OR s.up_sellerid = ' . $up_sid . ' ) ';
		}
		$sql = 'SELECT s.*' . $this->get_list_ext_sql() . '
		FROM
			' . table('seller') . ' s
		WHERE
			' . $sql_limit_city . '
		AND
			' . $sql_limit_upseller . '
		AND
			' . $category . '
		AND
			' . logic('city')->seller_sql_filter() . '
		AND
			' . logic('isearcher')->seller_sql_filter() . '
		AND
			' . $this->price_sql_filter() . '
		AND
			`enabled` = "true"
		ORDER BY
			' . logic('sort')->seller_sql_filter();
		logic('isearcher')->Linker($sql);
		$sql = page_moyo($sql);
		return dbc(DBCMax)->query($sql)->done();
	}

	
	public function GetOne($sid = null, $uid = null, $city = null)
	{
		$sql_filter = '1';
		is_null($sid) || $sql_filter .= ' AND id='.(int) $sid;
		is_null($uid) || $sql_filter .= ' AND userid='.(int) $uid;
		is_null($city) || $sql_filter .= ' AND area='.(int) $city;
		$sql = 'SELECT * FROM '.table('seller').' WHERE '.$sql_filter.' LIMIT 1';
		return dbc()->Query($sql)->GetRow();
	}

	
	
	public function Add($city, $userid, $sellername = '合作商家', $sellerphone = '', $selleraddress = '', $sellerurl = '', $map = '', $rebate = array())
	{
		extract($rebate);
		$userid = (int) $userid;
		$data = array(
			'userid' => intval($userid),
			'sellername' => strip_tags($sellername),
			'sellerphone' => strip_tags($sellerphone),
			'selleraddress' => strip_tags($selleraddress),
			'sellerurl' => $sellerurl,
			'sellermap' => $map,
			'area' => $city,
			'time'=> time(),
			'profit_id' =>$profit_id,
			'profit_pre' =>$profit_pre,
			'sell_pre' =>$sell_pre,
			'home_uid' =>$home_uid,
			'enabled' => $enabled,
			'city_place_region' => (int) $city_place_region,
			'city_place_street' => (int) $city_place_street,
			'imgs' => trim($imgs, ','),
			'price_avg' => $price_avg,
			'category' => $category,
			'trade_time' => strip_tags($trade_time),
			'content' => $content,
			'display_order' => (int) $display_order,
			'maptype' => 'baidu',
            'assure_money' => $assure_money,
            'pay_into' => (float) $pay_into,
            'send_points' => $send_points,
            'up_sellerid' => $this->up_sellerid($up_sellerid),
		);
		if($userid != 1){
			dbc(DBCMax)->update('members')->data(array('role_id'=>'6','role_type'=>'seller','privs'=>''))->where('uid='.(int)$userid)->done();
		}
		dbc()->SetTable(table('seller'));
		$ret = dbc()->Insert($data);

		$this->sub_sellercount($data['up_sellerid']);

		return $ret;
	}
	public function Join($in){
		$fields = array('userid','area','city_place_region','city_place_street','category','sellername','selleraddress','sellerphone','sellerurl','sellermap','profit_id','id_card','zhizhao');
		foreach($fields as $f){
			$data[$f] = strip_tags($in[$f]);
		}
		$rebate = logic('rebate')->Get_Rebate_setting();
		$profit_id = $data['profit_id'];
		$profit_id = $profit_id?$profit_id:0;
		if( $rebate['profit'][$profit_id] && $rebate['profit'][$profit_id]['pre'] ){
			$data['profit_pre'] = $rebate['profit'][$profit_id]['pre'];
		}
		$data['time'] = time();
				$data['enabled'] = 'false';
						return dbc(DBCMax)->insert('seller')->data($data)->done();
	}
	
	public function Register($username, $password)
	{
				$userExists = account()->Exists('username', $username);
		if ($userExists)
		{
			$user = account()->Search('username', $username, 1);
						if ($user['role_type'] == 'admin' && $user['uid'] != 1)
			{
				return array('error' => true, 'result' => '此用户是管理员，请换一个！');
			}
						$seller = logic('seller')->GetOne(null, $user['id']);
			if ($seller)
			{
				return array('error' => true, 'result' => '此用户已经是商家，请换一个！');
			}
			$uid = $user['id'];
		}
		else
		{
						if ($password == '')
			{
				return array('error' => true, 'result' => '密码不能为空！');
			}
			$mail = logic('misc')->rndString().'@'.str_replace(array('www.', ':'), '', strtolower(ini('settings.site_domain')));
			$rr = account()->Register($username, $password, $mail);
			if ($rr['error'])
			{
				return array('error' => true, 'result' => $rr['result']);
			}
			$uid = $rr['result'];
						account()->Validated($uid);
						user($uid)->set('role_type', 'seller');
		}
		return array('error' => false, 'result' => $uid);
	}
	
	public function U2SID($uid = null)
	{
		static $U2SIDS = array();

		is_null($uid) && $uid = user()->get('id');
		if ($uid < 0) {
			return $uid;
		}

		if(isset($U2SIDS[$uid])) {
			return $U2SIDS[$uid];
		}

		$seller = $this->GetOne(null, $uid, null);
		$sid = ($seller ? $seller['id'] : -1);
		$U2SIDS[$uid] = $sid;
		return $sid;
	}
		public function setmembertype($sid = 0, $status = 'true'){
		$sellerinfo = dbc(DBCMax)->query('select userid from '.table('seller').' where id='.$sid)->limit(1)->done();
		if($sellerinfo['userid'] > 1){			if($status == 'true'){
				dbc(DBCMax)->update('members')->data(array('role_type'=>'seller','role_id'=>'6'))->where('uid='.(int)$sellerinfo['userid'])->done();
			}else{
				dbc(DBCMax)->update('members')->data(array('role_type'=>'normal','role_id'=>'0'))->where('uid='.(int)$sellerinfo['userid'])->done();
			}
		}
	}
	
	public function AVParser(&$product)
	{
		if ( ! $product ) return false;
		if ( is_array($product[0]) )
		{
			$returns = array();
			foreach ( $product as $i => &$one )
			{
				$this->AVParser($one);
			}
			return;
		}
		$pid = $product['id'];
		$product['views']['sells_real'] = $product['sells_real'];
		if ($product['type'] == 'ticket')
		{
						$product['views']['tikCount'] = array(
				'TICK_STA_Used' => logic('coupon')->Count(null, $pid, null, TICK_STA_Used),
				'TICK_STA_Unused' => logic('coupon')->Count(null, $pid, null, TICK_STA_Unused),
				'TICK_STA_Overdue' => logic('coupon')->Count(null, $pid, null, TICK_STA_Overdue),
				'TICK_STA_Invalid' => logic('coupon')->Count(null, $pid, null, TICK_STA_Invalid)
			);
			$product['views']['sells_real'] = array_sum($product['views']['tikCount']);
		}
		elseif ($product['type'] == 'stuff')
		{
			$product['views']['delivery'] = array(
				'sended' => logic('delivery')->Count($pid, DELIV_SEND_Yes),
				'waiting' => logic('delivery')->Count($pid, DELIV_SEND_No),
				'finished' => logic('delivery')->Count($pid, DELIV_SEND_OK)
			);
			$product['views']['sells_real'] = array_sum($product['views']['delivery']);
		} elseif ('prize' == $product['type']) {
			$product['views']['sells_real'] = logic('prize')->allCount($product['id']);
		}
		$fbase = 'productid='.$pid.' AND pay='.ORD_PAID_Yes.' AND status='.ORD_STA_Normal;
		$product['views']['money'] = array(
			'all' => logic('order')->Summary($fbase),
			'real' => 0
		);
		if ($product['type'] == 'ticket')
		{
			$product['views']['money']['real'] = logic('coupon')->Summary($pid);
		}
		elseif ($product['type'] == 'stuff')
		{
			$product['views']['money']['real'] = logic('order')->Summary($fbase.' AND process IN("WAIT_BUYER_CONFIRM_GOODS","TRADE_FINISH")');
		}
	}

	
	public function get_list_ext_sql($sql = null)
	{
		static $ssql = '';
		if (is_null($sql))
		{
			$sql = $ssql;
			$ssql = '';
			return $sql;
		}
		else
		{
			return $ssql = $sql;
		}
	}

		public function money_add($sid, $money)
	{
		dbc(DBCMax)
			->update('seller')
			->data('money = money + '.(float) $money)
			->where('id='. (int) $sid)
		->done();
	}
	public function money_less($sid, $money)
	{
		dbc(DBCMax)
			->update('seller')
			->data('money = money - '.(float) $money)
			->where('id='. (int) $sid)
		->done();
	}
	public function order_success($sid)
	{
		dbc(DBCMax)
			->update('seller')
			->data('successnum = successnum + 1')
			->where('id='. (int) $sid)
		->done();
	}
	public function order_failed($sid)
	{
		dbc(DBCMax)
			->update('seller')
			->data('successnum = successnum - 1')
			->where('id='. (int) $sid)
		->done();
	}
	public function product_add($sid)
	{
		dbc(DBCMax)
			->update('seller')
			->data('productnum = productnum + 1')
			->where('id='. (int) $sid)
		->done();
	}
	public function product_del($sid)
	{
		dbc(DBCMax)
			->update('seller')
			->data('productnum = productnum - 1')
			->where('id='. (int) $sid)
		->done();
	}

	
	public function maplocation4google2sogou($w, $sellerid)
	{
		$seller = $this->GetOne($sellerid);
		$map = $seller['sellermap'];
		list($x,$y, $z) = explode(',', $map);
		if ($x && $y && $z)
		{
			$r = dfopen('http:/'.'/api.go2map.com/engine/api/translate/xml?points='.$y.','.$x.'&type=2');
			if (stristr($r, '<status>error</status>') || !stristr($r, '<status>ok</status>'))
			{
				return 'false';
			}
			preg_match_all('/<x>(.*?)<\/x>/i', $r, $m);
			$x = $m[1][0];
			preg_match_all('/<y>(.*?)<\/y>/i', $r, $m);
			$y = $m[1][0];
			$map_new = $x.','.$y.','.$z;
			dbc(DBCMax)->update('seller')->data('sellermap="'.$map_new.'"')->where('id='.(int)$sellerid)->done();
			return 'ok';
		}
		else
		{
			return 'false';
		}
	}

	
	public function proceeds($seller_userid, $userid, $money) {
		$seller_userid = (int) $seller_userid;
		$userid = (int) $userid;
		$money = (float) $money;
		if($seller_userid > 0 && $userid > 0 && $money > 0) {
			$user = user($userid)->get();
			$seller = $this->GetOne(null, $seller_userid);
            			if($user && $seller) {
				logic('me')->money()->less($money, $userid, array(
					'name' => '商家收款',
					'intro' => '在商家（'.$seller['sellername'].'）消费了 <b>&yen;'.$money.'元</b>！'
				));

				$smoney = $money;
				$intro = '会员（'.$user['username'].'）消费了 <b>&yen;'.$money.'元</b>！';
				$profit_pre = (float) $seller['profit_pre'];
				if($profit_pre > 0) {
					$pmoney = round($money * $profit_pre / 100, 2);
					$intro .= '<br>站长分成 <b>' . $profit_pre . '%（&yen;'.$pmoney.'元）</b>！';
					$smoney = round($money - $pmoney, 2);
				}
				logic('me')->money()->seller_money_add($smoney, $seller_userid, array(
					'name' => '商家收款',
					'intro' => $intro
				));

								logic('rebate')->Add_RebateValue_For_Buyer($userid, $money);
			}
		}
	}


	
	public function up_sellerid($up_sid, $sid = 0) {
		$up_sid = (int) $up_sid;
		if($up_sid < 1) {
			return 0;
		}
		$sid = (int) $sid;
		if($sid > 0 && $sid == $up_sid) {
			return 0;
		}
		$up_s = $this->GetOne($up_sid);
		if($up_s && false == $up_s['up_sellerid']) {
			return $up_sid;
		}
		return 0;
	}

	
	public function sub_sellercount($up_sid, $update = true) {
		$up_sid = (int) $up_sid;
		if($up_sid && false != ($up_s = $this->GetOne($up_sid)) && false == $up_s['up_sellerid']) {
			$sql = "select count(1) as `count` from ".table('seller')." where `up_sellerid`='$up_sid'";
			$rs = dbc()->Query($sql)->GetRow();
			if($update && $rs['count'] != $up_s['sub_sellercount']) {
				dbc()->Query("update ".table('seller')." set `sub_sellercount`='{$rs['count']}' where `id`='$up_sid'");
			}
			return $rs['count'];
		}
		return 0;
	}

	public function sub_sellerid($pid) {
		;
	}

    
    public function branchinfo($up_sid){
        $rs = dbc(DBCMax)->select('seller')->where(array('up_sellerid'=>$up_sid))->done();
        return $rs;
    }

    public function GetSellerAds($sellerid){
        $rs = dbc(DBCMax)->select('seller_ads')->where(array('sellerid'=>$sellerid))->done();
        return $rs;
    }

    
    public function area_seller($city,$city_place_region=0){
        if($city_place_region != 0){
            $rs = dbc(DBCMax)->query('select id from '.table('seller')." where area='".$city."' and city_place_region='".$city_place_region."'")->done();
        }else{
            $rs = dbc(DBCMax)->query('select id from '.table('seller')." where area='".$city."'")->done();
        }
        if($rs){
            foreach($rs as $key => $val){
                $sids[] = $val['id'];
            }
        }
        if($sids){
            $ssids = implode(',',$sids);
        }
        return $ssids;
    }
}