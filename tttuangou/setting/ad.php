<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name ad.php
 * @date 2015-08-31 16:32:14
 */



$config["ad"] =  array (
  'howdo' => 
  array (
    'mod_id' => 1,
    'name' => '首页单图广告位',
    'enabled' => false,
    'config' => 
    array (
      'list' => 
      array (
        '8bvuxd' => 
        array (
          'image' => 'uploads/images/howdo/h.8bvuxd.gif',
          'text' => '哈哈',
          'link' => 'https://www.baidu.com/',
          'target' => '_blank',
          'order' => '1',
          'city' => '0',
        ),
        'ygv1h5' => 
        array (
          'image' => 'uploads/images/howdo/h.ygv1h5.gif',
          'text' => '',
          'link' => '',
          'target' => '_self',
          'order' => '',
          'city' => '2',
        ),
      ),
    ),
  ),
  'howdot' => 
  array (
    'mod_id' => 2,
    'name' => '首页两图广告位',
    'enabled' => false,
    'config' => 
    array (
      'list' => 
      array (
      ),
    ),
  ),
  'howdof' => 
  array (
    'mod_id' => 3,
    'name' => '首页四图广告位',
    'enabled' => false,
    'config' => 
    array (
      'list' => 
      array (
      ),
    ),
  ),
  'howd0api' => 
  array (
    'mod_id' => 8,
    'name' => '客户端首页多图广告位',
    'enabled' => false,
    'config' => 
    array (
      'list' => 
      array (
      ),
      'dsp' => 
      array (
        'api' => 1,
        'time' => 3,
      ),
    ),
  ),
  'howdnew' => 
  array (
    'mod_id' => 9,
    'name' => '新模板首页广告位',
    'enabled' => true,
    'config' => 
    array (
      'dsp' => 
      array (
        'time' => '5',
      ),
      'list' => 
      array (
        'prf369' => 
        array (
          'image' => 'uploads/images/howdnew/hn.prf369.gif',
          'link' => '11',
          'color' => '#FCEDF0',
          'type' => '1',
          'image2' => 'uploads/images/howdnew/hn2.prf369.gif',
          'link2' => '22',
          'image_rup' => '',
          'linkrup' => 'http://www.aaa.com',
          'image_rdown' => '',
          'linkrdown' => 'http://www.aaa.com',
          'city' => '2',
        ),
        '1do2h4' => 
        array (
          'image' => 'uploads/images/howdnew/hn.1do2h4.gif',
          'link' => 'http://www.baidu.com',
          'color' => '#deeee4',
          'type' => '1',
          'image2' => 'uploads/images/howdnew/hn2.1do2h4.gif',
          'link2' => 'http://www.sina.com',
          'image_rup' => '',
          'linkrup' => '',
          'image_rdown' => '',
          'linkrdown' => '',
          'city' => '2',
        ),
        'lkc8b1' => 
        array (
          'image' => 'uploads/images/howdnew/hn.lkc8b1.gif',
          'link' => 'http://www.aaa.com',
          'color' => '#deeee4',
          'type' => '2',
          'image2' => 'uploads/images/howdnew/hn2.lkc8b1.gif',
          'link2' => '',
          'image_rup' => 'uploads/images/howdnew/hn3.lkc8b1.gif',
          'linkrup' => 'http://www.bbb.com',
          'image_rdown' => 'uploads/images/howdnew/hn4.lkc8b1.gif',
          'linkrdown' => 'http://www.bbb.com',
          'city' => '1',
        ),
      ),
    ),
  ),
  'howdom' => 
  array (
    'mod_id' => 5,
    'name' => '首页多图轮换广告位',
    'enabled' => false,
    'config' => 
    array (
      'list' => 
      array (
      ),
      'dsp' => 
      array (
        'time' => '3',
        'switchPath' => 'left',
        'showText' => 'false',
        'showButtons' => 'true',
      ),
    ),
  ),
);
?>