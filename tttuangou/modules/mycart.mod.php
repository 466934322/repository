<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name mycart.mod.php
 * @date 2016-05-25 16:51:50
 */


class ModuleObject extends MasterObject
{
    private $cookie_name = '__cart_view_id';
    function ModuleObject( $config )
    {
        $this->MasterObject($config);
        $runCode = Load::moduleCode($this);
        $this->$runCode();
    }
    public function main()
    {
        $upcfg = ini('recharge');
        $bank = ini('bank');
        $maxmoney = intval(user()->get('money'));
        $payaddress = $upcfg['payaddress'] ? $upcfg['payaddress'] : '请电话联系商家确认后再进行操作，否则钱财两空';
        include handler('template')->file('cash');
    }
    
    public function addCart()
    {
        $product_id = get('id', 'int');
        logic('cart_manage')->AddItem($product_id,1);
        $this->Messager('已经成功加入购物车');
    }
	
	
    public function addCart_artdialog()
    {
        $product_id = get('id', 'int');
        $product = dbc(DBCMax)->select('product')->where(array('id'=>$product_id))->limit(1)->done();
        $uid = MEMBER_ID;
                if($product['type'] == 'prize'){
            $this->Messager_artDialog('<b>抽奖产品不能加入购物车</b>');
        }
                if($uid){
            if($product['limit_level'] > 0) {
                $levels = logic('me')->level($uid);
                if($levels['level'] < $product['limit_level']) {
                    $this->Messager_artDialog('<b>您当前的用户等级不能够购买该产品</b>');
                }
            }
        }else{
            if($product['limit_level'] > 0){
                $this->Messager_artDialog('<b>有等级的产品不允许加购物车</b>');
            }
        }
                if($product['promo_cut'] > 0){
            $this->Messager_artDialog('<b>优惠码立减不能加购物车</b>');
        }
                if($product['newbie_cut'] > 0){
            $this->Messager_artDialog('<b>新用户立减不能加购物车</b>');
        }
                if($product['client_cut'] > 0){
            $this->Messager_artDialog('<b>客户端立减产品不允许加购物车</b>');
        }
                if(meta('paymentlist_of_'.$product_id)){
            $this->Messager_artDialog('<b>指定支付产品不允许加购物车</b>');
        }
                if(meta('expresslist_of_'.$product_id)){
            $this->Messager_artDialog('<b>指定配送方式的不允许加购物车</b>');
        }
        logic('cart_manage')->AddItem($product_id,1);

        $this->Messager_artDialog('<b>已经成功加入购物车</b>');
    } 

    public function delCart()
    {
    	$productId = get('id', 'int');
        logic('cart_manage')->RemoveItem($productId);
        $this->Messager('成功删除一条购物车商品');
    }
    
    public function listCart()
    {
        
    }
}