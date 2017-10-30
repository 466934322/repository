<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name signin.mod.php
 * @date 2015-01-26 15:00:47
 */
 


class ModuleObject extends MasterObject {

	public function ModuleObject( $config ) {
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}

	public function Main() {
		$ret = logic('signin')->signin();

		$rets = array(
			'1' => '恭喜您，今天获得<i>'.logic('signin')->notice['today_credit'].'</i>积分，明日签到可获得<i>'.logic('signin')->notice['nextday_credit'].'</i>积分！',
			'0' => '签到送积分的功能未开启',
			'-1' => '请先登录或者注册一个账号',
			'-2' => '您已经连续签到<i>'.logic('signin')->notice['signin_day'].'</i>天，明日签到可获得<i>'.logic('signin')->notice['nextday_credit'].'</i>积分',
			'-3' => '签到送积分的规则未设置或积分项为空',
		);
		exit($rets[$ret] ? $rets[$ret] : '未知错误');
	}

}