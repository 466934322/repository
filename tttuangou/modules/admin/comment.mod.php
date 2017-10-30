<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name comment.mod.php
 * @date 2015-11-12 16:33:06
 */





class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	
	public function vlist()
	{
		$this->CheckAdminPrivs('comments');
		$where = ' 1 ';

		$status = get('status');
		if(in_array($status, array('auditing', 'approved', 'denied', ))) {
			$where .= " AND `status`='{$status}' ";
		}
		if(false != ($reply = get('reply', 'int'))) {
			if(-1 == $reply) {
				$where .= " AND `reply`='' ";
			} elseif (1 == $reply) {
				$where .= " AND `reply`!='' ";				
			}
		}
		if(false != ($toped=get('toped'))) {
			$where .= " AND `toped`='1' ";
		}
		$iks = array('id', 'order_id', 'product_id', 'user_id', );
		foreach($iks as $ik) {
			$iv = get($ik, 'number');
			if($iv > 0) {
				$where .= " AND `{$ik}`='{$iv}' ";
			}
		}
                if(MEMBER_ROLE_TYPE == 'admin'){
            $smember = dbc(DBCMax)->query('select uid,area,city_place_region from '.table('members')." where uid='".MEMBER_ID."'")->limit(1)->done();
                        if($smember['area']){
                $asql = logic('product')->manage_area_product(MEMBER_ID);
                $where .= ' AND product_id IN('.$asql.')';
            }
        }
		$comments = logic('comment')->admin_vlist($where);
		include handler('template')->file('@admin/comments_list');
	}
	
	public function status_sync()
	{
		$this->CheckAdminPrivs('comments','ajax');
		$id = get('id', 'int');
		$status = get('status', 'txt');
		$r = logic('comment')->status_sync($id, $status);
		exit($r ? 'ok' : 'error');
	}
	
	public function toped_sync()
	{
		$this->CheckAdminPrivs('comments','ajax');
		$id = get('id', 'int');
		$switch = get('switch', 'txt');
		$r = logic('comment')->toped_sync($id, $switch);
		exit($r ? 'ok' : 'error');
	}
	
	public function ajax_modify()
	{
		$this->CheckAdminPrivs('comments','ajax');
		$id = get('id', 'int');
		$comment = logic('comment')->source_get_one($id);
		include handler('template')->file('@admin/comments_mgr_ajax');
	}
	
	public function ajax_submit()
	{
		$this->CheckAdminPrivs('comments','ajax');
		$id = post('id', 'int');
		$order_id = post('order_id', 'int');
		$product_id = post('product_id', 'int');
		$score = post('score', 'int');
		$content = post('content', 'string');
		$reply = post('reply', 'string');
		$user_name = post('user_name', 'txt');
		$r = logic('comment')->admin_form_submit($score, $content, $reply, $user_name, $product_id, $id, $order_id);
		exit($r ? 'ok' : 'error');
	}
	
	public function ajax_delete()
	{
		$this->CheckAdminPrivs('comments','ajax');
		$id = get('id', 'int');
		logic('comment')->delete($id);
		exit('<script type="text/javascript">comment_delete_result("ok", '.$id.');</script>');
	}
	
	public function ajax_view()
	{
		$this->CheckAdminPrivs('comments','ajax');
		$id = get('id', 'int');
		$comment = logic('comment')->source_get_one($id);
		if($comment['img']){
			$comment['content'] .= "<br><img src='".imager($comment['img'], IMG_Normal)."'>";
		}
		exit($comment['content']);
	}
	
	public function config()
	{
		$this->CheckAdminPrivs('comments');
		$config = ini('comment');
		include handler('template')->file('@admin/comments_config');
	}
	
	public function config_save()
	{
		$this->CheckAdminPrivs('comments');
		$config = post('config');
		ini('comment', $config);
		$this->Messager('设置保存成功！', '?mod=comment&code=config');
	}
}

?>