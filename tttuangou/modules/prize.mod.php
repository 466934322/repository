<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name prize.mod.php
 * @date 2015-08-31 16:32:14
 */





class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		if (MEMBER_ID < 1)
		{
			if (get('pid'))
			{
				header('Location: '.rewrite('?view='.get('pid')));
				exit;
			}
			$this->Messager(__('请先登录！'), '?mod=account&code=login');
		}
		$runCode = Load::moduleCode($this);
		$this->$runCode();
		
	}
	
	function Main()
	{
		header('Location: .');
	}
	function Sign()
	{
		$pid = get('pid', 'int');
				$prizes = logic('prize')->GetList($pid, user()->get('id'), 1);
		if ($prizes)
		{
						header('Location: '.rewrite('?mod=prize&code=view&pid='.$pid));
			exit;
		}
				$phone = logic('prize')->phone();
				$product = logic('product')->BuysCheck($pid, false);
		isset($product['false']) && $this->Messager($product['false']);
		include handler('template')->file('prize_sign');
	}
	function Iniz()
	{
		$pid = get('pid', 'int');
		$iz = logic('prize')->InizTicket($pid, user()->get('id'));
		if ($iz !== true)
		{
			$this->Messager($iz);
		}
		header('Location: '.rewrite('?mod=prize&code=view&pid='.$pid));
	}
	function View()
	{
		$pid = get('pid', 'int');
		$pid || exit('.O O. I need the Product-ID...');
		$prizes = logic('prize')->GetList($pid, user()->get('id'));
				$product = logic('product')->GetOne($pid);
		$product || exit('> _ < Product-ID invaid');
		$this->Title = $product['name'];
		include handler('template')->file('prize_view');
	}
	function Ajax_S2Phone()
	{
		if(MEMBER_ID < 1) exit('请先登录！');
		$phone = get('phone', 'number');
		if (strlen($phone) != 11) exit('无效的手机号码！');
		if(false == logic('seccode')->verify(get('seccode'))) exit('无效的图片验证码！请重新输入~');
		$r = logic('prize')->S2Phone($phone);
		exit($r === true ? 'ok' : $r);
	}
	function Ajax_Vfcode()
	{
		if(MEMBER_ID < 1) exit('请先登录！');
		$phone = get('phone', 'number');
		if (strlen($phone) != 11) exit('无效的手机号码！');
		$vcode = get('vcode', 'number');
		if (strlen($vcode) != 5) exit('无效的验证码！');
		$r = logic('prize')->Vfcode($phone, $vcode);
		exit($r === true ? 'ok' : $r);
	}
}

?>