<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name favorite.logic.php
 * @date 2016-05-25 16:51:50
 */





class FavoriteLogic
{
	
	public function get_list($status = 0, $user_id = MEMBER_ID)
	{
		$timestamp = time();
		if ($status == 0 ) {
			$sql_limit_status = '(p.overtime > '.$timestamp.' or p.overtime < 1) AND p.begintime < '.$timestamp;
		}
		if($status == 1){
			$sql_limit_status = '(p.overtime < '.$timestamp.' and p.overtime > 0)';
		}
		if($status == 2){
			$sql_limit_status = 'p.begintime > '.$timestamp;
		}
		if ($status == -1) {
			$sql_limit_status = '1';
		}

		$sql = 'SELECT p.*, f.uid, f.pid
		FROM
			' . table('favorite') .' f
		,
			' . table('product') . ' p
		WHERE
			'."f.uid = ".$user_id.'
		AND
			f.pid = p.id
		AND
			'. $sql_limit_status;
		$sql = page_moyo($sql);
		$results = dbc(DBCMax)->query($sql)->done();
		return $results;
	}
	
	public function get_one($pid = 0, $user_id = MEMBER_ID)
	{
		return dbc(DBCMax)->select("favorite")->where(array('uid'=>$user_id,'pid'=>$pid))->limit(1)->done();
	}
	
	public function create($product_id, $user_id = MEMBER_ID)
	{
		return dbc(DBCMax)->insert('favorite')->data(array(
			'uid' => $user_id,
			'pid' => $product_id
		))->done();
	}

	
	public function delete($product_id, $user_id = MEMBER_ID)
	{
		return dbc(DBCMax)->delete('favorite')->where(array('uid' => $user_id,'pid' => $product_id))->done();
	}
}

?>