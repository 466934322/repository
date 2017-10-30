<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name constant.php
 * @date 2015-08-31 16:32:13
 */






define('DBCMax', true);


define('USR_ANY', 0);


define('ORD_ID_ANY', 0);

define('PRO_ACV_Yes', true);

define('PRO_ACV_No', false);

define('PRO_DSP_None', 0);

define('PRO_DSP_City', 1);

define('PRO_DSP_Global', 2);

define('PRO_STA_Failed', 0);

define('PRO_STA_Normal', 1);

define('PRO_STA_Success', 2);

define('PRO_STA_Finish', 3);

define('PRO_STA_Refund', 4);

define('PRO_STA_WillExpire', 5);

define('PRO_STA_Expired', 6);

define('PRO_STA_Alarm', 7);


define('YUNGOU_STA_Normal', 1);

define('YUNGOU_STA_Readied', 2);

define('YUNGOU_STA_Published', 3);


define('PRO_STA_Recommend', 8);


define('PRODUCT_ORDER_BY_DEFAULT', 0);

define('PRODUCT_ORDER_BY_BEGINTIME_DESC', 1);

define('PRODUCT_ORDER_BY_OVERTIME_DESC', 2);

define('PRODUCT_ORDER_BY_SURPLUS_ASC', 3);

define('PRODUCT_ORDER_BY_TOTALSALES_DESC', 4);


define('ORD_PAID_ANY', -1);

define('ORD_PAID_Yes', 1);

define('ORD_PAID_No', 0);

define('ORD_STA_ANY', -1);

define('ORD_STA_Cancel', 0);

define('ORD_STA_Normal', 1);

define('ORD_STA_Overdue', 2);

define('ORD_STA_Failed', 3);

define('ORD_STA_Refund', 4);

define('ORD_STA_Virtual', 200);

define('ORD_STA_Virtual_AlREDAY', 201);


define('TICK_STA_ANY', -1);

define('TICK_STA_Unused', 0);

define('TICK_STA_Used', 1);

define('TICK_STA_Overdue', 2);

define('TICK_STA_Invalid', 3);


define('RECHARGE_STA_Normal', 1);

define('RECHARGE_STA_Blank', 255);


define('RECHARGE_CARD_STA_Normal', 1);


define('IMG_Tiny', -1);

define('IMG_Small', -2);

define('IMG_Normal', -3);

define('IMG_Original', -4);


define('INI_DELETE', microtime());

define('DELIV_SEND_OK', 0);

define('DELIV_SEND_Yes', 1);

define('DELIV_SEND_No', 2);

define('DELIV_PROCESS_IN', 3);


define('EXPORT_GENEALL_FLAG', 'exportGENERATEall');

define('EXPORT_GENEALL_VALUE', 'EGAYes');


define('UI_LOADER_ONCE', true);

?>