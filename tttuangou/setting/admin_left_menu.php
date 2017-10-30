<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name admin_left_menu.php
 * @date 2016-05-25 16:51:50
 */

 $menu_list = array (
 1 =>
  array (
	'title' => '快捷菜单',
	'link' => '',
	'sub_menu_list' =>
	array (
	),
  ),

  array (
	'title' => '网站设置',
	'link' => '',
	'sub_menu_list' =>
	array (
	  array (
		'title' => '网站设置',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '核心设置',
		'link' => 'admin.php?mod=setting&code=modify_normal',
		'shortcut' => false,
		'priv' => 'siteset'
	  ),
	  array (
		'title' => '客服信息设置',
		'link' => 'admin.php?mod=widget&code=block&op=config&flag=cservice',
		'shortcut' => false,
		'priv' => 'cserset'
	  ),
	  array (
		'title' => '支付设置',
		'link' => 'admin.php?mod=payment',
		'shortcut' => true,
		'priv' => 'payment'
	  ),
	  array (
		'title' => '短信平台设置',
		'link' => 'admin.php?mod=service&code=sms',
		'shortcut' => true,
		'priv' => 'servicesms'
	  ),
	  array (
		'title' => '通知事件管理',
		'link' => 'admin.php?mod=notify&code=event',
		'shortcut' => false,
		'priv' => 'notifyevent'
	  ),
	  array (
		'title' => '图片上传设置',
		'link' => 'admin.php?mod=upload&code=config',
		'shortcut' => false,
		'priv' => 'upload'
	  ),
	  array (
		'title' => '图片水印',
		'link' => 'admin.php?mod=image&code=watermark',
		'shortcut' => false,
		'priv' => 'watermark'
	  ),
	  array (
		'title' => '站点Logo',
		'link' => 'admin.php?mod=tttuangou&code=sitelogo',
		'shortcut' => false,
		'priv' => 'sitelogo'
	  ),
	  array (
		'title' => '分享设置',
		'link' => 'admin.php?mod=tttuangou&code=shareconfig',
		'shortcut' => false,
		'priv' => 'share'
	  ),
	  array (
		'title' => '一键登录',
		'link' => 'admin.php?mod=ulogin',
		'shortcut' => false,
		'priv' => 'ulogin'
	  ),
   	  array (
		'title' => '微信登录',
		'link' => 'admin.php?mod=wxlogin',
		'shortcut' => false,
		'priv' => 'wxlogin'
	  ),
        array (
            'title' => 'app微信登录',
            'link' => 'admin.php?mod=wxapplogin',
            'shortcut' => false,
            'priv' => 'wxlogin'
        ),
	  array (
		'title' => 'Ucenter整合',
		'link' => 'admin.php?mod=ucenter',
		'shortcut' => false,
		'priv' => 'ucenter'
	  ),
	  array (
		'title' => '伪静态',
		'link' => 'admin.php?mod=setting&code=modify_rewrite',
		'shortcut' => false,
		'priv' => 'rewrite'
	  ),
	  array (
		'title' => '内容过滤',
		'link' => 'admin.php?mod=setting&code=modify_filter',
		'shortcut' => false,
		'priv' => 'filter'
	  ),
	  array (
		'title' => 'IP访问控制',
		'link' => 'admin.php?mod=setting&code=modify_access',
		'shortcut' => false,
		'priv' => 'ipset'
	  ),


	  array (
		'title' => '客户端设置',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '移动应用管理',
		'link' => 'admin.php?mod=api',
		'shortcut' => false,
		'priv' => 'apimanage'
	  ),
	  array (
		'title' => '客户端版本管理',
		'link' => 'admin.php?mod=api&code=release',
		'shortcut' => false,
		'priv' => 'apimanage'
	  ),
	  array (
		'title' => '移动支付管理',
		'link' => 'admin.php?mod=setting&code=mnote',
		'shortcut' => false,
		'priv' => 'apimanage'
	  ),
	  array (
		'title' => '客户端下载管理',
		'link' => 'admin.php?mod=app',
		'shortcut' => false,
		'priv' => 'appmanage'
	  ),
	  array (
		'title' => TUANGOU_STR . '客户端介绍',
		'link' => ihelper('tg.app.android'),
		'shortcut' => false,
		'priv' => 'apimanage'
	  ),

	),
  ),

  array (
	'title' => '常用设置',
	'link' => '',
	'sub_menu_list' =>
	array (
	  array (
		'title' => '短信/邮件设置',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '短信平台设置',
		'link' => 'admin.php?mod=service&code=sms',
		'shortcut' => true,
		'priv' => 'servicesms'
	  ),
	  array (
		'title' => '网站推送日志',
		'link' => 'admin.php?mod=push&code=log&type=sms',
		'shortcut' => false,
		'priv' => 'push'
	  ),
	  array (
		'title' => '通知事件管理',
		'link' => 'admin.php?mod=notify&code=event',
		'shortcut' => false,
		'priv' => 'notifyevent'
	  ),
	  array (
		'title' => '群发服务管理',
		'link' => 'admin.php?mod=service',
		'shortcut' => false,
		'priv' => 'service'
	  ),
	  array (
		'title' => '订阅管理',
		'link' => 'admin.php?mod=subscribe',
		'shortcut' => false,
		'priv' => 'subscribe'
	  ),
	  array (
		'title' => '订阅群发',
		'link' => 'admin.php?mod=subscribe&code=broadcast&class=mail',
		'shortcut' => false,
		'priv' => 'subscribemail'
	  ),
	  array (
		'title' => '通知方式',
		'link' => 'admin.php?mod=notify',
		'shortcut' => false,
		'priv' => 'notify'
	  ),
	  array (
		'title' => '其他常用设置',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '顶部导航设置',
		'link' => 'admin.php?mod=tttuangou&code=indexnav',
		'shortcut' => false,
		'priv' => 'navset'
	  ),
	  array (
		'title' => '广告设置',
		'link' => 'admin.php?mod=ad&code=vlist',
		'shortcut' => false,
		'priv' => 'adset'
	  ),
	  array (
		'title' => '侧边栏管理',
		'link' => 'admin.php?mod=widget',
		'shortcut' => false,
		'priv' => 'widget'
	  ),
	  array (
		'title' => '静态页面管理',
		'link' => 'admin.php?mod=html&code=front',
		'shortcut' => false,
		'priv' => 'htmlset'
	  ),
	  array (
		'title' => '模板设置',
		'link' => 'admin.php?mod=styles&code=temp',
		'shortcut' => true,
		'priv' => 'templateset'
	  ),
	  array (
		'title' => '皮肤设置',
		'link' => 'admin.php?mod=styles&code=vlist',
		'shortcut' => true,
		'priv' => 'styles'
	  ),
	  array (
		'title' => '多团设置',
		'link' => 'admin.php?mod=ui&code=igos&op=config',
		'shortcut' => false,
		'priv' => 'uiigos'
	  ),
	  array (
		'title' => TUANGOU_STR . '券设置',
		'link' => 'admin.php?mod=coupon&code=config',
		'shortcut' => false,
		'priv' => 'coupon'
	  ),
	  array (
		'title' => '友情链接',
		'link' => 'admin.php?mod=link',
		'shortcut' => false,
		'priv' => 'link'
	  ),

	),
  ),





  array (
	'title' => TUANGOU_STR . '管理',
	'link' => '',
	'sub_menu_list' =>
	array (
	array (
		'title' => TUANGOU_STR . '设置',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => TUANGOU_STR . '默认设置',
		'link' => 'admin.php?mod=tttuangou&code=varshow',
		'shortcut' => true,
		'priv' => 'shopset'
	  ),
	  array (
		'title' => '城市区域设置',
		'link' => 'admin.php?mod=tttuangou&code=city',
		'shortcut' => false,
		'priv' => 'city'
	  ),
	  array (
		'title' => '分类管理',
		'link' => 'admin.php?mod=catalog',
		'shortcut' => false,
		'priv' => 'catalog'
	  ),

	  array (
		'title' => TUANGOU_STR . '管理',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '添加产品',
		'link' => 'admin.php?mod=product&code=add&~iiframe=yes',
		'shortcut' => false,
		'priv' => 'product'
	  ),
	  array (
		'title' => '产品管理',
		'link' => 'admin.php?mod=product',
		'shortcut' => true,
		'priv' => 'product'
	  ),
	  array (
		'title' => '订单管理',
		'link' => 'admin.php?mod=order',
		'shortcut' => true,
		'priv' => 'ordermanage'
	  ),
	  array (
		'title' => TUANGOU_STR . '券管理',
		'link' => 'admin.php?mod=coupon',
		'shortcut' => false,
		'priv' => 'coupon'
	  ),
	  array (
		'title' => '添加套餐',
		'link' => 'admin.php?mod=product&code=addlink&~iiframe=yes',
		'shortcut' => false,
		'priv' => 'product'
	  ),
	  array (
		'title' => '套餐管理',
		'link' => 'admin.php?mod=product&code=linklist&~iiframe=yes',
		'shortcut' => true,
		'priv' => 'product'
	  ),
	  array (
		'title' => '发货管理',
		'link' => 'admin.php?mod=delivery&code=vlist&ordproc=WAIT_SELLER_SEND_GOODS',
		'shortcut' => true,
		'priv' => 'delivery'
	  ),
	  array (
		'title' => '配送管理',
		'link' => 'admin.php?mod=express',
		'shortcut' => false,
		'priv' => 'express'
	  ),
	  array (
		'title' => '快递单打印',
		'link' => 'admin.php?mod=print&code=delivery&op=queue',
		'shortcut' => true,
		'priv' => 'print'
	  ),
	  array (
		'title' => '抽奖管理',
		'link' => 'admin.php?mod=prize&code=vlist',
		'shortcut' => false,
		'priv' => 'prize'
	  ),
	  array (
	  	'title' => '云购管理',
		'link' => 'admin.php?mod=yungou&code=vlist',
		'shortcut' => false,
		'priv' => 'yungou'
	  ),
	  array (
		'title' => '标签管理',
		'link' => 'admin.php?mod=tag',
		'shortcut' => false,
		'priv' => 'tag'
	  ),
	  array (
		'title' => '专题管理',
		'link' => 'admin.php?mod=special',
		'shortcut' => false,
		'priv' => 'special'
	  ),
	),
  ),

  array (
	'title' => '商家和用户管理',
	'link' => '',
	'sub_menu_list' =>
	array (
	  array (
		'title' => '商家设置',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '商家结算设置',
		'link' => 'admin.php?mod=fund&code=config',
		'shortcut' => false,
		'priv' => 'fundset'
	  ),
	  array (
		'title' => '商家分成设置',
		'link' => 'admin.php?mod=rebate_setting&code=show',
		'shortcut' => false,
		'priv' => 'rebate'
	  ),

	  array (
		'title' => '商家管理',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '添加商家',
		'link' => 'admin.php?mod=tttuangou&code=addseller',
		'shortcut' => false,
		'priv' => 'seller'
	  ),
	  array (
		'title' => '商家管理',
		'link' => 'admin.php?mod=tttuangou&code=mainseller',
		'shortcut' => true,
		'priv' => 'seller'
	  ),
	  array (
		'title' => '结算金额明细',
		'link' => 'admin.php?mod=fund&code=money',
		'shortcut' => false,
		'priv' => 'fundorder'
	  ),
	  array (
		'title' => '商家结算管理',
		'link' => 'admin.php?mod=fund&code=order',
		'shortcut' => false,
		'priv' => 'fundorder'
	  ),
	  
	  array (
		'title' => '用户设置',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '用户注册设置',
		'link' => 'admin.php?mod=account&code=config',
		'shortcut' => false,
		'priv' => 'userreg'
	  ),
	  array (
		'title' => '用户等级设置',
		'link' => 'admin.php?mod=account&code=credits',
		'shortcut' => false,
		'priv' => 'credits'
	  ),

	  array (
		'title' => '用户管理',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '添加新用户',
		'link' => 'admin.php?mod=member&code=add',
		'shortcut' => false,
		'priv' => 'memberadd'
	  ),
	  array (
		'title' => '搜索/编辑用户',
		'link' => 'admin.php?mod=member&code=search',
		'shortcut' => false,
		'priv' => 'memberedite'
	  ),
	  array (
		'title' => '用户提现管理',
		'link' => 'admin.php?mod=cash&code=order',
		'shortcut' => false,
		'priv' => 'cashorder'
	  ),
	  array (
		'title' => '充值订单管理',
		'link' => 'admin.php?mod=recharge&code=order',
		'shortcut' => false,
		'priv' => 'rechargeorder'
	  ),
	  array (
		'title' => '退款申请管理',
		'link' => 'admin.php?mod=refund',
		'shortcut' => false,
		'priv' => 'refund'
	  ),
	  array (
		'title' => '用户消费明细',
		'link' => 'admin.php?mod=member&code=moneylog',
		'shortcut' => false,
		'priv' => 'member_moneylog'
	  ),
	  array (
		'title' => '当前在线用户',
		'link' => 'admin.php?mod=sessions',
		'shortcut' => false,
		'priv' => 'sessions'
	  ),
	),
  ),


    array (
	'title' => '营销功能',
	'link' => '',
	'sub_menu_list' =>
	array (
	  array (
		'title' => '积分设置',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '普通积分设置',
		'link' => 'admin.php?mod=account&code=credit',
		'shortcut' => false,
		'priv' => 'creditset'
	  ),
	  array (
		'title' => '签到积分设置',
		'link' => 'admin.php?mod=signin',
		'shortcut' => false,
		'priv' => 'signin'
	  ),
        array (
            'title' => '积分转增设置',
            'link' => 'admin.php?mod=creditmall&code=credit_transfer',
            'shortcut' => false,
            'priv' => 'signin'
        ),
        array (
            'title' => '积分购买设置',
            'link' => 'admin.php?mod=creditmall&code=credit_buy',
            'shortcut' => false,
            'priv' => 'creditmall'
        ),

	  array (
		'title' => '积分商城',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '积分商城设置',
		'link' => 'admin.php?mod=creditmall',
		'shortcut' => false,
		'priv' => 'creditmall'
	  ),
      array (
        'title' => '商品分类管理',
        'link' => 'admin.php?mod=creditmall&code=goods_category',
        'shortcut' => false,
        'priv' => 'goods_category'
      ),
	  array (
		'title' => '添加商城礼品',
		'link' => 'admin.php?mod=creditmall&code=goods&op=info&act=add',
		'shortcut' => false,
		'priv' => 'creditmall'
	  ),
	  array (
		'title' => '商城礼品管理',
		'link' => 'admin.php?mod=creditmall&code=goods&op=list',
		'shortcut' => false,
		'priv' => 'creditmall'
	  ),
	  array (
		'title' => '兑换订单管理',
		'link' => 'admin.php?mod=creditmall&code=order&op=list',
		'shortcut' => false,
		'priv' => 'creditmall'
	  ),

	  array (
		'title' => '送红包设置',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '摇一摇送红包',
		'link' => 'admin.php?mod=hongbao',
		'shortcut' => false,
		'priv' => 'hongbao'
	  ),

	  array (
		'title' => '专题活动设置',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '专题管理',
		'link' => 'admin.php?mod=special',
		'shortcut' => false,
		'priv' => 'special'
	  ),

	  array (
		'title' => '抽奖活动',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '添加产品',
		'link' => 'admin.php?mod=product&code=add&~iiframe=yes',
		'shortcut' => false,
		'priv' => 'product'
	  ),
	  array (
		'title' => '抽奖管理',
		'link' => 'admin.php?mod=prize&code=vlist',
		'shortcut' => false,
		'priv' => 'prize'
	  ),

	  array (
		'title' => '优惠码管理',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '优惠码管理',
		'link' => 'admin.php?mod=promo_code',
		'shortcut' => false,
		'priv' => 'product'
	  ),

	  array (
		'title' => '充值卡设置',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '生成充值卡',
		'link' => 'admin.php?mod=recharge&code=card&op=generate',
		'shortcut' => false,
		'priv' => 'rechargecard'
	  ),
	  array (
		'title' => '充值卡管理',
		'link' => 'admin.php?mod=recharge&code=card',
		'shortcut' => false,
		'priv' => 'rechargecard'
	  ),

	  array (
		'title' => '网站返利设置',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '邀请返利设置',
		'link' => 'admin.php?mod=recharge&code=scale',
		'shortcut' => false,
		'priv' => 'rechargescale'
	  ),
	  array (
		'title' => '购物返现设置',
		'link' => 'admin.php?mod=recharge&code=buy_rebate',
		'shortcut' => false,
		'priv' => 'rechargeset'
	  ),
	  array (
		'title' => '充值返现设置',
		'link' => 'admin.php?mod=recharge&code=config',
		'shortcut' => false,
		'priv' => 'rechargeset'
	  ),
	),
  ),

  array (
	'title' => '互动与统计',
	'link' => '',
	'sub_menu_list' =>
	array (
	  
	  array (
		'title' => '用户互动管理',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '问答管理',
		'link' => 'admin.php?mod=tttuangou&code=mainquestion',
		'shortcut' => false,
		'priv' => 'question'
	  ),
	  array (
		'title' => '反馈信息',
		'link' => 'admin.php?mod=tttuangou&code=usermsg',
		'shortcut' => false,
		'priv' => 'usermsg'
	  ),
	  array (
		'title' => '评论管理',
		'link' => 'admin.php?mod=comment&code=vlist',
		'shortcut' => false,
		'priv' => 'comments'
	  ),
	  array (
		'title' => '文章管理',
		'link' => 'admin.php?mod=article',
		'shortcut' => false,
		'priv' => 'article'
	  ),

	  array (
		'title' => '网站统计',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '产品销售报表',
		'link' => 'admin.php?mod=salecount&code=product',
		'shortcut' => false,
		'priv' => 'salecount'
	  ),
	  array (
		'title' => '支付接口报表',
		'link' => 'admin.php?mod=salecount&code=payment',
		'shortcut' => false,
		'priv' => 'salecount'
	  ),
	  array (
		'title' => '用户消费报表',
		'link' => 'admin.php?mod=salecount&code=user',
		'shortcut' => false,
		'priv' => 'salecount'
	  ),
	  array (
		'title' => '产品结算报表',
		'link' => 'admin.php?mod=salecount&code=fund',
		'shortcut' => false,
		'priv' => 'salecount'
	  ),
	  array (
		'title' => '财务报表',
		'link' => 'admin.php?mod=reports',
		'shortcut' => false,
		'priv' => 'reports'
	  ),
	  array (
		'title' => '网站推送日志',
		'link' => 'admin.php?mod=push&code=log&type=sms',
		'shortcut' => false,
		'priv' => 'push'
	  ),
	  array (
		'title' => '当前在线人数',
		'link' => 'admin.php?mod=sessions',
		'shortcut' => false,
		'priv' => 'sessions'
	  ),
	  array (
		'title' => '数据调用',
		'link' => 'hr',
		'shortcut' => false
	  ),	  
	  array (
		'title' => '外部调用',
		'link' => 'admin.php?mod=tttuangou&code=dataapi',
		'shortcut' => false,
		'priv' => 'dataapi'
	  ),
	),
  ),


  array (
	'title' => '系统工具',
	'link' => '',
	'sub_menu_list' =>
	array (

	  array (
		'title' => '系统工具',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '清空缓存',
		'link' => 'admin.php?mod=cache',
		'shortcut' => false,
		'priv' => 'cache'
	  ),
	  array (
		'title' => '在线升级',
		'link' => 'admin.php?mod=upgrade',
		'shortcut' => true,
		'priv' => 'upgrade'
	  ),
	  array (
		'title' => '更新记录',
		'link' => 'http:/'.'/tg.tttuangou.net/changelog.htm?'.ini('settings.site_url'),
		'shortcut' => false,
		'priv' => ''
	  ),
	  array (
		'title' => '错误调试',
		'link' => 'admin.php?mod=dev',
		'shortcut' => false,
		'priv' => 'dev'
	  ),
	  array (
		'title' => '日志中心',
		'link' => 'admin.php?mod=zlog',
		'shortcut' => true,
		'priv' => 'zlog'
	  ),
	  array (
		'title' => '入侵检测',
		'link' => 'admin.php?mod=wips',
		'shortcut' => false,
		'priv' => 'wips'
	  ),
	  array (
		'title' => '文件校验',
		'link' => 'admin.php?mod=filecheck',
		'shortcut' => false,
		'priv' => 'filecheck'
	  ),

	  array (
		'title' => '数据库',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '数据备份',
		'link' => 'admin.php?mod=db&code=export',
		'shortcut' => false,
		'priv' => 'dbexport'
	  ),
	  array (
		'title' => '数据恢复',
		'link' => 'admin.php?mod=db&code=import',
		'shortcut' => false,
		'priv' => 'dbimport'
	  ),
	  array (
		'title' => '数据表优化',
		'link' => 'admin.php?mod=db&code=optimize',
		'shortcut' => false,
		'priv' => 'dboptimize'
	  ),
	  array (
		'title' => '数据库修复',
		'link' => 'admin.php?mod=db&code=repair',
		'shortcut' => false,
		'priv' => 'dbrepair'
	  ),
	  array (
		'title' => '数据初始化',
		'link' => 'admin.php?mod=tttuangou&code=clear',
		'shortcut' => false,
		'priv' => 'dataclear'
	  ),

	  array (
		'title' => '站点信息',
		'link' => 'hr',
		'shortcut' => false
	  ),
	  array (
		'title' => '蜘蛛爬行统计',
		'link' => 'admin.php?mod=robot',
		'shortcut' => false,
		'priv' => 'robot'
	  ),
	),
  ),



  
);
?>