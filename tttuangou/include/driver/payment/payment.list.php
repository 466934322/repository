<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name payment.list.php
 * @date 2016-04-12 18:33:12
 */





return array(
	'alipay' => array(
		'name' => '支付宝',
		'detail' => '【推荐】费率高（1.2%），但最多人使用的支付方式--修改此内容请到后台全局设置/支付设置'
	),
    'alipay2' => array(
        'name' => '支付宝2',
        'detail' => '【推荐】费率高（1.2%），但最多人使用的支付方式--修改此内容请到后台全局设置/支付设置'
    ),
    'alipaymobile' => array(
        'name' => '支付宝移动支付',
        'detail' => '【推荐】手机客户端快捷支付，仅限团购无线客户端使用'
    ),
    'alipaymobile2' => array(
        'name' => '支付宝移动支付2',
        'detail' => '【推荐】手机客户端快捷支付，仅限团购无线客户端使用'
    ),
	'self' => array(
		'name' => '余额支付',
		'detail' => '【推荐】使用本站账户余额进行支付'
	),
	'cod' => array(
		'name' => '货到付款',
		'detail' => '【不推荐】送货上门，当面付款'
	),
	'tenpay' => array(
		'name' => '财付通',
		'detail' => '【不推荐】费率高（1%），需购买年费套餐--修改此内容请到后台全局设置/支付设置'
	),
	'bank' => array(
		'name' => '转账汇款',
		'detail' => '【不推荐】通过ATM机或银行转帐（周期长）'
	),
	'recharge' => array(
		'name' => '充值卡',
		'detail' => '【不推荐】本站自有充值卡充值（用于无网银区域）'
	),
	'chinabank' => array(
		'name' => '网银在线',
		'detail' => '【不推荐】费率高（0.9%），需购买年费套餐，提现周期长--修改此内容请到后台全局设置/支付设置'
	),
	'bankdirect' => array(
		'name' => '网银直连即时到账',
		'detail' => '【推荐】费率超低（0.55%），提现周期短T+1--修改此内容请到后台全局设置/支付设置'
	),
	'kuaibillmobile' => array(
		'name' => '快钱移动支付',
		'detail' => '【推荐】手机客户端快捷支付，仅限团购无线客户端使用'
	),
	
	'yeepay' => array(
		'name' => '易宝一键支付',
		'detail' => '【费率低】有卡就能付，无需开通网银'
	),
	'lianlianpay' => array(
		'name' => '银行卡快捷支付',
		'detail' => '【推荐】无需开通网银也可一键支付，开通门槛低、费率低，支持154家银行，一次开通同时支持web、客户端、wap--修改此内容请到后台全局设置/支付设置'
	),
	
	
	'tenpaywap' => array(
		'name' => '财付通手机支付',
		'detail' => '【推荐】无需开通网银也可一键支付'
	),
	'alipaywap' => array(
		'name' => '支付宝手机WAP支付',
		'detail' => '【推荐】无需开通网银也可一键支付'
	),
    'alipaywap2' => array(
        'name' => '支付宝手机WAP支付2',
        'detail' => '【推荐】无需开通网银也可一键支付'
    ),
	'wxpay' => array(
		'name' => '微信手机WAP支付',
		'detail' => '【推荐】无需开通网银也可一键支付'
	),
    'wxsmpay' => array(
        'name' => '微信PC端扫码支付',
        'detail' => '【推荐】无需开通网银也可一键支付'
    ),
    'wxapppay' => array(
        'name' => 'APP微信支付',
        'detail' => '【推荐】无需开通网银也可一键支付'
    ),
);

?>