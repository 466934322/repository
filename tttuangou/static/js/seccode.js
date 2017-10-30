/** * @copyright (C)2014 Cenwor Inc. * @author Cenwor <www.cenwor.com> * @package js * @name seccode.js * @date 2015-08-31 16:32:14 */ 
var __Global_Seccode_Val = '';

/**
 * 显示验证码
 */
function seccode(options) {
    if (typeof options == 'undefined' || options == null || options == '') {
        options = {};
    }
    var id = 'seccode';
    if (options.id) {
        id = options.id;
    }
    var updateFunc = "updateSeccode";
    if (options.updateFunc) {
        updateFunc = options.updateFunc;
    }
    var img_id = id + '_img';
    if (options.img_id) {
        img_id = options.img_id;
    }
    var img = 'index.php?mod=seccode&'+$.rnd.stamp();
    var html = '<img id="'+img_id+'" src="'+img+'" align="absmiddle" onclick="'+updateFunc+'(\''+id+'\');">';
    if (options.wp) {
        $('#'+options.wp).html(html);
    } else {
        document.writeln(html);
    }
}

/**
 * 更新验证码
 */
function updateSeccode(id) {
    if (typeof id != 'undefined' && id != null && id != '') {
        id = 'seccode';
    }
    var img = 'index.php?mod=seccode&'+$.rnd.stamp();
    $('#' + id + '_img').attr("src", img);
    $('#' + id + '_input').val('');
    $("#" + id + '_check_result').html('');
    __Global_Seccode_Val = '';
}

/**
 * 校验验证码
 */
function checkSeccode(id, options) {
    if (typeof options == 'undefined' || options == null || options == '') {
        options = {};
    }

    if (typeof id != 'undefined' && id != null && id != '') {
        id = 'seccode';
    }
    
    var tips_id = id + "_check_result";
    if (options.tips_id) {
        tips_id = options.tips_id;
    }
    
    __Global_Seccode_Val = $('#' + id + '_input').val();

    if(4 != __Global_Seccode_Val.length) {
        __Global_Seccode_Val = '';
        $("#"+tips_id).html('<img src="static/images/wrong.png" />').show();
        return false;
    }

    $.post(
        'index.php?mod=seccode&code=check&'+$.rnd.stamp(),
        {"seccode": __Global_Seccode_Val},
        function(r) {
            if ('true' == r) {
                $("#"+tips_id).html('<img src="static/images/accept.png" />');
                $("#"+tips_id).show();
                if (options.success) {
                    options.success.call();
                }
            } else {
                __Global_Seccode_Val = '';
                $("#"+tips_id).html('<img src="static/images/wrong.png" />');
                $("#"+tips_id).show();
                if (options.failed) {
                    options.failed.call();
                }
            }
        }
    );
}