<?php

$appConfig =  array(
	'APP_URI' => 'http://'.$_SERVER['HTTP_HOST'].__ROOT__,
	'IMAGE_URI' => 'http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Public/images/',
	'UPLAOD_URI' => 'http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Public/uploads/',
	
    'UPLOAD'	  =>  'Public/uploads/',
    'UPLOAD_ROOT' =>  WEB_ROOT . 'Public/uploads/',
    // 系统公用配置目录
    'COMMON_CONF_PATH' => WEB_ROOT . 'Common/Conf/',  
    'PAGE_LIST_ROWS' => 5,
    'APIKEY' => 'Illuminera',
    'TOKEY_EXPIRE_DATE'  => 86400
);

$routeConfig =  array(
    'URL_ROUTER_ON'   => true,
    'URL_ROUTE_RULES'=>array(
        array('user/login','user/login','',array('method'=>'post')),
        array('user/register','user/register','',array('method'=>'post')),
        array('user/GetRepList','user/repList','',array('method'=>'get')),
        array('user/:id','user/getUser','',array('method'=>'get')),
        array('user/getTest','test/getTest','',array('method'=>'post')),
        array('user/sendTest','test/sendTest','',array('method'=>'post')),
        array('user/getTestResult','test/getTestResult','',array('method'=>'post')),
    )
);

//第三方接口设置
$tokenConfig = array(

);

$smsConfig =  array(
);

return array_merge($appConfig , $routeConfig, $tokenConfig, $smsConfig);