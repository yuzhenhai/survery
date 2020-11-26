<?php

namespace Api\Model;
use Think\Model;

/**
 * User
 * 用户模型
 */
class UserModel extends Model {

     protected $tableName = 'member'; 

    protected $_auto = array(
        // 用户名，密码
        array('t', 'datetime', 1, 'callback'),
        array('password', 'encrypt', 3, 'callback'),
        // 最后登录时间
        array('last_login', 'datetime', 3, 'callback'),
        // 默认注册后激活
        array('status','1')
    );

    protected $_validate = array(
        // 用户名
        array('user', '', '用户名已经存在，请更换一个！', 1, 'unique', 3),
        // password
        array('password', 'require', '密码不能为空！', 1, 'regex', 3)
    );


	/**
     * 是否存在用户
     * @param  string     $username 用户名
     * @return boolean
     */
    public function existUser($username) {
        if ($this->where("username='{$username}'")->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * 账户是否启用
     * @param  string  $username username
     * @return boolean
     */
    public function isActive($username) {
        $where = array(
            'username' => $username,
            'status' => 1
        );

        if ($this->where($where)->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * 修改密码
     * @param  int $id 用户ID
     * @param  string $password 新密码
     * @return boolean
     */
    public function updatePassword($uid,$password) {
    	$passwordHash = password($password);

    	$this->where("uid={$uid}")
              ->save(array('password' => $passwordHash));
        return true;
    }

    /**
     * 更新最后登录时间
     * @param  int $id 用户ID
     * @return boolean
     */
    public function updateLogin($uid) {
    	$this->where("uid={$uid}")
              ->save(array('last_login' => $this->datetime()));
        return true;
    }


    /**
     * 加密密码
     * @param  string $data 需要加密的数据
     * @return string
     */
    public function encrypt($data) {
    	$hash = md5($data);
    	$salt = substr($hash, 2, 8);    	
        return md5($salt . $hash);
    }  

    protected function datetime(){
        return date('Y-m-d H:i:s',time());
    }  
}