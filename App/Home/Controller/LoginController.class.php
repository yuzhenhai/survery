<?php
/**
 *
 * Copyright: Bodasoft
 * Author   ：dowei<douwei@bodait.com>
 * Date     ：2018-1-23
 * Version  ：1.0.0
 * Desc     ：后台登录控制器。
 *
 **/

namespace Home\Controller;

use Home\Controller\ComController;
use Think\datx\City;
use Think\datx\BaseStation;
use Think\datx\District;
use Think\IP;

class LoginController extends ComController
{
    public function index()
    {
        $flag = $this->check_login();
        if ($flag) {
            $this->error('您已经登录,正在跳转到主页', U("index/index"));
        }

        $this->display();
    }



     public function register()
    {
        $flag = $this->check_login();
        if ($flag) {
            $this->error('您已经登录,正在跳转到主页', U("index/index"));
        }

        $this->display();
    }


    public function login()
    {
        $verify = isset($_POST['verify']) ? trim($_POST['verify']) : '';
        if (!$this->check_verify($verify, 'login')) {
            $this->error('验证码错误！', U("login/index"));
        }

        $username = isset($_POST['user']) ? trim($_POST['user']) : '';
        $password = isset($_POST['password']) ? password(trim($_POST['password'])) : '';
        $remember = isset($_POST['remember']) ? $_POST['remember'] : 0;
        if ($username == '') {
            $this->error('用户名不能为空！', U("login/index"));
        } elseif ($password == '') {
            $this->error('密码必须！', U("login/index"));
        }

        $model = M("Member");
        $user = $model->field('uid,user,status,approval,close')->where(array('user' => $username, 'password' => $password))->find();

        if($user['status'] == '0' || $user['approval'] == '0'){
            $this->error('登录失败，您的状态还未审核,请联系管理员！', U("login/index"));
        }
        if($user['close'] == '1'){
            $this->error('登录失败，账户已被删除！', U("login/index"));
        }


        if ($user) {
            $salt = C("COOKIE_SALT");
            $ip = get_client_ip();
            $ua = $_SERVER['HTTP_USER_AGENT'];
            session_start();
            session('uid',$user['uid']);
            //加密cookie信息
            $auth = password($user['uid'].$user['user'].$ip.$ua.$salt);
            if ($remember) {
                cookie('auth', $auth, 3600 * 24 * 365);//记住我
            } else {
                cookie('auth', $auth);
            }

            $IPS = new Ip();
            // $ips = $IPS->find("223.220.233.0");
            
            if($ip != '0.0.0.0' && $ip != '127.0.0.1'){
                $ips = $IPS->find($ip);
                if(is_array($ips)){
                    M('Member')->data(array('last_city' => $ips[1]))->where("uid=$user[uid]")->save();
                }
            }
            $last_login = time();
            M('Member')->data(array('last_login' => $last_login))->where("uid=$user[uid]")->save();



            addlog('登录成功。',false,1);
            $url = U('index/index');
            header("Location: $url");
            exit(0);
        } else {
            addlog('登录失败。', $username);
            $this->error('登录失败，请重试！', U("login/index"));
        }
    }

    function check_verify($code, $id = '')
    {
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }

    public function verify()
    {
        $config = array(
            'fontSize' => 14, // 验证码字体大小
            'length' => 4, // 验证码位数
            'useNoise' => false, // 关闭验证码杂点
            'imageW' => 100,
            'imageH' => 30,
        );
        $verify = new \Think\Verify($config);
        $verify->entry('login');
    }

     public function reg()
    {
   
        $prefix = C('DB_PREFIX');

        $register = M('member_register')->field("{$prefix}member_register.*")
                    ->find();

        $this->assign('register', $register);

        $this->display('reg');
    }


     public function forget()
    {
   
        $prefix = C('DB_PREFIX');


        $this->display('forget');
    }

    public function forgetupdate(){
        $prefix = C('DB_PREFIX');
        $realname = isset($_POST['realname'])?trim($_POST['realname']):false;
        $phone = isset($_POST['phone'])?trim($_POST['phone']):false;

        $data['realname'] = isset($_POST['realname'])?trim($_POST['realname']):false;
        $data['phone'] = isset($_POST['phone'])?trim($_POST['phone']):false;

        if(!$realname){
            $this->error("用户名不能为空！");
        }
        if(!$phone){
            $this->error("电话号码不能为空！");
        }

        $model = M("Member");
        $real = $model->field('uid,user')->where(array('realname' => $realname, 'phone' => $phone))->find();
        $user = $model->field('uid,user')->where(array('user' => $realname, 'phone' => $phone))->find();
        // $user = M('Member')->field('*')->where($data)->find();

        if($user || $real){
            $password = isset($_POST['password'])?trim($_POST['password']):false;
            $password1 = isset($_POST['password1'])?trim($_POST['password1']):false;
            if($password != $password1){
                $this->error("确认密码与新密码不相同！");
            }
            $password = password($password);
            if($user){
                $rel = $model->data(array('password' => $password))->where("uid=$user[uid]")->save();
            }
            if($real){
                $rel = $model->data(array('password' => $password))->where("uid=$real[uid]")->save();
            }
            if($rel){
                $this->success("成功修改密码!",U('index'));
            } else {
                $this->error("修改失败！");
            }
            
        } else {
            $this->error("不存在用户名或电话不正确！");
        }

        // $data['phone'] = isset($_POST['phone'])?$_POST['phone']:false;
    }

}
