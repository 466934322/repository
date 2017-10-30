/**
 ************
 * 用于快速注册和登录的类
 *
 * 使用方法：
 * 在产品详情页面（有购买链接）顶部粘贴以下代码：
    <script>
	MEMBER_ID = '{MEMBER_ID}';
	PRODUCT_ID = '{$product["id"]}';
	</script>
 * 然后在该页面的底部粘贴以下代码
   {~ui('loader')->js('@quickSignIn')}
 *
 ************
 */

// 开启倒计时 （类）
var CountDownForDynamicCode =  function() {

	var countDownTime = 0;
	var countDownObj = null;

}
// 执行倒计时工作
CountDownForDynamicCode.startTime = function() {

	if (this.countDownObj == null) {
		// 新一轮倒计时
		this.countDownTime = 60;
		this.countDownObj = setTimeout('CountDownForDynamicCode.startTime()', 1000);
		$('#getDynamicCode_button').attr('data-status', 'disable').attr('disabled', true);		// 申请验证码按钮暂时不可用
	}
	else {
		// 倒计时处理并检测倒计时是否结束
		this.countDownTime--;

		$('#getDynamicCode_button').val('重新发送（' + this.countDownTime + '）');

		if (this.countDownTime <= 0) {
			// 倒计时结束
			this.countDownTime = 0;
			clearTimeout(this.countDownObj);
			this.countDownObj = null;
			$('#getDynamicCode_button').attr('data-status', 'active').val('重新获取验证码').removeAttr('disabled');	// 恢复按钮为可用状态
		}
		else {

			this.countDownObj = setTimeout('CountDownForDynamicCode.startTime()', 1000);

		}
	}

}
// 执行初始化工作
CountDownForDynamicCode.init = function() {

	this.countDownTime = 0;

	if (this.countDownObj != null) {

		clearTimeout(this.countDownObj);

		this.countDownObj = null;

	}

	$('#getDynamicCode_button').attr('data-status', 'active').val('免费获取手机动态码').removeAttr('disabled');
}

// 记录错误的代码及字符串
var quickSignInStringMap = function() {};
quickSignInStringMap.FORMAT_TURE = '格式正确';

quickSignInStringMap.FORMAT_ERROR_PHONE = '请输入正确的手机号码';
quickSignInStringMap.FORMAT_ERROR_DYNAMIC_CODE = '动态码错误（短信中的6位数字）';
quickSignInStringMap.FORMAT_ERROR_PASSWORD = '密码错误（不小于4位的字符）';

quickSignInStringMap.APPLY_DYNAMIC_CODE = '点击获取手机动态码';
quickSignInStringMap.APPLYING_DYNAMIC_CODE = '动态码获取中';
quickSignInStringMap.REAPPLY_DYNAMIC_CODE = '重新获取动态码';

quickSignInStringMap.SEND_SUCCESSED_DYNAMIC_CODE = '动态码发送成功';
quickSignInStringMap.SEND_FAILED_DYNAMIC_CODE = '动态码发送失败'

quickSignInStringMap.POST_ERROR = '网络错误，请求失败';


// 验证手机号码是否符合规则
function checkMobile(str) {

	var re = /^1\d{10}$/;

	if (re.test(str)) {
		return true;
	}

	return false;
}

// 验证动态码是否符合规则
function checkDynamicCode(str) {

	var re = /^\d{6}$/;		// 6位数字

	if (re.test(str)) {
		return true;
	}

	return false;
}

// 检测密码是否符合规则
function checkPassword(str) {

	if (str && str.length >= 4) {
		return true;
	}

	return false;

}

// 设置错误提示信息
function showErrorInfo(errStr) {

	$('#quickSignIn_comment').text(errStr);

}
// 清除错误提示信息
function cleanErrorInfo() {

	$('#quickSignIn_comment').text(' ');

}

/**
 * 验证表单数据是否符合要求
 *
 * @return 成功返回字符串'符合要求', 失败返回失败原因字符串
 */
function checkFormInfo() {


	var signInType = $('#signInType').val();
	if (signInType == 'dynamicCode') {					// 检测动态码格式

		if (!checkMobile($('#signInPhone').val())) {		// 检测手机号
			return quickSignInStringMap.FORMAT_ERROR_PHONE;
		}

		if (!checkDynamicCode($('#signInDynamicCode').val())) {
			return quickSignInStringMap.FORMAT_ERROR_DYNAMIC_CODE;
		}

	}
	else if (signInType == 'password') {				// 检测密码格式

		if (!checkPassword($('#signInPassword').val())) {

			return quickSignInStringMap.FORMAT_ERROR_PASSWORD;

		}

	}

	return quickSignInStringMap.FORMAT_TRUE;

}

/**
 * 设置获验证码按钮的状态
 *
 * @param string status 值为 active 和 disabled 二选一，表示按钮的状态
 * @param string value 按钮的 value 值
 *
 * @return 设置成功返回 true，否则返回 false
 */
function setDynamicCode_button(status, value) {

	if (status == 'active') {

		$('#getDynamicCode_button').attr('data-status', 'active').removeAttr('disabled').val(value);

	}
	else if (status == 'disabled') {

		$('#getDynamicCode_button').attr('data-status', 'disable').attr('disabled', true).val(value);

	}
	else {
		return false;
	}

	return true;

}

/**
 * AJAX 申请发送手机动态码
 *
 */
function applyForDynamicCode_AJAX() {

	// 申请发送验证码
	$.ajax({
		url			: '?mod=phone&code=vfsend',
		data		: {
						'phone' : $('#signInPhone').val(),
						'seccode' : __Global_Seccode_Val,
						'check_exists' : 'no'							// 增加的参数，表示不检测是否已绑定
					  },
		type		: 'post',
		success		: function (data, textStatus) {
							/**
							 * data 格式说明：
							 * 发送失败返回失败说明，发送成功无返回值
							 */
							if(data == '') {

								setDynamicCode_button('disabled', quickSignInStringMap.SEND_SUCCESSED_DYNAMIC_CODE);	// 动态码发送成功

								CountDownForDynamicCode.startTime();		// 开启倒计时

							}
							else {

								showErrorInfo(data);	// 错误提示，动态码获取失败

								setDynamicCode_button('active', quickSignInStringMap.REAPPLY_DYNAMIC_CODE); // 恢复按钮的功能 重新获取动态码

								return false;

							}

					  },
		error		: function (XMLHttpRequest, textStatus, errorThrown) {

						showErrorInfo(quickSignInStringMap.POST_ERROR);

						// 恢复按钮为可用状态
						setDynamicCode_button('active', quickSignInStringMap.REAPPLY_DYNAMIC_CODE); // 申请手机动态码
					  }
	});

}

/**
 * AJAX 请求快速注册、登录
 *
 */
function quickSignIn_AJAX() {

	$.ajax({
			url 		: '?mod=account&code=quickSignIn', 										// 目标 URL
			data		: { 																	// 参数列表
							'signInType'  : '' + $('#signInType').val(),
							'home_id'  : '' + $('#homeId').val(),
							'signInPhone' :	'' + $('#signInPhone').val(),
							'signInDynamicCode' : '' + $('#signInDynamicCode').val(),
							'signInPassword'	: '' + $('#signInPassword').val()
						  },
			type		: 'post',																// 请求方式

			success		: function (data, textStatus) {											// 请求成功后的回调函数
							/**
							 * data 格式说明：
							 * 发送失败返回失败说明，发送成功返回 '登录成功'
							 */
							 if (data == '登录成功') {
								 // 注册登录成功，继续购买流程
								 window.location.href = '?mod=buy&code=checkout&id=' + PRODUCT_ID;
							 }
							 else {
								 // 注册或登录出错，显示错误信息
								 showErrorInfo(data);
							 }


						 },
			error		: function (XMLHttpRequest, textStatus, errorThrown) {					// 请求失败后的回调函数
								showErrorInfo(textStatus);		// 网络错误，请求失败
						 }
	});

}


// 保存快速登录对话框
var quickSignInObj = null;

// 生成对话框并注册基本事件
// 当点击了购买按钮后，用这个方法判断后续处理
function quickSignInDialog() {

	<!-- 快速注册/登录 对话框的内容 -->
	var quickSignInContent = '\
	<div id="quickSignIn_wrap">\
		<p id="quickSignIn_comment"></p>\
		<form id="quickSignIn_form">\
			<input type="hidden" name="targetUrl" value="?mod=buy&code=checkout&id=' + PRODUCT_ID + '">\
			<input type="hidden" id="homeId" name="home_id" value="' + HOME_ID + '">\
			<input id="signInType" type="hidden" name="signInType" value="dynamicCode">	<!-- 用户选择的标签是验证码还是密码登录 -->\
			<ul id="signInType_tab">\
				<li data-signInType="signInType_dynamicCode_wrap" class="selected">手机动态码登录</li>\
				<li data-signInType="signInType_password_wrap" >密码登录</li>\
			</ul>\
			<div class="signInType_wrap">\
			<input id="signInPhone" type="text" name="signInPhone" placeholder="请输入手机号码" value="">\
			<div id="seccode_block">\
			<input id="seccode_input" type="text" name="seccode" placeholder="请输入下图中的字符" onblur="checkSeccode(\'seccode\')" style="width:120px" />\
			<a href="javascript:updateSeccode(\'seccode\');">换一个</a>\
			<span id="seccode_check_result" style="display:none"></span>\
			<span id="seccode_display"><img id="seccode_img" style="height:35px" src="?mod=seccode'+$.rnd.stamp()+'" onclick="updateSeccode(\'seccode\')" /></span>\
			</div>\
			<div id="signInType_wrap">\
				<div id="signInType_dynamicCode_wrap">\
					<input id="getDynamicCode_button" data-status="active" type="button" value="免费获取手机动态码">\
					<input id="signInDynamicCode" type="text" name="signInDynamicCode" placeholder="请输入动态码" value="">\
				</div>\
				<div id="signInType_password_wrap">\
					<input id="signInPassword" type="password" name="signInPassword" value="" placeholder="请输入密码">\
				</div>\
			</div>\
			</div>\
		</form>\
	</div>\
	';

	// 生成对话框
	quickSignInObj = art.dialog(
		{
			id:'quickSignDialog1',
			title:'登录',
			content: quickSignInContent,
			lock: true,

			initialize: function () {
		        seccode({"id":"seccode", "wp":"seccode_display"});
		    },

			ok:function() {

				cleanErrorInfo();

				var result = checkFormInfo();		// 检测数据填写是否符合要求
				if (result != quickSignInStringMap.FORMAT_TRUE) {

					showErrorInfo(result);

					return false;

				}

				quickSignIn_AJAX();			// 无刷新登录

				return false;

			},
			cancel:function() {

				cleanErrorInfo();

				CountDownForDynamicCode.init();		// 关闭对话框时要初始化倒计时类的数据

				quickSignInObj = null;				// 全局变量赋值

			},
		}
	);

	// 注册事件
	// 点击了标签
	$('#signInType_tab li').click(function(e) {

		$('#signInType_tab li').removeClass('selected');	// 设置当前选中的标签

		$(e.target).addClass('selected');

		$('#signInType_wrap').children('div').hide();	// 设置当前选中标签对应的 DIV

		var signInType = $(e.target).attr('data-signInType');

		$('#' + signInType).show();

		$('#signInType').val(signInType.split('_')[1]);		// 修改当前行为（隐藏表单项的值）

	//	Jiliang Qiu added the case for Task #2068 on 2015/06/05;
		if ($('#signInType').val() == 'password') {
			$('#seccode_block').hide();
			$('#signInPhone').attr('placeholder', '输入用户名、邮箱或手机号码');
		}
		else {
			$('#seccode_block').show();
			$('#signInPhone').attr('placeholder', '请输入手机号码');
		}
	//	End Task #2068's addition;

		cleanErrorInfo(); 										// 隐藏错误提示

	});

	// 点击了“免费获取手机动态码”按钮
	$('#getDynamicCode_button').click(function(e) {

		// 判断按钮的状态，如果是活动，则可以提交 AJAX 申请,否则是在倒计时中，不重复提交
		var buttonObj = e.target;
		if ($(buttonObj).attr('data-status') != 'active') {
			return false;
		}

		// 验证是手机号码是否符合规则
		var signInPhone = $('#signInPhone').val();
		if (!checkMobile(signInPhone)) {

			showErrorInfo(quickSignInStringMap.FORMAT_ERROR_PHONE);

			return false;

		}

		if('' == __Global_Seccode_Val) {

			showErrorInfo('请输入正确的图片验证码！');

			return false;
		}

		// 立即设置按钮不可再次点击
		setDynamicCode_button('disabled', quickSignInStringMap.APPLYING_DYNAMIC_CODE);	// 动态码申请中

		// 清除错误提示
		cleanErrorInfo();

		// 申请发送验证码
		applyForDynamicCode_AJAX();

	});

	// 输入框失去焦点后检测自己数据的正确性
//	Jiliang Qiu deactivated the Format Validator for Task #2068 on 2015/06/05;
/*	$('#signInPhone').blur(function () {

		if (!checkMobile($('#signInPhone').val())) {		// 检测手机号
			showErrorInfo(quickSignInStringMap.FORMAT_ERROR_PHONE);
		}
		else {
			if ($('#quickSignIn_comment').text() == quickSignInStringMap.FORMAT_ERROR_PHONE) {	// 去掉失败的提示
				cleanErrorInfo();
			}
		}

	});	*/
	$('#signInDynamicCode').blur(function() {

		if (!checkDynamicCode($('#signInDynamicCode').val())) {		// 检测动态码
			showErrorInfo(quickSignInStringMap.FORMAT_ERROR_DYNAMIC_CODE);
		}
		else {
			if ($('#quickSignIn_comment').text() == quickSignInStringMap.FORMAT_ERROR_DYNAMIC_CODE) {
				cleanErrorInfo();
			}
		}

	});
	$('#signInPassword').blur(function() {

		if (!checkPassword($('#signInPassword').val())) {		// 检测密码
			showErrorInfo(quickSignInStringMap.FORMAT_ERROR_PASSWORD);
		}
		else {
			if ($('#quickSignIn_comment').text() == quickSignInStringMap.FORMAT_ERROR_PASSWORD) {
				cleanErrorInfo();
			}
		}

	});
}


function quickSignIn() {

	// 用户未登录时添加快速登录框
	if (MEMBER_ID <= 0) {

		$('a[href*="?mod=buy&code=checkout&id="]').attr('href', 'javascript:void(0);').click(quickSignInDialog);
		$('a[href*="/index.php/buy/checkout/id-"]').attr('href', 'javascript:void(0);').click(quickSignInDialog);
		$('a[href*="/buy/checkout/id-"]').attr('href', 'javascript:void(0);').click(quickSignInDialog);

	}

}

$.getScript('static/js/seccode.js');

$().ready(quickSignIn());