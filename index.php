<?php
define(ACCESSKEY, '');  //你的accessKey
$memobirdID = '';       //设备编码
$str = 'default';       //用户标识
require_once('memobird.php');
date_default_timezone_set('Etc/GMT-8');
$memobird = new memobird();
//绑定并获取用户ID
$res = $memobird->getUserId($memobirdID, $str);
$userid = $res['showapi_userid'];
var_dump($res);
//添加打印文字
$c = $memobird->addTextContent('欢迎使用MEMOBIRD');
//添加图片
$c = $memobird->addImagesContent(file_get_contents('example.bmp'));
//获取单色图片
$res = $memobird->getPic(base64_encode(file_get_contents('example.jpg')));
var_dump($res);
$c = $memobird->addContent('P:' . $res['result']);
//打印
$res = $memobird->printPaper($memobirdID, $userid);
var_dump($res);
$url = 'http://memobird.iecheng.cn/tmp/memobird/example.html';
$res = $memobird->printUrl($url, $memobirdID, $userid);
var_dump($res);
$html = '<html><head></head><style>h2{font-size:2em;}</style><body><h2>欢迎使用</h2><p>Hello,MEMOBIRD!</p></body></html>';
$res = $memobird->printHtml($html, $memobirdID, $userid);
var_dump($res);
