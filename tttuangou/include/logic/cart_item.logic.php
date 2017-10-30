<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name cart_item.logic.php
 * @date 2015-08-31 16:32:13
 */





class Cart_itemLogic
{
	
	public function GetOne($id)
	{
		$id = (int)$id;
		if (!$id) return array();
		$ckey = 'cart.getone.'.$id;
		$list = cached($ckey);
		if ($list) return $list;
		return cached($ckey, dbc(DBCMax)->query('SELECT * FROM '.table('cart_item').' WHERE id='.$id)->limit(1)->done());
	}
	
	#@example delete(91);删除当前登录用户，商品号是91的购物车东西
	#@example delete();删除当前登录用户，购物车内所有商品
	public function delete($product_id=0,$cart_id=MEMBER_ID)
	{
		$product_id = (int) $product_id;
		dbc()->SetTable(table('cart_item'));
		if($product_id == 0){
            dbc()->Delete('', 'cart_id='.$cart_id);
        }
		else{
            if(dbc()->Delete('', 'product_id='.$product_id.' and cart_id='.$cart_id)){
                return 1;
            }
        }
	}
}

?>