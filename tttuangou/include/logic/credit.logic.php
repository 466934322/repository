<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name credit.logic.php
 * @date 2016-05-05 15:32:45
 */



class CreditLogic
{
	public function get_list_for_me($uid){
		$sql = "SELECT * FROM ". table('credit') ." WHERE uid='". (int)$uid ."' ORDER BY id DESC";
		$sql = page_moyo($sql);
		$res = dbc(DBCMax)->query($sql)->done();
		if( $res ){
			return $res;
		}else{
			return array();
		}
	}

	public function add_score($pid=0,$uid=0,$score=0,$type='buy',$share='',$sid='',$money='',$proportion='',$other_uid=''){
		$info = '';
                if($type == 'get_credit' || $type == 'give_credit'){
                        $other_user = user($other_uid)->get();
            $real_score = $score * (100-$proportion)/100;
        }
		if(!in_array($type,array('buy','reply','forward', 'signin', 'mall','pay_bill','credit_buy','give_credit','get_credit'))){
			$type='buy';
		}
		if($type != 'buy' && $type != 'pay_bill' && $type != 'credit_buy' && $type != 'get_credit' && $type != 'give_credit'){
			$set_scores = ini('credits.config');
			$score = (int)$set_scores[$type];
		}
        if($type == 'pay_bill'){
                        if($sid){
                $seller = logic('seller')->GetOne($sid);
            }
            $info = '买单订单：'.$seller['sellername'];
        }elseif($type == 'credit_buy'){

            $info = '支付：'.$money.'元,兑换'.$score.'积分';
        }elseif($type == 'give_credit'){

            $info = '赠送：'. $other_user['username'] .' '.$score.' 积分,转赠获得率'.(100-$proportion).'%,对方可获得'.$real_score.'积分';
        }elseif($type == 'get_credit'){

            $info = '获得：'. $other_user['username'] .' 赠送的 '.$score.'积分,转赠获得率'.(100-$proportion).'%,实际获得'.$real_score.'积分';
            $score = $real_score;
        }else{
            $products = dbc(DBCMax)->select('product')->in('name')->where('`id`='. (int)$pid)->limit(1)->done();
            if($products['name']){
                if($type == 'buy'){
                    $info = '兑换产品：'.$products['name'];
                }elseif($type == 'reply'){
                    $info = '评论产品：'.$products['name'];
                }elseif($type == 'forward' && $share && $uid > 0){
                    $chx = dbc(DBCMax)->select('credit')->where(array('uid'=>$uid,'pid'=>$pid,'type'=>'forward'))->limit(1)->done();
                    if(!$chx){
                        $info = '分享产品：'.$products['name'].' 到：'.$share;
                    }
                }
            }
        }
		self::log($uid, $score, $info, $type, $pid);
	}

	public function log($uid, $score, $info, $type, $pid = 0) {
		$uid = (int) $uid;
		$score = (int) $score;
		$pid = (int) $pid;
		if($uid > 0 && $score != 0 && $info) {
			$info .= '<br />当前积分：' . user($uid)->get('scores');
			$data = array(
				'uid'  => $uid,
				'pid'  => $pid,
				'info' => $info,
				'score'=> $score,
				'type' => $type,
				'gettime' => time(),
			);
			dbc(DBCMax)->insert('credit')->data($data)->done();
            if($type == 'give_credit'){
                dbc(DBCMax)->update('members')->data('scores=scores-'.$score)->where('uid='.$uid)->done();
            }else{
                dbc(DBCMax)->update('members')->data('scores=scores+'.$score)->where('uid='.$uid)->done();
            }
		}
	}

    
    public function GetFree($money,$type=0, $uid = null)
    {
        $uid = (null === $uid ? user()->get('id') : (int) $uid);
        
            $order = $this->__CreateNew($uid,$money,$type);
                return $order;
    }
    
    public function Where($sql_limit)
    {
        $sql = '
		SELECT
			*
		FROM
			'.table('credit_order').'
		WHERE
			'.$sql_limit.'
		';
        return dbc()->Query($sql)->GetAll();
    }
    
    private function __CreateNew($uid,$money,$type=0)
    {
        $array = array(
            'orderid' => $this->__GetFreeID(),
            'userid' => $uid,
            'ptype' => $type,
            'money' => $money,
            'createtime' => time(),
            'status' => 255
        );
        dbc()->SetTable(table('credit_order'));
        dbc()->Insert($array);
        return $array;
    }
    
    public function GetOne($id, $uid=null)
    {
        $id = (is_numeric($id) ? $id : 0);
        $sql = '
		SELECT
			*
		FROM
			' . table('credit_order') .'
		WHERE
			orderid = ' . $id . (is_null($uid) ? '' : " AND `userid`='".(int) $uid."' ");
        $order = dbc()->Query($sql)->GetRow();
        return $order;
    }
    
    public function GetList($where = '1')
    {
        $sql = dbc(DBCMax)->select('credit_order')->where($where)->order('createtime.desc')->sql();
        logic('isearcher')->Linker($sql);
        $sql = page_moyo($sql);
        return dbc(DBCMax)->query($sql)->done();
    }
    
    private function __GetFreeID()
    {
        $id = (date('Y', time())+1000) . date('md', time()) . str_pad(rand('1', '99999'), 5, '0', STR_PAD_LEFT);
        $sql = '
		SELECT
			*
		FROM
			' . table('credit_order') . '
		WHERE
			orderid = ' . $id;
        $order = dbc()->Query($sql)->GetRow();
        if ( empty($order) )
        {
            return $id;
        }
        else
        {
            return $this->__GetFreeID();
        }
    }

    
    public function Update($id, $array)
    {
        $id = (is_numeric($id) ? $id : 0);
        dbc()->SetTable(table('credit_order'));
        return dbc()->Update($array, 'orderid = '.$id);
    }
}
?>