<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name yungou.logic.php
 * @date 2016-07-25 18:45:53
 */





class YungouLogic
{
	
	
	public function addCanYuJiLu($pid, $orderid, $uid, $idx = false) {

				$guid = (is_numeric($idx) ? ($orderid.'-'.$idx) : ('USR-'.md5("$pid, $orderid, $uid, $idx, ".(string)microtime(true))));

				$sql = 'INSERT INTO ' . table('yungou_canyu') .
			   ' (pid, orderid, uid, `number`, addtime, `guid`)' .
			   ' VALUES(' . $pid . ',' . $orderid . ',' . $uid . ',' .
			   
			   ' (SELECT maxnumber + 1 FROM 
			   						   (SELECT IFNULL(MAX(number),0) AS maxnumber 
									   	FROM ' . table('yungou_canyu') . 
										' WHERE pid = ' . $pid . ') AS tmpTable)' .					   
			    ',"' . time() . '", "' . $guid . '")';
		
				if(false == dbc(DBCMax)->select('yungou_canyu')->where(array('guid'=>$guid))->done()) {
			$result = dbc(DBCMax)->query($sql)->done();
		}
		
		return $result;
		
	}
	
	
	public function getYunGouList_withinPage($smember='')
	{
        if($smember['area']){
            if($smember['city_place_region']){
                $where = ' and city='.$smember['area'].' and city_place_region='.$smember['city_place_region'];
            }else{
                $where = ' and city='.$smember['area'];
            }
        }else{
            $where = '';
        }
		$sql = 'SELECT id,flag,name,img,sells_count,virtualnum,maxnum,yungou,display,`order`' .
			   ' FROM ' . table('product') .
			   ' WHERE yungou != 0' .$where.										   ' AND  saveHandler = "normal"'.
			   ' ORDER BY `order`, id DESC';
		$sql = page_moyo($sql);					
		$result = dbc(DBCMax)->query($sql)->done();					
		$yunGouList = array();
		
		if (!is_null($result)) {
			
			$yunGouList = $this->parse_yungou_list($result);				
		}
		
		return $yunGouList;	
	}
	
	
	public function getYunGouList_byUid_withinPage($uid)
	{
		$sql = 'SELECT p.id, p.flag, p.name, p.img, p.sells_count, p.virtualnum,
					   p.maxnum, p.yungou, p.display, p.order' .
			   ' FROM ' . table('order') . ' AS o ' .
			   ' LEFT JOIN ' . table('product') . ' AS p ' .
			   ' ON o.productid = p.id' .
			   ' WHERE o.userid = ' . (int) $uid .
			   ' AND yungou != 0' .
			   ' AND o.status != ' . ORD_STA_Virtual . 
			   ' AND o.status != ' . ORD_STA_Virtual_AlREDAY .
			   ' GROUP BY o.productid' .
			   ' ORDER BY p.id DESC';
			   
		$sql = page_moyo($sql);
		
		$result = dbc(DBCMax)->query($sql)->done();				
		$yunGouList = array();
		
		if (!is_null($result)) {
			
			$yunGouList = $this->parse_yungou_list($result);
			
		}
		return $yunGouList;
	}
	
	
	public function getCanYuJiLu_withinPage($pid, $uid = NULL, $orderid = NULL) {
		$sql = 'SELECT *' .
			   ' FROM ' . table('yungou_canyu') .
			   ' WHERE pid = ' . $pid;
			   
		if (!is_null($uid)) {
			
			$sql .= ' AND uid = ' . $uid;
			
		}
		
		if (!is_null($orderid)) {
			
			$sql .= ' AND orderid = ' . $orderid;
		}

		$sql .= ' ORDER BY id';
			   
			   
		$sql = page_moyo($sql);					
		$result = dbc(DBCMax)->query($sql)->done();
		
		if (is_null($result)) {
			
			$result = array();						
		}
		
		return $result;
	}
	
	
	public function getCanYuJiLu($pid, $uid = NULL, $orderid = NULL) {
		$sql = 'SELECT *' .
			   ' FROM ' . table('yungou_canyu') .
			   ' WHERE pid = ' . $pid;
			   
		if (!is_null($uid)) {
			
			$sql .= ' AND uid = ' . $uid;
			
		}
		
		if (!is_null($orderid)) {
			
			$sql .= ' AND orderid = ' . $orderid;
		}

		$sql .= ' ORDER BY id';
		
		$result = dbc(DBCMax)->query($sql)->done();
		
		if (is_null($result)) {
			
			$result = array();						
		}
		
		return $result;
	}

	public function getYunGouNumbers($pid, $uid = NULL, $orderid = NULL) {
				$canYuJiLu = $this->getCanYuJiLu($pid, $uid, $orderid);
		
		$numberArr = array();
		foreach ($canYuJiLu as $jiLu) {
			$numberArr[] = $jiLu['number'];
		}
		
		return implode(' , ', $numberArr);
	}
	
	
	public function getCanYuJiLu_groupByOrder($pid, $uid = NULL, $orderid = NULL) {
		
		$canYuJiLu = array();				
		$sql = 'SELECT *' .
			   ' FROM ' . table('yungou_canyu') .
			   ' WHERE pid = ' . $pid;
			   
		if (!is_null($uid)) {
			
			$sql .= ' AND uid = ' . $uid;
			
		}
		
		if (!is_null($orderid)) {
			
			$sql .= ' AND orderid = ' . $orderid;
		}

		$sql .= ' ORDER BY id';
		
		$result = dbc(DBCMax)->query($sql)->done();
		
		
		if (! is_null($result)) {
			
			foreach ($result as $jiLu) {
				
				if (! array_key_exists($jiLu['orderid'], $canYuJiLu)) {
					
					$order = logic('order')->SrcOne($jiLu['orderid']);
					
					if ($order) {
						$canYuJiLu[$jiLu['orderid']] = array(
							'paymoney' => $order['paymoney'],									'buytime' => $order['buytime'],											'numbers' => array(),														);
					}
					else {
						
						return $canYuJiLu;																		
					}
					
				}
				
				$canYuJiLu[$jiLu['orderid']]['numbers'][] = $jiLu['number'];
				
			}
			
		}
		
		return $canYuJiLu;
	}
	
	
	public function getCanYuJiLu_byNumber($pid, $number) {
		
		$canYuJiLu = array();
		
		$sql = 'SELECT *' .
			   ' FROM ' . table('yungou_canyu') .
			   ' WHERE pid = ' . $pid .
			   ' 	AND number = ' . $number .
			   ' LIMIT 1';
		
		$result = dbc(DBCMax)->query($sql)->done();
		
		if (!is_null($result)) {
			$canYuJiLu = $result[0];
		}
		
		return $canYuJiLu;
		
	}
	
	
	public function getZhongJiangJiLu($pid) {
		
		$jiLu = array();
		
		$sql = 'SELECT *' . 
			   ' FROM '. table('yungou_zhongjiang') .
			   ' WHERE pid = ' . $pid .
			   ' LIMIT 1';
			   
		$result = dbc(DBCMax)->query($sql)->done();
		
		if (!is_null($result)) {
			$jiLu = $result[0];
		}
		
		return $jiLu;
		
	}
	
	
	public function getZhongJiang($pid)
	{
		$zhongJiang = array();
		
		$sql = 'SELECT *' .
			   ' FROM ' . table('yungou_zhongjiang') .
			   ' WHERE pid = ' . $pid .
			   ' ORDER BY id' .
			   ' LIMIT 1';
		
		$result = dbc(DBCMax)->query($sql)->done();
		
		if (!is_null($result)) {
			$zhongJiang = $result[0];
		}
		
		return $zhongJiang;
	}

	
	public function GetPhoneList($pid, $excUid = false)
	{
		
		$phones = '';
		
		$sql = 'SELECT m.phone' .
			   ' FROM ' . table('yungou_canyu') . ' AS y' .
			   ' LEFT JOIN ' . table('members') . ' AS m' .
			   ' ON y.uid = m.uid' .
			   ' WHERE y.pid=' . $pid;
			   
		if ($excUid) {
			$sql .= ' AND y.uid != ' . $excUid;
		}
		
		$sql .= ' GROUP BY y.uid';
		
		$result = dbc(DBCMax)->query($sql)->done();
		
		if (!is_null($result)) {

			foreach ($result as $val) {
				if (!empty($val['phone'])) {
					$phones .= ';' . $val['phone'];
				}
			}
			
			$phones = substr($phones, 1);	
		}
		
		return $phones;
	}
	
	
	public function isYunGouSoldOut($pid) {
		
		$result = false;
		
		$product = logic('product')->SrcOne($pid);						
		if (!is_null($product) && ($product['sells_count'] >= $product['maxnum'])) {
			$result = true;
		}
		
		return $result;
		
	}
	
	
	public function isYunGouOrder($orderId) {
		
		$result = false;
		
		$order = logic('order')->srcOne($orderId);					
		if ($order) {
			
			$product = logic('product')->srcOne($order['productid']);				
			if ( !is_null($product) && ($product['yungou'] != 0) ) {
				
				$result = true;
				
			}
			
		}
		
		return $result;
		
	}
	
	
	public function isYunGouProduct($productId) {
		
		$result = false;
		
		$product = logic('product')->SrcOne($productId);
		
		if ( !is_null($product) && ($product['yungou'] != 0) ) {
			
			$result = true;
			
		}
		
		return $result;
		
	}
	
	
	public function setZhongJiang($pid, $number)
	{
		
		$result = '公开中奖号码失败！（数据库错误）';
		
		$canYu = $this->getCanYuJiLu_byNumber($pid, $number);				
		$data = array();
		if (empty($canYu))
		{
						$data['uid'] = 0;
			$data['phone'] = '';
		}
		else
		{
			$data['uid'] = $canYu['uid'];
			$data['phone'] = user($canYu['uid'])->get('phone');
		}
		$data['pid'] = $pid;
		$data['orderid'] = $canYu['orderid'];
		$data['number'] = $number;
		$data['addtime'] = time();
		
		$index = dbc(DBCMax)->insert('yungou_zhongjiang')->data($data)->done();
		
		if ($index) {
			$result = true;
		}
		
		return $result;
	}
	
	
	public function parse_yungou_list($yunGouArr) {
		
		$displayMode = array(
			PRO_DSP_None 	=> 	'不在前台显示',
			PRO_DSP_City 	=> 	'限定城市显示',
			PRO_DSP_Global 	=> 	'全部城市显示',
		);
		
		$newYunGouArr = array();

		foreach ($yunGouArr as $index => $yunGou) {
            			$newYunGouArr[$index]['id'] = $yunGou['id'];								$newYunGouArr[$index]['flag'] = $yunGou['flag'];							$newYunGouArr[$index]['name'] = $yunGou['name'];										$newYunGouArr[$index]['nowprice'] = $yunGou['nowprice'];				$newYunGouArr[$index]['price'] = $yunGou['price'];							$newYunGouArr[$index]['totalSalesNum'] = $yunGou['sells_count'] + $yunGou['virtualnum'];				$newYunGouArr[$index]['realSalesNum'] = $yunGou['sells_count'];										$newYunGouArr[$index]['remainSalesNum'] = $yunGou['maxnum'] - $yunGou['sells_count'];					$newYunGouArr[$index]['status'] = $yunGou['yungou'];													$newYunGouArr[$index]['display'] = $displayMode[$yunGou['display']];									$newYunGouArr[$index]['order'] = $yunGou['order'];														$newYunGouArr[$index]['yungou'] = $yunGou['yungou'];										
            $newYunGouArr[$index]['imgs'] = ($yunGou['img'] != '') ? explode(',', $yunGou['img']) : array();
            $newYunGouArr[$index]['img'] = (isset($newYunGouArr[$index]['imgs'][0]) ? $newYunGouArr[$index]['imgs'][0] : '');
        }
		
		return $newYunGouArr;
	}
}
?>