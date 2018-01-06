<?php
require_once('memobird.php');
$memobird = new memobird();
$memobirdid = '';//设备编码
$str = '';//用户标识
//绑定并获取用户ID
$res = $memobird->getUserId($memobirdid, $str);
$userid = $res['showapi_userid'];
//添加打印文字
$c = $memobird->addTextContent('欢迎使用MEMOBIRD');
//添加图片
$c = $memobird->addImagesContent(file_get_contents('example.bmp'));
//获取单色图片
$res = $memobird->getPic(base64_encode(file_get_contents('example.jpg')));
$c = $memobird->addContent('P:' . $res['result']);
//打印
$res = $memobird->printPaper($memobirdid, $userid);
$url = 'http://memobird.iecheng.cn/tmp/memobird/example.html';
$res = $memobird->printUrl($url, $memobirdid, $userid);
$html = '<html><head></head><style>h2{font-size:2em;}</style><body><h2>欢迎使用</h2><p>Hello,World!</p></body></html>';
$res = $memobird->printHtml($html, $memobirdid, $userid);
