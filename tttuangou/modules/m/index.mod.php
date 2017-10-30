<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name index.mod.php
 * @date 2016-05-05 15:32:45
 */





class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	public function main()
	{
		$clientUser = get('u', 'int');
        if(get('uid', 'int')){
            $clientUser = get('uid', 'int');
        }
		if ( $clientUser != '' )
		{
			handler('cookie')->setVar('finderid', $clientUser);
			handler('cookie')->setVar('findtime', time());
		}
        $conf = ini('creditmall');
        $credit_open = $conf['open'];
		$data = logic('product')->display();
				if (!$data && get('page', 'int') == 0)
		{
			header('Location: '.rewrite('?mod=subscribe&code=mail'));
			exit;
		}
		$product = $data['product'];
                if(false == $data['mutiView']){
            $product['sells_out'] = $product['sells_real']+intval($product['virtualnum']);
                        $favorited = logic('favorite')->get_one($product['id']);
        }
		$this->Title = $data['mutiView'] ? '' : $product['name'];
		$data['mutiView'] || mocod('product.view');
		$data['mutiView'] || productCurrentView($product);

				if('home' == $data['file']) {
			$new_product = logic('product')->GetNewList(10, true);
			if(empty($new_product)) {
				$new_product = logic('product')->GetNewList(10);
			}
            foreach($new_product as $k=>$val){
                                $new_product[$k]['sells_out'] = $val['sells_real']+intval($val['virtualnum']);
            }
		}
                if(user()->get('id')){
            $uid = user()->get('id');
        }
		
		if(get('city')) {
			header('Location: ' . ini('settings.site_url') . '/m.php');
		}
		include handler('template')->file('@m/' . $data['file']);
	}
}