<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>分类列表</title>
    <meta content="telephone=no" name="format-detection"/>
    <meta name="viewport" content="width=320, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="http://127.0.0.1/baijiacmsV3/themes/default/__RESOURCE__/css/bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" href="http://127.0.0.1/baijiacmsV3/themes/default/__RESOURCE__/css/list.css" type="text/css"/>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="WX_search1" id="mallHead">

            <form class="WX_search_frm1" action="mobile.php" id="searchForm" name="searchForm">
            	
        	<input type="hidden" name="beid" value="<?php  echo $_CMS['beid'];?>"/>
                <input type="hidden" name="mod" value="mobile"/>
                <input type="hidden" name="do" value="goodlist"/>
                <input type="hidden" name="name" value="shopwap"/>
                <input name="keyword" id="search_word" class="WX_search_txt hd_search_txt_null"
                       placeholder="请输入商品名进行搜索！" type="search" AUTOCOMPLETE="off"/>

                <div class="WX_me">
                    <a href="javascript:;" id="submit" class="WX_search_btn_blue">搜索</a>
                </div>
            </form>
        </div>
        <div class="category">
            <ul>
                <?php  if(is_array($category)) { foreach($category as $item) { ?>
                <li class="clearfix">
                    <div class="info">
                        <p class="name"><a
                                href="<?php  echo mobile_url('goodlist', array('pcate' => $item['id']))?>">
                            <?php  echo $item['name'];?></a></p>

                        <div class="data">
                            <?php  if(is_array($children[$item['id']])) { foreach($children[$item['id']] as $child) { ?>
                            <a href="<?php  echo mobile_url('goodlist', array('ccate' => $child['id']))?>">
                                <?php  echo $child['name'];?></a>
                            <?php  } } ?>
                        </div>
                    </div>
                </li>
                <?php  } } ?>
            </ul>
        </div>

    </div>
</div>
<script src="http://127.0.0.1/baijiacmsV3/themes/default/__RESOURCE__/js/jquery-1.11.3.min.js" type="text/javascript"></script>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $("#submit").click(function () {
            if ($("#search_word").val()) {
                $("#searchForm").submit();
            } else {
                return false;
            }
        });
    });
</script>

<?php include themePage('footer');?>
<?php include themePage('weixinshare');?>
<?php  include page('footer');?>	