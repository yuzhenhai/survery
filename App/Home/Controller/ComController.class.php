<?php
/**
 *
 * Copyright: Bodasoft
 * Author   ：dowei<douwei@bodait.com>
 * Date     ：2018-1-23
 * Version  ：1.0.0
 * Desc     ：前台公用控制器。
 *
 **/

namespace Home\Controller;

use Common\Controller\BaseController;
use Think\Auth;

class ComController extends BaseController
{
    public $USER;

    public function _initialize()
    {

        C(setting());
        if (!C("COOKIE_SALT")) {
            $this->error('请配置COOKIE_SALT信息');
        }
        /**
         * 不需要登录控制器
         */
        if (in_array(CONTROLLER_NAME, array("Login"))) {
            return true;
        }
        //权限判断
        $flag =  $this->check_login();
        $url = U("login/index");
        if (!$flag) {
            header("Location: {$url}");
            exit(0);
        }
        $m = M();
        $prefix = C('DB_PREFIX');
        $UID = $this->USER['uid'];

      /*  $userinfo = $m->query("SELECT * FROM {$prefix}auth_group g left join {$prefix}auth_group_access a on g.id=a.group_id where a.uid=$UID");
        $Auth = new Auth();
        $allow_controller_name = array('Upload');//放行控制器名称
        $allow_action_name = array();//放行函数名称
        if ($userinfo[0]['group_id'] != 1 && !$Auth->check(CONTROLLER_NAME . '/' . ACTION_NAME,
                $UID) && !in_array(CONTROLLER_NAME, $allow_controller_name) && !in_array(ACTION_NAME,
                $allow_action_name)
        ) {
            $this->error('没有权限访问本页面!');
        }*/

        $user = member(intval($UID));
        $this->assign('user', $user);
        if($user['last_visit']>$user['last_post']){
            $talks = M("talk_topic")->field("id,date")->where("status=1 AND approval=1 AND close=0")->select();

        }else{
            $talks = M("talk_topic")->field("id,date")->where("status=1 AND approval=1 AND close=0")->select();

        }

        $talk_exist = 0;
        foreach ($talks as $key => $talk) {
            
            $date = strtotime($talk['date']);
            if($date>$user['last_visit'] && $date>$user['last_post']){
                $talk_exist = 1;
            }
        }


        $this->assign('talk_exist', $talk_exist);
        $current_action_name = ACTION_NAME == 'edit' ? "index" : ACTION_NAME;
        $current = $m->query("SELECT s.id,s.title,s.name,s.tips,s.pid,p.pid as ppid,p.title as ptitle FROM {$prefix}auth_rule s left join {$prefix}auth_rule p on p.id=s.pid where s.name='" . CONTROLLER_NAME . '/' . $current_action_name . "'");
        $this->assign('current', $current[0]);

    }

    public function check_login(){
        session_start();
        $flag = false;
        $salt = C("COOKIE_SALT");
        $ip = get_client_ip();
        $ua = $_SERVER['HTTP_USER_AGENT'];
        $auth = cookie('auth');
        $uid = session('uid');
        $prefix = C('DB_PREFIX');
        if ($uid) {
            $user = M('member')->field("{$prefix}member.*,{$prefix}auth_group.id as gid,{$prefix}auth_group.title")
            ->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
            ->join("{$prefix}auth_group ON {$prefix}auth_group.id = {$prefix}auth_group_access.group_id")
            ->where("{$prefix}member.uid = ".$uid)
            ->find();

            if ($user) {
                if ($auth ==  password($uid.$user['user'].$ip.$ua.$salt)) {
                    $flag = true;
                    $this->USER = $user;
                }
            }
        }
        return $flag;
    }

    public function hot(){

        $prefix = C('DB_PREFIX');
        $where = array();
        $where[] = "{$prefix}talk_topic.status = '1'";
        $where[] = "{$prefix}talk_topic.approval = '1'";
        $where[] = "{$prefix}talk_topic.ess = '1'";

        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }
        $talks = M('talk_topic');

        $hots = $talks->field("{$prefix}talk_topic.*")
            ->where($wherestring)
            ->limit('0,5')
            ->select();

        return $hots;
    }

    public function user(){

        $prefix = C('DB_PREFIX');
        $where = array();
        $where[] = "{$prefix}member_celebrity.status = '1'";
        $where[] = "date({$prefix}member_celebrity.begin) <=DATE(NOW())";
        $where[] = "date({$prefix}member_celebrity.end) >=DATE(NOW())";

        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }
        $member = M('member_celebrity');

        $list = $member->field("{$prefix}member_celebrity.*,{$prefix}member.realname,{$prefix}member.head,(SELECT count({$prefix}talk_topic.id) FROM {$prefix}talk_topic WHERE {$prefix}talk_topic.uid = {$prefix}member_celebrity.uid) as count")
                ->join("{$prefix}member ON {$prefix}member_celebrity.uid = {$prefix}member.uid")
                ->where($wherestring)
                ->limit('0,5')
                ->select();

        return $list;
    }
}