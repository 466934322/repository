<?php
/**
 * 模块：积分商城功能
 * @copyright (C)2015 Cenwor Inc.
 * @author 狐狸<foxis@qq.com>
 * @package creditmall.mod.php
 * @version $Id$
 */
class ModuleObject extends MasterObject {

	public $is3g = '';
	public $tplid = '';
	public $user = array();

	public function ModuleObject( $config ) {
		global $rewriteHandler;
		$rewriteHandler = null;

		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);

		if(false == logic('creditmall')->is_open) {
			exit('creditmall is closed');
		}

		$this->is3g = ($_REQUEST['3g'] ? 1 : 0);
		if($this->is3g) {
			$this->tplid = '_3g';
		}
		$this->_init_user();

		$this->$runCode();
	}

	public function Main() {
		exit('request uri is error');
			}

	public function goods_list() {
		$this->Title = '积分商城首页';

		$params = array('expire_min' => strtotime(date('Y-m-d')));
        $catelog = dbc(DBCMax)->select('goods_catalog')->order('`order` desc')->done();
                $category = get('cate', 'int');        if($category > 0) {
            $params['category'] = $category;
            $cate = dbc(DBCMax)->select('goods_catalog')->where('id='.$category)->limit(1)->done();
            $this->Title = '分类列表';
        }
                $sort_type = get('sort_type');        switch($sort_type){
            case 'newest':
                $params['sort_type'] = ' dateline desc';
                $sort_type = '最新';
                break;
            case 'sellest':
                $params['sort_type'] = ' order_count desc';
                $sort_type = '销量';
                break;
            case 'scoreTop':
                $params['sort_type'] = ' price desc';
                $sort_type = '积分（最高）';
                break;
            case 'scorelow':
                $params['sort_type'] = ' price asc';
                $sort_type = '积分（最低）';
                break;
        }
		$price_max = get('price_max', 'int');
		if($price_max > 0) {
			$params['price_max'] = $price_max;
            $sort_type = '我能兑换的';
			$this->Title = '我能兑换的商品列表';
		}
		$list = logic('creditmall')->goods()->get_list($params);

		include handler('template')->file('@creditmall/goods_list' . $this->tplid);
	}

	public function goods_info() {
		$id = get('id', 'int');
		if($id < 1 || false == ($one = logic('creditmall')->goods()->get_one($id))) {
			$this->_msg('商品已经不存在了');
		}

		$this->Title = '商品详情';
		include handler('template')->file('@creditmall/goods_info' . $this->tplid);
	}

	public function order_create() {
		$id = (int) $_REQUEST['id'];
		if($id < 1 || false == ($one = logic('creditmall')->goods()->get_one($id))) {
			$this->_msg('商品已经不存在了');
		}

		if(false != ($data = post('data'))) {
			$ret = logic('creditmall')->order()->create(array(
					'uid' => $this->user['id'],
					'goods_id' => $id,
					'address' => $data['address'],
					'tel' => $data['tel'],
					'qq' => $data['qq']
				));
			if($ret) {
				$this->_msg($ret);
			} else {
				$this->_msg('', '?mod=creditmall&code=order&op=list&3g=1');
			}
		} else {
			$last = logic('creditmall')->order()->get_last($this->user['id']);
		}

		$this->Title = '兑换商品';
		include handler('template')->file('@creditmall/order_create' . $this->tplid);
	}

	public function order_list() {
		$list = logic('creditmall')->order()->get_list(array(
				'uid' => $this->user['id']
			));

		$this->Title = '兑换记录';
		include handler('template')->file('@creditmall/order_list' . $this->tplid);
	}

	public function order_info() {
		$id = get('id', 'number');
		if($id < 1 || false == ($one = logic('creditmall')->order()->get_one($id, true))) {
			$this->_msg('订单已经不存在了');
		}
		if($one['uid'] != $this->user['id']) {
			$this->_msg('您没有权限查看该订单');
		}

		$this->Title = '订单详情';
		include handler('template')->file('@creditmall/order_info' . $this->tplid);
	}

	public function credit_list() {
		$list = logic('credit')->get_list_for_me($this->user['id']);

		$this->Title = '我的积分';
		include handler('template')->file('@creditmall/credit_list' . $this->tplid);
	}

	public function signin() {
		$ret = logic('signin')->signin($this->user['id']);

		$rets = array(
			'1' => '恭喜您，今天获得<i>'.logic('signin')->notice['today_credit'].'</i>积分，明日签到可获得<i>'.logic('signin')->notice['nextday_credit'].'</i>积分！',
			'0' => '签到送积分的功能未开启',
			'-1' => '请先登录或者注册一个账号',
			'-2' => '您已经连续签到<i>'.logic('signin')->notice['signin_day'].'</i>天，明日签到可获得<i>'.logic('signin')->notice['nextday_credit'].'</i>积分',
			'-3' => '签到送积分的规则未设置或积分项为空',
		);
		$ret_msg = ($rets[$ret] ? $rets[$ret] : '未知错误');

		$this->Title = '签到送积分';
		include handler('template')->file('@creditmall/signin' . $this->tplid);
	}

	private function _msg($msg = '', $to = '') {
		if(empty($msg)) {
			if($to) {
				header('Location: ' . $to);
			}
		}

		$this->Title = '消息提示';
		include handler('template')->file('@creditmall/msg' . $this->tplid);
		exit;
	}

	private function _init_user() {
		$api_uid = api_uid();
        if(!$api_uid){
            $api_uid = MEMBER_ID;
        }
		if($api_uid < 1) {
			$this->_msg('需要先登录才能继续操作');
		}
		$this->user = user($api_uid)->get();
		if(false == $this->user || $this->user['id'] < 1) {
			$this->_msg('需要先登录才能继续操作');
		}
	}

}