<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name admin_privs.php
 * @date 2015-11-12 16:33:06
 */


$privs_list = array (
array ('title' => '网站设置','sub_priv_list' =>
	array (
	array ('title' => '核心设置','priv' => 'siteset'),
	array ('title' => '客服信息设置','priv' => 'cserset'),
	array ('title' => '支付设置','priv' => 'payment'),
	array ('title' => '短信平台设置','priv' => 'servicesms'),
	array ('title' => '图片上传设置','priv' => 'upload'),
	array ('title' => '图片水印','priv' => 'watermark'),
	array ('title' => '站点Logo','priv' => 'sitelogo'),
	array ('title' => '分享设置','priv' => 'share'),
	array ('title' => '一键登录','priv' => 'ulogin'),
	array ('title' => 'Ucenter整合','priv' => 'ucenter'),
	array ('title' => '伪静态','priv' => 'rewrite'),
	array ('title' => '内容过滤','priv' => 'filter'),
	array ('title' => 'IP访问控制','priv' => 'ipset'),
	 )
	),
  array ('title' => '客户端设置','sub_priv_list' =>
	array (
	array ('title' => '移动应用管理','priv' => 'apimanage'),
	array ('title' => '客户端下载管理','priv' => 'appmanage')
	)
  ),
  array ('title' => '常用设置','sub_priv_list' =>
	array (	
	array ('title' => '短信平台设置','priv' => 'servicesms'),
	array ('title' => '通知事件管理','priv' => 'notifyevent'),
	array ('title' => '群发服务管理','priv' => 'service'),
	array ('title' => '订阅管理','priv' => 'subscribe'),
	array ('title' => '订阅群发','priv' => 'subscribemail'),
	array ('title' => '通知方式','priv' => 'notify'),
	array ('title' => '顶部导航设置','priv' => 'navset'),
	array ('title' => '广告设置','priv' => 'adset'),
	array ('title' => '通知事件管理','priv' => 'notifyevent'),
	array ('title' => '侧边栏管理','priv' => 'widget'),
	array ('title' => '静态页面管理','priv' => 'htmlset'),
	array ('title' => '模板设置','priv' => 'templateset'),
	array ('title' => '皮肤设置','priv' => 'styles'),
	array ('title' => '多团设置','priv' => 'uiigos'),
	array ('title' => TUANGOU_STR . '券设置','priv' => 'coupon'),
	array ('title' => '友情链接','priv' => 'link'),
	)
  ),

array ('title' => TUANGOU_STR . '管理','sub_priv_list' =>
	array (
	array ('title' => '团购默认设置','priv' => 'shopset'),
	array ('title' => '城市区域设置','priv' => 'city'),
	array ('title' => '分类管理','priv' => 'catalog'),
	array ('title' => '产品/套餐管理','priv' => 'product'),
	array ('title' => '产品推荐权限','priv' => 'producthot'),
	array ('title' => '订单管理','priv' => 'ordermanage'),
	array ('title' => '订单删除','priv' => 'orderdelete'),
	array ('title' => TUANGOU_STR . '券管理','priv' => 'coupon'),
	array ('title' => '发货管理','priv' => 'delivery'),
	array ('title' => '配送管理','priv' => 'express'),
	array ('title' => '快递单打印','priv' => 'print'),
	array ('title' => '抽奖管理','priv' => 'prize'),
	array ('title' => '标签管理','priv' => 'tag'),
	array ('title' => '专题管理','priv' => 'special'),
    array ('title' => '云购管理','priv' => 'yungou'),
	)
  ),
array ('title' => '商家管理','sub_priv_list' =>
	array (
	array ('title' => '商家分成设置','priv' => 'rebate'),
	array ('title' => '商家管理','priv' => 'seller'),
	array ('title' => '商家结算管理','priv' => 'fundorder'),
	array ('title' => '商家结算设置','priv' => 'fundset'),
	array ('title' => '商家分成设置','priv' => 'rebate')
	)
  ),
  array ('title' => '用户管理','sub_priv_list' =>
	array (
	array ('title' => '用户注册设置','priv' => 'userreg'),
	array ('title' => '用户等级设置','priv' => 'credits'),
	array ('title' => '添加新用户','priv' => 'memberadd'),
	array ('title' => '搜索/编辑用户','priv' => 'memberedite'),
	array ('title' => '用户提现管理','priv' => 'cashorder'),
	array ('title' => '充值订单管理','priv' => 'rechargeorder'),
	array ('title' => '退款申请管理','priv' => 'refund'),
	array ('title' => '权限设置','priv' => 'privs'),
	array ('title' => '快捷充值/扣费','priv' => 'quickrecharge'),
	array ('title' => '用户消费明细','priv' => 'member_moneylog'),
	array ('title' => '当前在线用户','priv' => 'sessions'),
	)
  ),

  array ('title' => '营销功能','sub_priv_list' =>
	array (
	array ('title' => '普通积分设置','priv' => 'creditset'),
	array ('title' => '签到积分设置','priv' => 'signin'),
	array ('title' => '积分商城管理','priv' => 'creditmall'),
	array ('title' => '摇一摇送红包','priv' => 'hongbao'),
	array ('title' => '专题管理','priv' => 'special'),
	array ('title' => '抽奖管理','priv' => 'prize'),
	array ('title' => '充值卡管理','priv' => 'rechargecard'),
	array ('title' => '邀请返利设置','priv' => 'rechargescale'),
	array ('title' => '购物/充值返现设置','priv' => 'rechargeset'),
	)
  ),

  array ('title' => '互动与统计','sub_priv_list' =>
	array (
	array ('title' => '问答管理','priv' => 'question'),
	array ('title' => '反馈信息','priv' => 'usermsg'),
	array ('title' => '评论管理','priv' => 'comments'),
	array ('title' => '管理员虚拟评论','priv' => 'virtual_comments'),
	array ('title' => '文章管理','priv' => 'article'),

	array ('title' => '财务报表','priv' => 'reports'),
	array ('title' => '报表统计','priv' => 'salecount'),
	array ('title' => '推送管理','priv' => 'push'),

	array ('title' => '当前在线人数','priv' => 'sessions'),
	)
  ),
array ('title' => '系统工具','sub_priv_list' =>
	array (
	array ('title' => '清空缓存','priv' => 'cache'),
	array ('title' => '在线升级','priv' => 'upgrade'),
	array ('title' => '错误调试','priv' => 'dev'),
	array ('title' => '日志中心','priv' => 'zlog'),
	array ('title' => '入侵检测','priv' => 'wips'),
	array ('title' => '文件校验','priv' => 'filecheck'),
	array ('title' => '数据备份','priv' => 'dbexport'),
	array ('title' => '数据恢复','priv' => 'dbimport'),
	array ('title' => '数据表优化','priv' => 'dboptimize'),
	array ('title' => '数据库修复','priv' => 'dbrepair'),
	array ('title' => '数据初始化','priv' => 'dataclear'),
	array ('title' => '蜘蛛爬行统计','priv' => 'robot')
	)
  ),
);
?>