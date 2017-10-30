<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name WapNotifyResponseHandler.class.php
 * @date 2015-08-31 16:32:13
 */



class WapNotifyResponseHandler extends ResponseHandler {
	
	function __construct() {
		$this->WapNotifyResponseHandler();
	}
	
	function WapNotifyResponseHandler(){
		parent::ResponseHandler();
	}
	
	function isTenpaySign() {
				$keysArr = array(				
				"ver",
				"charset",
				"bank_type",
				"bank_billno", 				"pay_result",
				"pay_info",     				"purchase_alias",  
				"bargainor_id",  
				"transaction_id",
				"sp_billno",
				"total_fee",
				"fee_type",
				"attach",
				"time_end"
		);	
		return parent::isTenpaySign($keysArr);	
	}
}