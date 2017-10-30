<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name product.logic.php
 * @date 2016-09-19 17:35:14
 */





class ProductLogic
{
	
	public function display($category = '1')
	{
		if(count($_GET)==1 && $_GET['mod'] == 'index' && 'index' == WEB_BASE_ENV_DFS::$APPNAME){
			define('INDEX_DEFAULT', TRUE);
		}else{
			define('INDEX_DEFAULT', false);
		}
		$productID = get('view', 'int');
		if ( false == $productID )
		{
			$mutiView = true;
			if (ini('ui.igos.pager'))
			{
			}
			else
			{
				$_GET[EXPORT_GENEALL_FLAG] = EXPORT_GENEALL_VALUE;
			}
			$actived = PRO_ACV_Yes;
			if(false != get('prize')) {
				$category .= ' AND p.`type`="prize" ';
				$actived = null;
			}
			if(($sellerid = get('sellerid', 'int')) > 0) {
				$category .= ' AND p.`sellerid`="' . $sellerid . '" ';
			}
			$product = logic('product')->GetList(logic('misc')->City('id'), $actived, $category);
			
		}
		else
		{
			$mutiView = false;
			$product = logic('product')->GetOne($productID);

						logic('recent_view')->add($productID);
		}
				$usePager = get('page', 'int') > 0 ? true : false;
		if ( !$usePager && false == $product )
		{
			return false;
		}
				if ( !$usePager && $mutiView && count($product) == 1 )
		{
			$mutiView = false;
			$product = $product[0];
			$product['product_link'] = $this->get_link_product($product['linkid']);
						$_GET['view'] = $product['id'];
		}
		else if (ini('ui.igos.dsper') && $mutiView && count($product) > 1)
		{
			logic('product')->reSort($product);
		}
	
				if ($mutiView) {
			$file = 'home';
		} else if ($product['yungou'] == 0) {					$file = 'detail';
		} else {
			$file = 'yungou_detail';
		}

		if(INDEX_DEFAULT === true && $mutiView && false == ini('ui.igos.oldindex')){
			$city_id = logic('misc')->City('id');
			$cache_id = 'default.catalog.procount.' . $city_id;
			$cache_product = fcache($cache_id, 1800);
			if($cache_product){
				$product = $cache_product;
			}else{
				$catalogs = logic('catalog')->Navigate(6);
				if($catalogs){
					foreach($catalogs as $key => $val){
						$val['navcate'] = logic('catalog')->Filter($val['flag'], 'product');
						$catalogs[$key]['product'] = logic('product')->GetList($city_id, PRO_ACV_Yes, $val['navcate'], $val['index_display_count']);
						if(!$catalogs[$key]['product']){
							unset($catalogs[$key]);
						}
					}
					$product = $catalogs;
				}
				fcache($cache_id, $product);
			}
		}
		return array(
			'mutiView' => $mutiView,
			'file' => $file,
			'product' => $product
		);
	}
		function GetNewList($limit=2, $ishot = false, $cid = null)
	{
		$cid = (int) (0 == (int) $cid ? logic('misc')->City('id') : $cid);
		$now = time();
		$where = '(display = '.PRO_DSP_Global.' OR (display = '.PRO_DSP_City.' AND city = '.$cid.'))
			AND begintime < '.$now.' AND (overtime > '.$now.' or overtime < 1)
			AND saveHandler = "normal"' .
			($ishot ? ' AND hotenabled = "true" ' : '');
		$order = ($ishot ? ' `order` DESC, ' : '');
		$sql = 'SELECT `id`,`name`,`flag`,`price`,`nowprice`,`img`, `linkid`,`virtualnum`,(`virtualnum`+`sells_count`) AS `sells_count`,
			`begintime`,`intro`,`type`,`overtime`,`maxnum`,`successnum`,`is_countdown`,`newbie_cut`,`client_cut`,`limit_level`,`rebate_money`,`yungou`,`promo_cut`,`score`
		FROM '.table('product').' WHERE '.$where.' ORDER BY '.$order.' `id` DESC LIMIT '.$limit;
        $product = dbc(DBCMax)->query($sql)->done();
		if($product) {
			foreach($product as $key => $val){
				$imgs = $val['img'] != '' ? explode(',', $val['img']) : null;
				$product[$key]['img'] = ($imgs && $imgs[0]) ? $imgs[0] : 0;
				$product[$key]['pic'] = imager($product[$key]['img'],IMG_Normal);
			}
			$product = $this->__parse_result($product);
		}
		return $product;
	}
        function GetHotList($limit=5, $ishot = false, $sellerid = null ,$cid = null)
    {
        $cid = (int) (0 == (int) $cid ? logic('misc')->City('id') : $cid);
        $now = time();
        $where = '(display = '.PRO_DSP_Global.' OR (display = '.PRO_DSP_City.' AND city = '.$cid.'))
			AND begintime < '.$now.' AND (overtime > '.$now.' or overtime < 1)
			AND saveHandler = "normal"' .
            ($ishot ? ' AND hotenabled = "true" ' : '');
        $order = ($ishot ? ' `sells_count` DESC, ' : '');
        $sql = 'SELECT `id`,`name`,`flag`,`price`,`nowprice`,`img`, `linkid`,`virtualnum`,(`virtualnum`+`sells_count`) AS `sells_count`,
			`begintime`,`intro`,`type`,`overtime`,`maxnum`,`successnum`,`is_countdown`,`newbie_cut`,`client_cut`,`limit_level`,`rebate_money`,`yungou`,`promo_cut`,`score`
		FROM '.table('product').' WHERE '.$where.' ORDER BY '.$order.' `id` DESC LIMIT '.$limit;
        $product = dbc(DBCMax)->query($sql)->done();
        if($product) {
            foreach($product as $key => $val){
                $imgs = $val['img'] != '' ? explode(',', $val['img']) : null;
                $product[$key]['img'] = ($imgs && $imgs[0]) ? $imgs[0] : 0;
                $product[$key]['pic'] = imager($product[$key]['img'],IMG_Normal);
            }
            $product = $this->__parse_result($product);
        }
        return $product;
    }
	
	function GetOne( $id, $cached = true )
	{
		$ckey = 'product.getone.'.$id;
		$list = $cached ? cached($ckey) : false;
		if ($list) return $list;
		$sql = 'SELECT p.*,s.sellername,s.sellerphone,s.selleraddress,s.sellerurl,s.sellermap,s.trade_time,s.userid AS selleruserid
		FROM
			' . table('product') . ' p
		LEFT JOIN ' . table('seller') . ' s
		ON
			(p.sellerid = s.id)
		WHERE
			p.id = ' . (int)$id;
		$data = $this->__parse_result( dbc(DBCMax)->query($sql)->limit(1)->done() );

		if( $data ){
			if( $data['begintime'] > time() ){
				$lasttime = $data['begintime'] - time();
				if( $lasttime > 86400 ){
					$data['begin_date'] = date('Y-m-d H:i:s',$data['begintime']);
				}else{
					$data['limit_time'] = $lasttime;
				}
			}
						if($data['is_countdown'] == 1){
				logic('order')->FreeCountDownOrder($id);
			}
			$data['product_link'] = $this->get_link_product($data['linkid']);
		}
		return cached($ckey, $data);
	}
	
	public function GetFirst()
	{
		$list = $this->GetList(logic('misc')->City('id'), PRO_ACV_Yes);
		return $list[0];
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
	
	function GetOwnerList( $sellerID = 0, $limit = null, $parse = false, $productid = 0 ){
		$sql_limit = '';
		if( !empty($limit) && (int)$limit>0 ){
			$sql_limit = ' LIMIT '.(int)$limit;
		}
		$sql = "SELECT `id`,`name`,`price`,`nowprice`,`img`,`virtualnum`,(`virtualnum`+`sells_count`) AS `sells_count`,`begintime`,`intro`,`type`,`overtime`,`maxnum`,`successnum`,`is_countdown`,`yungou`,`promo_cut`,`score`
		FROM `".table('product')."` WHERE `display`>0 AND `saveHandler`='normal' AND `sellerid`='".$sellerID."' AND (`overtime`>=".time()." OR `overtime`<1) 
		".($productid > 0 ? " AND `id`!='{$productid}' " : "")."
		ORDER BY `overtime` ASC ".$sql_limit;
        		$data = dbc(DBCMax)->query($sql)->done();
		if($data){
			foreach($data as &$v) {
				$v['imgs'] = $img = explode(',',$v['img']);
				$v['pic'] = imager($img[0],IMG_Normal);
											}
			if($parse) {
				$data = $this->__parse_result($data);
			}
		}
		return $data;
	}
    
    function GetAllTuangou( $sellerID = 0, $limit = null, $parse = false, $productid = 0,$extend = '1'){
        $sql_limit = '';
        if( !empty($limit) && (int)$limit>0 ){
            $sql_limit = ' LIMIT '.(int)$limit;
        }
        $sql = "SELECT `id`,`name`,`price`,`nowprice`,`img`,`virtualnum`,(`virtualnum`+`sells_count`) AS `sells_count`,`begintime`,`intro`,`type`,`overtime`,`maxnum`,`successnum`,`is_countdown`,`yungou`,`promo_cut`,`score`
		FROM `".table('product')."` WHERE `display`>0 AND `saveHandler`='normal' AND `sellerid`='".$sellerID."' AND (`overtime`>=".time()." or overtime<1)
		".($productid > 0 ? " AND `id`!='{$productid}' " : "")."
		ORDER BY `overtime` ASC ";
        $sql = page_moyo($sql);
        $data = dbc(DBCMax)->query($sql)->done();
        if($data){
            foreach($data as &$v) {
                $v['imgs'] = $img = explode(',',$v['img']);
                $v['pic'] = imager($img[0],IMG_Normal);
                                            }
            if($parse) {
                $data = $this->__parse_result($data);
            }
        }
        return $data;
    }
	
	function GetList( $cid = -1, $actived = null, $extend = '1', $limit = null, $sellerid=null)
	{
		$cid = (int) $cid;
		$sql_limit_city = '1';
		if ( $cid > 0 )
		{
			$sql_limit_city = '(p.display = '.PRO_DSP_Global.' OR (p.display = '.PRO_DSP_City.' AND p.city = ' . $cid . ') )';
		}
		$sql_limit_actived = '1';
		$now = time();
		if ( !is_null($actived) )
		{
			if ($actived === PRO_ACV_Yes)
			{
				$sql_limit_actived = 'p.begintime < ' . $now . ' AND (p.overtime > ' . $now . ' or p.overtime < 1)';
			}
			else
			{
				$sql_limit_actived = ' ( p.overtime > 0 AND p.overtime < ' . $now . ' ) '; 			}
		}

		$extend = ($extend ? $extend : 1);

		$sql = 'SELECT p.*,s.sellername,s.sellerphone,s.selleraddress,s.sellerurl,s.sellermap,p.totalnum'.$this->get_list_ext_sql();
		$sql .= ' FROM ' . table('product') . ' p LEFT JOIN ' . table('seller') . ' s ON (p.sellerid=s.id) WHERE ';
		$sql .= $sql_limit_actived . ' AND ' . $sql_limit_city . ' AND ' . $extend;

        if( !is_null($sellerid) ) {
            $sql .= ' AND p.`sellerid`="' . $sellerid . '" ';
        }

		$sql .= INDEX_DEFAULT === true ? '' : (' AND ' . logic('city')->place_sql_filter() . ' AND ' . logic('product_tag')->product_sql_filter() . ' AND ' . logic('isearcher')->product_sql_filter());
		$sql .= ' AND p.saveHandler = "normal" ORDER BY ';
		$sql .= (INDEX_DEFAULT === true ? 'p.order DESC, p.id DESC' : logic('sort')->product_sql_filter());
		if((INDEX_DEFAULT === true && false == ini('ui.igos.oldindex')) || null !== $limit){
			$limit = (int) $limit;
			if($limit < 0) {
				return array();
			}
			if(0 == $limit) {
				$limit = 6;
			}
			$sql .=  ' LIMIT ' . $limit;
		}else{
			logic('isearcher')->Linker($sql);
			$sql = page_moyo($sql);
		}
        $result = dbc(DBCMax)->query($sql)->done();
		return $this->__parse_result($result);
	}
	
	function GetOtherList( $city_id, $category, $selfid, $type = 0)
	{
		$now = time();
		$city_id = (int) $city_id;
		$catasql = '';
		$order = '`order`.desc,sells_count.desc';
		if($type) {
			$order = 'id.desc';
			$catasql = ' AND category = '.(int) $category;
		}
		$catasql = ($type ? ' AND category = '.(int) $category : '');
		$selfid = (int) $selfid;
		$sql_where = '(display = '.PRO_DSP_Global.' OR (display = '.PRO_DSP_City.' AND city = '.$city_id.'))'.$catasql.' AND begintime < '.$now.' AND (overtime > '.$now.' or overtime < 1) AND id != '.$selfid.' AND saveHandler = "normal"';
		$result = dbc(DBCMax)->select('product')->where($sql_where)->order($order)->limit(5)->done();
		if ( count($result) < 5 && $type)
		{
			$sql_where = '(display = '.PRO_DSP_Global.' OR (display = '.PRO_DSP_City.' AND city = '.$city_id.')) AND begintime < '.$now.' AND (overtime > '.$now.' or overtime < 1) AND id != '.$selfid.' AND saveHandler = "normal"';
			$result = dbc(DBCMax)->select('product')->where($sql_where)->order($order)->limit(5)->done();
		}
		return $this->__parse_result($result);
	}
	
	public function SrcOne($id)
	{
		return dbc(DBCMax)->select('product')->where('id='.(int)$id)->limit(1)->done();
	}
	
	public function Where($sql_limit)
	{
		$sql = 'SELECT * FROM '.table('product').' WHERE '.$sql_limit;
		return dbc(DBCMax)->query($sql)->done();
	}
	
	public function Update($id, $array)
	{
		$id = (int) $id;
		zlog('product')->update($id, $array);
		dbc()->SetTable(table('product'));
		if(isset($array['@extra'])) unset($array['@extra']);
		dbc()->Update($array, 'id = '.$id);
		fcache('default.catalog.procount', 0);
	}
	
	public function Update_direct($id, $array)
	{
		dbc()->SetTable(table('product'));
		if(isset($array['@extra'])) unset($array['@extra']);
		dbc()->Update($array, 'id = '.(int)$id);
		fcache('default.catalog.procount', 0);
	}
	
	public function Delete($id)
	{
		$id = (int) $id;
		$p = $this->SrcOne($id);
		zlog('product')->delete($id, $p);
				$imgs = explode(',', $p['img']);
		foreach ($imgs as $i => $iid)
		{
			logic('upload')->Delete($iid);
		}
				dbc(DBCMax)->delete('product')->where('id='.$id)->done();
				$sqls = array(
						'DELETE FROM '.table('finder').' WHERE productid='.$id,
						'DELETE FROM '.table('ticket').' WHERE productid='.$id,
						'DELETE FROM '.table('favorite').' WHERE pid='.$id,
		);
				$orderList = logic('order')->Where('productid='.$id);
		foreach ($orderList as $i => $order)
		{
			$oid = $order['orderid'];
						$sqls[] = 'DELETE FROM '.table('order').' WHERE orderid='.$oid;
						$sqls[] = 'DELETE FROM '.table('order_clog').' WHERE sign='.$oid;
						$sqls[] = 'DELETE FROM '.table('paylog').' WHERE sign='.$oid;
		}
				$sqls[] = 'DELETE FROM ' . table('yungou_canyu') . ' WHERE pid = ' . $id;
		$sqls[] = 'DELETE FROM ' . table('yungou_zhongjiang') . ' WHERE pid = ' . $id;
		
		foreach ($sqls as $i => $sql)
		{
			dbc(DBCMax)->query($sql)->done();
		}
		logic('seller')->product_del($p['sellerid']);
		fcache('default.catalog.procount', 0);
		return true;
	}
	
	public function Publish($data)
	{
		logic('seller')->product_add($data['sellerid']);
		dbc()->SetTable(table('product'));
		$id = dbc()->Insert($data);
		zlog('product')->publish($id, $data);
		fcache('default.catalog.procount', 0);
		return $id;
	}
	
	function MoneySaves()
	{
		$now = time();
		$sql = 'SELECT SUM((price-nowprice)*(virtualnum+totalnum)) AS saves
		FROM
			' . table('product') . '
		WHERE
			(overtime < ' . $now . ' and overtime > 0)
		AND
			status = 2';
		$result = dbc(DBCMax)->query($sql)->limit(1)->done();
		return $result['saves'];
	}
	
	function SellsCount( $id )
	{
		$sql = 'SELECT SUM(productnum) AS sums
		FROM
			' . table('order') . '
		WHERE
			productid=' . intval($id) . '
		AND
			'.logic('pay')->OrderPaidSQL().'
		AND
			status = '.ORD_STA_Normal;
		$result = dbc(DBCMax)->query($sql)->limit(1)->done();
		return (int)$result['sums'];
	}

    
	
	function BuyersCount( $id )
	{
		$sql = 'SELECT COUNT(1) AS cnts
		FROM
			' . table('order') . '
		WHERE
			productid = ' . intval($id) . '
		AND
			'.logic('pay')->OrderPaidSQL().'
		AND
			status = '.ORD_STA_Normal;
		$result = dbc(DBCMax)->query($sql)->limit(1)->done();
		return (int)$result['cnts'];
	}
	
	function Surplus( $maxnum, $sells )
	{
		$surplusnum = $maxnum - $sells;
		return $surplusnum;
	}
	
	function BuysCheck( $id, $checkIfBuyed = true, $curBuys = false, $ord_is_Paid = null, $uid = 0, $amount=0 )
	{
		$id = (int) $id;
		if (!$id) return array('false' => __('请选择你要购买的产品！'));
		$sql = 'SELECT *
		FROM
			' . table('product') . '
		WHERE
			id = ' . $id;
		$product = dbc(DBCMax)->query($sql)->limit(1)->done();
		if (!$product['id']) return array('false' => __('没有找到相应的产品！'));
		$now = time();
		if ( $product['begintime'] > $now ) return array('false' => __(TUANGOU_STR . '还没有开始哦！'));
		if ( $product['overtime'] > 0 and $product['overtime'] < $now ) return array('false' => __(TUANGOU_STR . '已经结束了哦！'));
		if ( $product['maxnum'] > 0 ) 		{
			$surplus = $this->Surplus($product['maxnum'], $this->SellsCount($id));
			if ($curBuys && $ord_is_Paid === ORD_PAID_Yes)
			{
				$surplus += $curBuys;
			}
			if($surplus < 1) return array('false' => __('该产品已经卖完了！下次请赶早'));
			if($curBuys && $curBuys > $surplus) return array('false' => __('该产品库存已经不足，请重新下单购买！'));
		}
		$buid = ($uid ? $uid : user()->get('id'));
		if ( $checkIfBuyed && $product['multibuy'] == 'false' )
		{			
			if ( $this->AlreadyBuyed($id, $buid, 0, 0) ) return array('false' => __('您已经购买过此产品了哦！'));
		}
		if(0 === $amount) {
			$amount = $curBuys;
		}
		if(false !== $amount) {
			if($amount < 1 || $amount < $product['oncemin']) {
				return array('false' => __('请填写正确的购买数量！'));
							}
			if($product['oncemax'] > 0  && $amount > $product['oncemax']) {
				return array('false' => __('您一次不能购买这么多！'));
			}
		}
		if($product['limit_level'] > 0) {
			$levels = logic('me')->level($buid);
			if($levels['level'] < $product['limit_level']) {
				return array('false' => __('您当前的用户等级不能够购买该产品！'));
			}
		}
		return $this->__parse_result($product);
	}
	
	function AlreadyBuyed( $id, $uid, $comment = 0, $pay = 1)
	{
		$sql = '
		SELECT
			`orderid`
		FROM
			' . table('order') . '
		WHERE
			`productid` = ' . (int) $id . '
		AND
			`userid`= ' . (int) $uid;
		if($comment) {
			$sql .= ' AND `comment` = 1 ';
		}
		if($pay) {
			$sql .= ' AND `pay` = 1 ';
		}
		$result = dbc()->Query($sql)->GetRow();
		return $result ? true : false;
	}
	
	function Maintain($pid = false)
	{
		logic('product')->UpdateSTATUS();
		if ($pid)
		{
			$product = logic('product')->GetOne($pid, false);
			if ($product['succ_remain'] <= 0)
			{
								logic('order')->findSuccess($pid);
			}
						$sellsCount = $this->SellsCount($pid);
			if (!$product['is_countdown'] && $sellsCount)			{
				$this->Update_direct($pid, array('sells_count' => $sellsCount));
			}
		}
	}
	
	function UpdateSTATUS()
	{
		$now = time();
		$sqls = array(
						'UPDATE '.table('product').' SET status='.PRO_STA_Failed.' WHERE successnum>(virtualnum+totalnum) AND (overtime<'.$now.' and overtime>0) AND begintime<'.$now,
						'UPDATE '.table('product').' SET status='.PRO_STA_Finish.' WHERE successnum<=(virtualnum+totalnum) AND (overtime<'.$now.' and overtime>0) AND begintime<'.$now,
						'UPDATE '.table('product').' SET status='.PRO_STA_Normal.' WHERE successnum>(virtualnum+totalnum) AND (overtime>'.$now.' or overtime<1) AND begintime<'.$now,
						'UPDATE '.table('product').' SET status='.PRO_STA_Success.' WHERE successnum<=(virtualnum+totalnum) AND (overtime>'.$now.' or overtime<1) AND begintime<'.$now,
		);
		$r = 0;
		foreach ($sqls as $i => $sql)
		{
			$r += dbc(DBCMax)->query($sql)->done();
		}
		$r && zlog('product')->maintain($r);
	}
	
	private function __parse_result( $product )
	{
		if ( ! $product ) return false;
		if ( is_array($product[0]) )
		{
			$returns = array();
			foreach ( $product as $i => $one )
			{
				$returns[] = $this->__parse_result($one);
			}
			return $returns;
		}
				$product['price'] *= 1;
		$product['nowprice'] *= 1;
		if ( $product['nowprice'] > 0 )
		{
			$product['discount'] = round(10 / ($product['price'] / $product['nowprice']), 1);
		}
		else
		{
			$product['discount'] = 0;
		}
		if ( $product['discount'] <= 0 ) $product['discount'] = 0;
		$product['time_remain'] = $product['overtime'] - time();
		$product['succ_total'] = $product['successnum'];
				if ($product['type'] == 'prize')
		{
			$product['succ_real'] = logic('prize')->sigCount('pid='.$product['id']);
		}
		else
		{
			$product['succ_real'] = $this->BuyersCount($product['id']);
		}
		$product['succ_buyers'] = $product['succ_real'] + $product['virtualnum'];
		$product['succ_remain'] = $product['succ_total'] - $product['succ_buyers'];
				if ($product['type'] == 'prize')
		{
			$product['sells_real'] = $product['succ_real'];
		}elseif($product['type'] == 'ticket'){
            $product['sells_real'] = logic('coupon')->GetList_count( $product['id'] );
        }else{
            $product['sells_real'] = $this->SellsCount($product['id']);
        }
        if($product['yungou'] == 1){
            $product['sells_real'] = $this->SellsCount($product['id']);
        }
		$product['sells_count'] = ($product['is_countdown'] ? (int)$product['sells_count'] : $product['sells_real']) + $product['virtualnum'];
				if ($product['oncemin'] <= 0)
		{
			$product['oncemin'] = 1;
		}
				if ( $product['maxnum'] > 0 )
		{
			$product['surplus'] = $this->Surplus($product['maxnum'], $product['sells_real']);
		}
		else
		{
						$product['surplus'] = 9999;
		}

				$product['imgs'] = ($product['img'] != '') ? explode(',', $product['img']) : array();
		$product['img'] = (isset($product['imgs'][0]) ? $product['imgs'][0] : '');
				$product['sellermap'] = explode(',', $product['sellermap']);
				if ($product['type'] == 'stuff')
		{
			$product['weightsrc'] = $product['weight'];
			$product['weightunit'] = ($product['weight'] >= 1000) ? 'kg' : 'g';
			$product['weight'] *= ($product['weightunit'] == 'kg') ? 0.001 : 1;
		}
				$this->PresellParser($product);
				$product['tags'] = logic('product_tag')->get_list($product['id']);
				$product['cuts'] = logic('cut')->snapshot_pro($product);
				return $product;
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
		$base = 'productid='.$product['id'];
		$STA_Normal = 'status='.ORD_STA_Normal;
		$product['mny_all'] = (float)logic('order')->Summary($base.' AND '.$STA_Normal);
		$product['mny_paid'] = (float)logic('order')->Summary($base.' AND pay='.ORD_PAID_Yes.' AND '.$STA_Normal);
		$product['mny_waited'] = (float)logic('order')->Summary($base.' AND pay='.ORD_PAID_No.' AND '.$STA_Normal);
		$product['mny_refund'] = (int)logic('order')->Summary($base.' AND status='.ORD_STA_Refund);
	}
	
	private function PresellParser(&$product)
	{
		if (isset($product['id']) && $product['id'])
		{
			$ptext = meta('p_presell_text_'.$product['id']);
			if ($ptext)
			{
				$pprice = meta('p_presell_price_full_'.$product['id']);
				$product['presell'] = array(
					'text' => $ptext,
					'price_full' => $pprice
				);
			}
		}
	}
	
	public function PresellSubmit($id)
	{
		if (post('presell_is'))
		{
			meta('p_presell_text_'.$id, post('presell_text'));
			meta('p_presell_price_full_'.$id, post('presell_price'));
		}
		else
		{
			meta('p_presell_text_'.$id, null);
			meta('p_presell_price_full_'.$id, null);
		}
	}
	
	public function STA_Name($STA_Code)
	{
		$STA_NAME_MAP = array(
			PRO_STA_Failed => '已结束，'. TUANGOU_STR . '失败',
			PRO_STA_Normal => '进行中，未成团',
			PRO_STA_Success => '进行中，已成团',
			PRO_STA_Finish => '已结束，'. TUANGOU_STR . '成功',
			PRO_STA_Refund => '已结束，已经返款'
		);
		return $STA_NAME_MAP[$STA_Code];
	}
	
	public function reSort($productList)
	{
		foreach ($productList as $i => $product)
		{
			if ($product['surplus'] < 0 && $product['order'] > 0)
			{
				logic('product')->Update($product['id'], array('order'=>0));
			}
		}
	}
	
	public function ClearDraft($pID, $dID, $exceptPID = false)
	{
				if ($pID)
		{
			$sql_filter = '1';
			$exceptPID && $sql_filter = 'id<>'.$exceptPID;
			$whereSQL = 'saveHandler="draft" AND draft='.$pID.' AND '.$sql_filter;
			$affCount = dbc(DBCMax)->delete('product')->where($whereSQL)->done();
			zlog('product')->draftClear($whereSQL, $affCount);
		}
		if (post('draft-pro-id')) $dID = post('draft-pro-id', 'int');
				if ($dID > 0 && $dID != $pID)
		{
			meta('p_hs_'.$dID, null);
			meta('p_ir_'.$dID, null);
			meta('expresslist_of_'.$dID, null);
			meta('paymentlist_of_'.$dID, null);
			if ($pID == 0)
			{
								$whereSQL = 'saveHandler="draft" AND id='.$dID;
				$affCount = dbc(DBCMax)->delete('product')->where($whereSQL)->done();
				zlog('product')->draftClear($whereSQL, $affCount);
			}
		}
				logic('catalog')->ProUpdate();
	}
	
	public function allowCSaveHandler($pid, $newHandler)
	{
		if (!in_array($newHandler, array('normal','draft'))) return false;
		$product = logic('product')->SrcOne($pid);
		if (!in_array($product['saveHandler'], array('normal','draft'))) return false;
		if ($product['saveHandler'] == 'normal' && $newHandler == 'draft') return false;
		return true;
	}
	
	public function GetDraftCount()
	{
		$r = dbc(DBCMax)->select('product')->in('COUNT(1) AS DrfCount')->where('saveHandler="draft"')->limit(1)->done();
		return $r['DrfCount'];
	}
	
	public function GetDraftList()
	{
		return dbc(DBCMax)->select('product')->where('saveHandler="draft"')->done();
	}
	
	public function CheckProductDraft($pid)
	{
		return dbc(DBCMax)->select('product')->where('saveHandler="draft" AND draft='.$pid)->order('addtime.DESC')->limit(1)->done();
	}

	
	function productCheck($id,$city=''){		$id = (is_numeric($id) ? $id : 0);
		$now = time();
		if($city!=''){
			$sql='select * from '.TABLE_PREFIX.'tttuangou_product where begintime <= '.$now.' and (overtime > '.$now.' or overtime < 1) and id = '.$id.' and (city = '.floatval($city).' or display = 2)';
		}else{
			$sql='select * from '.TABLE_PREFIX.'tttuangou_product where begintime <= '.$now.' and (overtime > '.$now.' or overtime < 1) and id = '.$id;
		}
		$query = dbc()->Query($sql);
		if (!$query)
		{
			return false;
		}
		$product=$query->GetRow();
				$product['price'] *= 1;
		$product['nowprice'] *= 1;
		return $product;
	}
	function AddSellerProNum($sellerid){
		$sql='update '.TABLE_PREFIX.'tttuangou_seller set productnum = productnum + 1 where id = '.floatval($sellerid);
		$query = dbc()->Query($sql);
		return true;
	}
	function DelSellerProNum($sellerid){
		$sql='update '.TABLE_PREFIX.'tttuangou_seller set productnum = productnum - 1 where id = '.floatval($sellerid);
		$query = dbc()->Query($sql);
		return true;
	}
	function AddSellerSucNum($sellerid){
		$sql='update '.TABLE_PREFIX.'tttuangou_seller set successnum = successnum + 1 where `is_countdown`=0 AND id = '.floatval($sellerid);
		$query = dbc()->Query($sql);
		return true;
	}
	function AddSellerTotMoney($sellerid,$money){
		$sql='update '.TABLE_PREFIX.'tttuangou_seller set money = money + '.$money.' where id = '.floatval($sellerid);
		$query = dbc()->Query($sql);
	}
	function delSellerTotMoney($sellerid,$money){
		$sql='update '.TABLE_PREFIX.'tttuangou_seller set money = money - '.$money.' where id = '.floatval($sellerid);
		$query = dbc()->Query($sql);
	}
		function GetUserSellerProduct($uid){
		$pids = array();
		$sinfo = dbc(DBCMax)->query('select id from '.table('seller')." where userid='".$uid."'")->limit(1)->done();
		$sql = "SELECT id FROM ".table('product')." WHERE sellerid ='".$sinfo['id']."'";
		$product = dbc(DBCMax)->query($sql)->done();
		if($product){
			foreach($product as $key => $val){
				$pids[] = $val['id'];
			}
		}
		return $pids;
	}
        function GetUserAreaProduct($uid){
        $smember = dbc(DBCMax)->query('select uid,area,city_place_region from '.table('members')." where uid='".$uid."'")->limit(1)->done();
        $pids = array();
        if($smember['area']){
            $sql = 'select id from '.table('product')." where city='".$smember['area']."'";
            if($smember['city_place_region']){
                $sql = 'select id from '.table('product')." where city='".$smember['area']."' and city_place_region='".$smember['city_place_region']."'";
            }
            $product = dbc(DBCMax)->query($sql)->done();
        }
        if($product){
            foreach($product as $key => $val){
                $pids[] = $val['id'];
            }
        }
        return $pids;
    }

		function GetOwnerLink($sellerid){
		$sql = "SELECT `id`,`name` FROM `".table('product')."` WHERE `display`>0 AND `saveHandler`='normal' AND `linkid`=0 AND `sellerid`='".$sellerid."'";
		$data = dbc(DBCMax)->query($sql)->done();
		return $data;
	}
		public function get_link_product($linkid){
		$data = array();
		if($linkid > 0){
			$sql = 'SELECT * FROM `'.table('product_link').'` WHERE `id`='.$linkid;
			$data = dbc(DBCMax)->query($sql)->limit(1)->done();
			if($data){
				$data = $this->_format_link_data($data);
			}
		}
		return $data;
	}
	private function _format_link_data($data){
		$pids = array();
		$linkproduct = unserialize($data['link_product']);
		foreach($linkproduct as $k => $v){
			$pids[] = $v['pid'];
		}
		$productnames = dbc(DBCMax)->query('SELECT id,name FROM `'.table('product').'` WHERE `id` IN('.implode(',',$pids).')')->done();
		foreach($linkproduct as $lk => $lv){
			foreach($productnames as $pk => $pv){
				if($lv['pid'] == $pv['id']){
					$linkproduct[$lk]['product_name'] = $pv['name'];
				}
			}
		}
		$data['products'] = $linkproduct;
		$data['pids'] = $pids;
		return $data;
	}
		public function get_link_list($sellerid = 0){
		$data = array();
		if($sellerid > 0){
						$sql = 'SELECT * FROM `'.table('product_link').'` WHERE `sellerid` in ("'.$sellerid.'")';
		}else{
			$sql = 'SELECT * FROM `'.table('product_link').'`';
		}
		$sql = page_moyo($sql);
		$data = dbc(DBCMax)->query($sql)->done();
		if($data){
			foreach($data as $key => $val){
				$data[$key] = $this->_format_link_data($val);
			}
		}
		return $data;
	}
		public function linksave($sellerid = 0, $data = array()) {
		if($sellerid > 0 && $data && is_array($data) && count($data) > 1){
			$pids = array();
			foreach($data as $k => $v){
				$pids[] = $v['pid'];
			}
			$ldata = array(
				'sellerid' => $sellerid,
				'link_product' => serialize($data)
			);
			$rid = dbc(DBCMax)->insert('product_link')->data($ldata)->done();
			if($rid > 0){
				dbc(DBCMax)->update('product')->data(array('linkid'=>$rid))->where('id IN('.implode(',',$pids).')')->done();
			}
		}
	}
		public function deletelink($id = 0) {
		if($id > 0 && $this->check_link_byid($id)) {
			dbc(DBCMax)->delete('product_link')->where(array('id'=>$id))->limit(1)->done();
			dbc(DBCMax)->update('product')->data(array('linkid'=>'0'))->where(array('linkid'=>$id))->done();
		}
	}
		public function check_link_byid($id = 0){
		$return = false;
		if($id > 0){
			$linkinfo = $this->get_link_product($id);
			if($linkinfo){
				if(MEMBER_ROLE_TYPE == 'seller'){
					$sellerid = logic('seller')->U2SID(MEMBER_ID);
					if($sellerid == $linkinfo['sellerid']){
						$return = true;
					}
				}elseif(MEMBER_ROLE_TYPE == 'admin'){
					$return = true;
				}
			}
		}
		return $return;
	}
		public function updatelink($id = 0, $data = array()) {
		if($id > 0 && $data && is_array($data) && count($data) > 1 && $this->check_link_byid($id)){
			$pids = array();
			foreach($data as $k => $v){
				$pids[] = $v['pid'];
			}
			dbc(DBCMax)->update('product_link')->data(array('link_product'=>serialize($data)))->where(array('id'=>$id))->done();
			dbc(DBCMax)->update('product')->data(array('linkid'=>'0'))->where(array('linkid'=>$id))->done();
			dbc(DBCMax)->update('product')->data(array('linkid'=>$id))->where('id IN('.implode(',',$pids).')')->done();
		}
	}

	public function get_prize_list($limit = 3, $actived = null) {
		if(false === ($data = cache("misc/product-get_prize_list-{$actived}-{$limit}", 600))) {
			$data = $this->GetList(logic('misc')->City('id'), $actived, ' p.`type`="prize" ', $limit);
			cache($data);
		}
		return $data;
	}
	
	
	
	
	
	public function getList_forAdmin($whereSql, $orderSql)
	{

		$sql = 'SELECT p.*,' .
					  'p.sells_count * p.nowprice AS totalsales_order,' . 
					  'IF(ISNULL(p.maxnum), 99999,							
					  	  IF(p.maxnum=0, 99999,
					  	     IF(p.maxnum < p.sells_count, 0, p.maxnum - p.sells_count)
							 )
						  ) AS surplus_order,' .  					  's.sellername,s.sellerphone,s.selleraddress,s.sellerurl,s.sellermap,' .
					  'c2.name AS catalogSecondName, c1.name AS catalogFirstName' .
					  $this->get_list_ext_sql();
		$sql .= ' FROM ' . table('product') . ' p ' . 
					' LEFT JOIN ' . table('seller') . ' s ON p.sellerid=s.id' .
					' LEFT JOIN ' . table('catalog') . ' AS c2 ON p.category=c2.id'.
					' LEFT JOIN ' . table('catalog') . ' AS c1 ON c2.parent=c1.id';
		$sql .= ' WHERE ' . $whereSql;
		$sql .= ' AND p.saveHandler = "normal"';
		
		$sql .= ' ORDER BY p.id';
		logic('isearcher')->Linker($sql);
		$sql = page_moyo($sql);

		$sql = str_replace(' ORDER BY p.id', ' ORDER BY ' . $orderSql, $sql);

		$result = dbc(DBCMax)->query($sql)->done();
		return $this->__parse_result($result);
	}

	public function get_sub_sellerids($pid, $inc_self_sid = 1) {
		$pid = (int) $pid;
		if($pid < 1) {
			return ;
		}

		$product = $this->SrcOne($pid);
		$sids = explode(",", $product['sub_sellerid']);
		if($inc_self_sid) {
			$sids[] = $product['sellerid'];
		}

		return $sids;
	}

    
    public function store_product($sellerid)
    {
    	$category = 1;
            $actived = PRO_ACV_Yes;
            if($sellerid) {
                $category = ' p.`sellerid`="' . $sellerid . '" ';
            }
            $product = logic('product')->GetList(logic('misc')->City('id'), $actived, $category);
        foreach($product as $val){
                        $category = dbc(DBCMax)->select('catalog')->where('id='.$val['category'])->limit(1)->done();
            if($category['parent'] != 0){
                $category2 = dbc(DBCMax)->select('catalog')->where('id='.$category['parent'])->limit(1)->done();
            }else{
                $category2 = $category;
            }
            $catalogs[$category2['id']] = $category2;
        }
                $usePager = get('page', 'int') > 0 ? true : false;
        if ( !$usePager && false == $product )
        {
            return false;
        }

            $city_id = logic('misc')->City('id');
            $cache_id = 'default.catalog.procount.' . $city_id .'_'. $sellerid;
            $cache_product = fcache($cache_id, 1800);
            if($cache_product){
                $product = $cache_product;
            }else{
                
                foreach($catalogs as $key=>$val){
                    $val['navcate'] = logic('catalog')->Filter($val['flag'], 'product');

                    $catalogs[$key]['product'] = logic('product')->GetList($city_id, PRO_ACV_Yes, $val['navcate'], $val['index_display_count'], $sellerid);
                    if(!$catalogs[$key]['product']){
                        unset($catalogs[$key]);
                    }
                }
                $product = $catalogs;
            }

        return  $product;
    }

    
    public function manage_area_product($uid){
        $pids = logic('product')->GetUserAreaProduct($uid);
        $asql = 0;
        if($pids){
            $asql = implode(',',$pids);
        }
        return $asql;
    }
}
?>