<?php
/**
 *
 * Copyright: Bodasoft
 * Author   ：dowei<douwei@bodait.com>
 * Date     ：2018-1-23
 * Version  ：1.0.0
 * Desc     ：后台退出控制器。
 *
 **/

namespace Home\Controller;

class LogoutController extends ComController
{
    public function index()
    {
    	$user = $this->user;
	    $last_visit = time();
        M('Member')->data(array('last_visit' => $last_visit))->where("uid=$user[uid]")->save();

        cookie('auth', null);
        session('uid',null);
        $url = U("login/index");
        header("Location: {$url}");
        exit(0);
    }
}