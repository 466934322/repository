<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name comment.mod.php
 * @date 2015-08-31 16:32:13
 */





class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		if (MEMBER_ID < 1)
        {
            $this->Messager(__('请先登录！'), '?mod=account&code=login');
        }
		$runCode = Load::moduleCode($this, false, false);
		$this->$runCode();
	}

	function Main()
	{
		$this->Title = '评价产品';
		$pid = get('pid', 'number');
		if($pid > 0 && false != ($product = logic('product')->GetOne($pid))) {
			if(false === admin_priv('virtual_comments')) {
	    		$this->Messager('您没有权限');
	    	}
	    	include handler('template')->file('@m/user_reviews_3g');
	    	exit;
		}
		$order_id = get('oid', 'number');
		$order_cid = get('coid', 'number');
		if($order_id){
			$order = logic('order')->GetOne($order_id);
			if($order && $order['userid'] == MEMBER_ID && $order['comment'] == 1){
				$product = $order['product'];
				unset($order);
				include handler('template')->file('@m/user_reviews_3g');
			}else{
				$this->Messager('操作错误！','?mod=me&code=order&comment=1');
			}
		}else{
			$this->Title = '我给商家的评价';
			$comments = logic('comment')->front_get_my_comments(MEMBER_ID,$order_cid);
			include handler('template')->file('comment_my');
		}
    }

	
	public function submit()
	{
		$product_id = get('pid', 'number');
		$order_id = get('oid', 'number');
		$score = post('score', 'int');
		$content = strip_tags(post('content', 'txt'));
		if(trim($content) == ''){
			$this->Messager('请输入评价内容！');
		}
		$result = logic('comment')->front_user_submit($order_id, $score, $content, MEMBER_ID, NULL, $img='', $anonymous='', $product_id, $virtual_username='');

        if (is_numeric($result))
		{
			$this->Messager('评价成功！','m.php?mod=me&code=order&pay=1');
		}
		else
		{
			$this->Messager('评价失败！'.$result);
		}
	}

		public function ajaxsubmit()
	{
		$id = post('id','int');
		$reply = trim(strip_tags(post('reply')));
		$retrun = logic('comment')->seller_reply($id, $reply);
	}
}

?>