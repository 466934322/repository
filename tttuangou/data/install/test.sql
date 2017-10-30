/*
SQLyog Enterprise - MySQL GUI v8.1 
MySQL - 5.0.51b-community-nt : Database - tgmax_pub
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Data for the table `{prefix}system_members` */

insert  into `{prefix}system_members`(`uid`,username,password,secques,gender,adminid,regip,regdate,lastip,lastvisit,lastactivity,lastpost,oltime,pageviews,credits,extcredits1,extcredits2,email,bday,sigstatus,tpp,ppp,styleid,dateformat,timeformat,pmsound,showemail,newsletter,invisible,timeoffset,newpm,accessmasks,face,tag_count,role_id,role_type,new_msg_count,tag,own_tags,login_count,truename,phone,last_year_rank,last_month_rank,last_week_rank,this_year_rank,this_month_rank,this_week_rank,last_year_credit,last_month_credit,last_week_credit,this_year_credit,this_month_credit,this_week_credit,view_times,use_tag_count,create_tag_count,image_count,noticenum,ucuid,invite_count,invitecode,province,city,topic_count,at_count,follow_count,fans_count,email2,qq,msn,aboutme,at_new,comment_new,fans_new,topic_favorite_count,tag_favorite_count,disallow_beiguanzhu,`validate`,favoritemy_new,money,checked,finder,findtime,totalpay) values (2,'cenwor','e10adc3949ba59abbe56e057f20f883e','',0,0,'',0,'',0,0,0,0,0,0,0,0,'demo@name.com','0000-00-00',0,0,0,0,'',0,0,0,0,0,'',0,0,'',0,0,'seller',0,'',0,0,'','',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'','','',0,0,0,0,'','','','',0,0,0,0,0,0,0,0,'0.00',0,0,0,'0.00'),(3,'jishigou','e10adc3949ba59abbe56e057f20f883e','',0,0,'',0,'60.177.179.175',1303880709,1303880709,0,0,0,0,0,0,'demo@name.com','0000-00-00',0,0,0,0,'',0,0,0,0,0,'',0,0,'',0,0,'normal',0,'',0,1,'','',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'','','',0,0,0,0,'','','','',0,0,0,0,0,0,0,0,'0.00',0,0,0,'0.00'),(4,'tttuangou','e10adc3949ba59abbe56e057f20f883e','',0,0,'',0,'127.0.0.1',1303880709,1303880709,0,0,0,0,0,0,'tows@apiz.org','0000-00-00',0,0,0,0,'',0,0,0,0,0,'',0,0,'',0,0,'nomal',0,'',0,1,'','',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'','','',0,0,0,0,'','','','',0,0,0,0,0,0,0,0,'0.00',0,0,0,'0.00');

/*Data for the table `{prefix}tttuangou_address` */

insert  into `{prefix}tttuangou_address`(id,owner,name,region,address,zip,phone,lastuse) values (1,1,'神话',',3133,3134,3139,','杭州市西湖区','300013','1388888888',UNIX_TIMESTAMP());

/*Data for the table `{prefix}tttuangou_express` */

insert  into `{prefix}tttuangou_express`(id,`name`,express,firstunit,firstprice,continueunit,continueprice,regiond,dpenable,detail,`order`,enabled) values (1,'全国包邮',4,1000,'0.00',1000,'0.00',0,'false','全国包邮啦',1,'true');

/*Data for the table `{prefix}tttuangou_order` */

insert  into `{prefix}tttuangou_order`(orderid,productid,productnum,productprice,totalprice,userid,addressid,buytime,paytype,paymoney,pay,paytime,expresstype,expressprice,invoice,expresstime,extmsg,process,status,remark) values (2011042729691,2,1,'298.00','0.00',1,0,UNIX_TIMESTAMP()-3600,1,'0.00',1,UNIX_TIMESTAMP(),0,'0.00','',0,'爱聚合','TRADE_FINISHED',1,NULL),(2011042735431,3,1,'9999.00','0.00',1,1,UNIX_TIMESTAMP()-3300,1,'0.00',1,UNIX_TIMESTAMP(),1,'0.00','CN123456789',UNIX_TIMESTAMP()-1800,'好业宝','TRADE_FINISHED',1,NULL);

/*Data for the table `{prefix}tttuangou_order_clog` */

insert  into `{prefix}tttuangou_order_clog`(id,`sign`,action,`uid`,remark,`time`) values (1,2011042729691,'confirm',1,'[确认订单] 确认收到298元',UNIX_TIMESTAMP()),(2,2011042735431,'confirm',1,'[确认订单] 确认收到9999元',UNIX_TIMESTAMP());

/*Data for the table `{prefix}tttuangou_product` */

insert  into `{prefix}tttuangou_product`(id,sellerid,city,name,flag,price,nowprice,img,intro,content,cue,theysay,wesay,begintime,overtime,`type`,perioddate,weight,successnum,virtualnum,maxnum,oncemax,oncemin,multibuy,allinone,totalnum,display,addtime,status,`order`) values (2,1,1,'天天外卖系统V1.0版本','天天外卖系统V1.0版本','10000','8000','3,4','完美配合天天团购程序，同时又可独立部署，手机wap，安卓，苹果多平台支持 ','<p>
	<span style="font-size:24px;">现在面临的问题？</span> 
</p>
<p>
	1.美团糯米的压力，团购市场被挤压
</p>
2.上门和外卖的兴起，团购网站寻求新的转型<br />
3.团购已经等同于低价，很难再开发出新概念<br />
<p>
	4.用户使用团购频率低，资金压力大
</p>
<p>
	<br />
</p>
<p>
	<span style="font-size:24px;">为什么要做外卖？</span> 
</p>
<p>
	1.团购商户可共享，开辟市场容易<br />
2.外卖使用频率高，用户粘度高<br />
3.帮助小商户解决名气低，生意难做的问题<br />
4.餐饮、鲜花、药品、便利店、面包店、菜店等，都能成为你的客户
</p>
<p>
	<br />
</p>
<p>
	<span style="font-size:24px;">天天外卖可以帮你解决的问题</span> 
</p>
<p>
	帮助团购网站开拓外卖新市场 / 帮助传统餐饮行业转型
</p>
<p>
	了解天天外卖系统详情，请咨询微信客服“tttuangou”，或者登陆官网www.cenwor.com了解
</p>
<br />',' ',' ',' ',UNIX_TIMESTAMP()-86400,UNIX_TIMESTAMP()+259200,'ticket',UNIX_TIMESTAMP()+604800,0,1,39,0,1,1,'true','true',1,2,UNIX_TIMESTAMP(),2,0),(3,1,1,'要想业务好，就用好业宝！一站式、智能化的移动电商和营销平台','好业宝~移动电商和营销平台','10000.00','999.00','5,6,7,8','好业宝是一套智能化的“移动电商和微营销”平台，致力于帮助每家中小企业搭建自己的用户池塘（转型升级进入粉丝经济时代），并持续实现收入倍增。','好业宝“移动电商和微营销”平台，致力于帮助企业快速获取精准的目标用户、留住来访的每个用户、吸引用户成交转化、根据消费周期引导复购，并通过三级分销让用户转介绍新用户--》最终让企业收入持续倍增，访问<a href=\"http://www.haoyebao.com\" target=\"_blank\">好业宝</a>了解详情','<div id=\"com_v\" class=\"boxCenterList RelaArticle\"><p>现在免费加入，店铺免费、功能也免费。</p>\r\n</div>','用户拉新、留存转化、成交复购、三级分销、业务倍增，谁用谁知道','<p sizcache=\"298\" sizset=\"33\">微营销、微网店、o2o模式、微crm系统，这里全都有</p>',UNIX_TIMESTAMP()-86400,UNIX_TIMESTAMP()+604800,'stuff',UNIX_TIMESTAMP(),1000,1,123,0,1,1,'true','true',1,2,UNIX_TIMESTAMP(),2,10);

/*Data for the table `{prefix}tttuangou_push_queue` */

insert  into `{prefix}tttuangou_push_queue`(id,`type`,target,`data`,rund,`result`,`update`,pr) values (1,'mail','admin@cenwor.com','a:2:{s:7:\"subject\";s:28:\"天天团购系统 提示您\";s:7:\"content\";s:91:\"感谢您的购买\n订单号：2011042729691\n团购券编号：870629518244\n密码：722124\";}','false',NULL,UNIX_TIMESTAMP(),9);

/*Data for the table `{prefix}tttuangou_question` */

insert  into `{prefix}tttuangou_question`(id,userid,username,content,reply,`time`) values (1,3,'好业宝','你们可以帮助安装吗？','可以的。购买后，提供ftp信息、数据库信息，几分钟即可帮你安装好。',UNIX_TIMESTAMP());

/*Data for the table `{prefix}tttuangou_seller` */

insert  into `{prefix}tttuangou_seller`(id,userid,sellername,sellerphone,selleraddress,sellerurl,sellermap,area,productnum,successnum,money,`time`) values (1,2,'杭州神话信息技术有限公司','0571-88800819','浙江杭州市西湖区
','http://cenwor.com/','',1,3,2,'10297.00',UNIX_TIMESTAMP());

/*Data for the table `{prefix}tttuangou_subscribe` */

insert  into `{prefix}tttuangou_subscribe`(id,`type`,target,city,`time`) values (1,'mail','admin@biniu.com',1,UNIX_TIMESTAMP()),(2,'sms','13888888888',1,UNIX_TIMESTAMP());

/*Data for the table `{prefix}tttuangou_ticket` */

insert  into `{prefix}tttuangou_ticket`(ticketid,`uid`,productid,orderid,`number`,password,usetime,mutis,status) values (1,1,2,2011042729691,'870629518244','722124','0000-00-00 00:00:00',1,0);

/*Data for the table `{prefix}tttuangou_uploads` */

insert  into `{prefix}tttuangou_uploads`(id,name,intro,`path`,url,`type`,`size`,mime,extra,`uid`,ip,`update`) values (1,'91ab162b046.jpg','网站宝','./uploads/demo/1ba6cc89c53fb45e337127a668af9f5d.jpg','uploads/demo/1ba6cc89c53fb45e337127a668af9f5d.jpg','jpg',61982,'application/octet-stream','a:2:{s:5:\"width\";i:536;s:6:\"height\";i:312;}',1,1018278831,UNIX_TIMESTAMP()),(2,'69bea264a2a.jpg','网站宝','./uploads/demo/765eb031e26a8a041ae2290b0a31a928.jpg','uploads/demo/765eb031e26a8a041ae2290b0a31a928.jpg','jpg',63852,'application/octet-stream','a:2:{s:5:\"width\";i:535;s:6:\"height\";i:331;}',1,1018278831,UNIX_TIMESTAMP()),(3,'52adf1d4e71.jpg','爱聚合','./uploads/demo/977f57d7da524156d1f951791cd3892a.jpg','uploads/demo/977f57d7da524156d1f951791cd3892a.jpg','jpg',42435,'application/octet-stream','a:2:{s:5:\"width\";i:471;s:6:\"height\";i:276;}',1,1018278831,UNIX_TIMESTAMP()),(4,'af8670f9020.jpg','爱聚合','./uploads/demo/5fae400ed88f0ec3d1b97642a883cba2.jpg','uploads/demo/5fae400ed88f0ec3d1b97642a883cba2.jpg','jpg',48755,'application/octet-stream','a:2:{s:5:\"width\";i:526;s:6:\"height\";i:299;}',1,1018278831,UNIX_TIMESTAMP()),(5,'f6135c3e3ec.jpg','好业宝','./uploads/demo/22665b22ff0c66a493c1dec3ea7ee7c9.jpg','uploads/demo/22665b22ff0c66a493c1dec3ea7ee7c9.jpg','jpg',26687,'application/octet-stream','a:2:{s:5:\"width\";i:478;s:6:\"height\";i:301;}',1,1018278831,UNIX_TIMESTAMP()),(6,'8edf76e191e.jpg','好业宝','./uploads/demo/4175419d8938c9adbea980358d48ad82.jpg','uploads/demo/4175419d8938c9adbea980358d48ad82.jpg','jpg',39663,'application/octet-stream','a:2:{s:5:\"width\";i:493;s:6:\"height\";i:300;}',1,1018278831,UNIX_TIMESTAMP()),(7,'97ef578542b.jpg','好业宝','./uploads/demo/61baf7aa85d980548def226e3b429e85.jpg','uploads/demo/61baf7aa85d980548def226e3b429e85.jpg','jpg',29816,'application/octet-stream','a:2:{s:5:\"width\";i:497;s:6:\"height\";i:316;}',1,1018278831,UNIX_TIMESTAMP()),(8,'9858554f923.jpg','好业宝','./uploads/demo/499606bbf6a3d27558dbb67ceea407c6.jpg','uploads/demo/499606bbf6a3d27558dbb67ceea407c6.jpg','jpg',27784,'application/octet-stream','a:2:{s:5:\"width\";i:503;s:6:\"height\";i:291;}',1,1018278831,UNIX_TIMESTAMP());

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
