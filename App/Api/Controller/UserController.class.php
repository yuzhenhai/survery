<?php
namespace Api\Controller;
use Think\Exception;
class UserController extends ComController
{
    // 登录
    public function login()
    {
        try {
            $cellphone = I('post.cellphone', '', 'string');
            $password = I('post.password', '', 'string');
            if (empty($cellphone)) {   
                return $this->sendError(2002, '手机号码不正确');
            }
            
            if (empty($password)) {
                $result = array(
                    'result' => '0',
                    'message' => '请输入密码'
                );
                return $this->sendError(2003, '密码不正确');
            }
            $User = D('User');
            $account = $User->where("phone ='" . $cellphone . "'")
                ->find();
            // 判断用户是否存在
            if (empty($account)) {
                return $this->sendError(2002, '手机号码不正确');
            }
            // 密码验证
            
            if ($account['password'] != password($password)) {
                $result = array(
                    'result' => '0',
                    'message' => '密码不正确'
                );
                return $this->sendError(2003, '密码不正确');
            }
            // 是否启用
            
            if (! $User->isActive($username)) {
                return $this->sendError(2004, '账户已被禁用');
            }
            
            // 更新最后登录时间
            $User->updateLogin($account['uid']);
            $Token = D('Token');
            $accessToken = $Token->setUserToken($account['uid']);

            $data = array(
                'accessToken' => $accessToken,
                'uid' => $account['uid'],
                'username' => $account['user'],
                'realname' => $account['realname'],
                'cellphone' => $account['phone'],
                'admin' => $account['admin']
            );
            return $this->sendSuccess($data,'登陆成功');
        } catch (Exception $e) {
            $result1 = array(
                'result' => '0',
                'message' => '失败'
            );
            return $this->sendError(2001, '登陆失败');
        }
    }
    

    public function logout()
    {
        $this->filterLogin();
        $Token = D('Token');
        if($Token->deleteUserToken($this->userId)){
            return $this->sendSuccess(null,'退出成功');
        }else{            
            return $this->sendError(2011, '退出失败');
        }
    }

    
    // 修改密码
    public function changepwd()
    {
        $this->filterLogin();
        if (empty($_POST['password'])) {
            return $this->sendError(2101, '请输入新密码');
        }
        
        $User = D('User');
        $account = $User->getById($this->userId);
        $password = I('post.password');
        // 判断用户是否存在
        if (empty($account) ) {
            return $this->sendError(2001, '用户错误');
        }
        if ($User->updatePassword($account['id'], $password)) {
            return $this->sendSuccess(null, '密码修改成功');
        } else {
            return $this->sendError(2002, '密码修改失败');
        }
    }

    public function update()
    {
        $this->filterLogin();
        try{
            $data = array();
            
            $fields = array(
                'sex',
                'age',
                'birthday',
                'qq',
                'email',
                'address'
            );
            
            $User = M('Member');
            
            foreach ($_POST as $key => $val) {
                if (in_array($key, $fields)) {
                    if ($key == 'birthday') {
                        $data[$key] = date('Y-m-d H:i:s', $val);
                    } else {
                        $data[$key] = $val;
                    }
                }
            }
            
            $data['uid'] = $this->userId;
            
            if (false === $User->create($data)) {
                return $this->sendError(2500, '修改失败');
            }
            $User->save();
            
            $result = array(
                'result' => '1',
                'message' => '成功'
            );
            return $this->sendSuccess(null, '修改成功');
        }
        catch (Exception $e){
            $result = array(
                'result' => '0',
                'message' => $e->getMessage()
            );
            return $this->sendError(2500,  $e->getMessage());
        }
     
    }

    /**
     * 设置头像
     *
     * @param file $avatar
     *            头像文件
     * @return [type] [description]
     */
    public function setAvatar()
    {
        try {
        // todo 确认如何上传
        // $this->filterLogin();
        // todo 从post中取文件并上传，参数名称avatar，然后
        \Think\Log::record('图片接收，时间：'.date("h:i:sa"), 'WARN');
        $file = $_FILES['file']; // 得到传输的数据
                                 // 得到文件名称
        $name = $file['name'];
        $type = strtolower(substr($name, strrpos($name, '.') + 1)); // 得到文件类型，并且都转化成小写
        $allow_type = array(
            'jpg',
            'jpeg',
            'gif',
            'png'
        ); // 定义允许上传的类型
           // 判断文件类型是否被允许上传
        if (! in_array($type, $allow_type)) {
            // 如果不被允许，则直接停止程序运行
            $result = array(
                'result' => '0',
                'message' => '失败'
            );
            return $this->response($result, 'json');
        }
        // 判断是否是通过HTTP POST上传的
        if (! is_uploaded_file($file['tmp_name'])) {
            // 如果不是通过HTTP POST上传的
            $result = array(
                'result' => '0',
                'message' => '不是POST'
            );
            return $this->response($result, 'json');
        }
        $upload_path = dirname(dirname(dirname(dirname(__FILE__)))) . '/Public/uploads/avatars/';
        // 开始移动文件到相应的文件夹
        if (move_uploaded_file($file['tmp_name'], $upload_path . $file['name'])) {
                 $result = array(
                'result' => '1',
                'message' => '成功'
            );
            return $this->response($result, 'json');
        } else {
                $result = array(
                'result' => '0',
                'message' => '失败'
            );
            return $this->response($result, 'json');
        }
       } catch (Exception $e) {
            $result = array(
                'result' => '0',
                'message' => '失败'
            );
            return $this->response($result, 'json');
        }
    }

    /*
     * Name： 发送短信验证码，
     * Params：用户名，用户手机号码
     */
    public function sendsms()
    {
        $cellphone = I('post.cellphone',NULL);
        if (empty($cellphone) || !preg_match("/^((13[0-9])|(15[^4,\\D])|(18[0,0-9]))\\d{8}$/", $cellphone)) {
            return $this->sendError(2500, '手机号码不正确');
        }
        
        $SmsApi = D('Sms', 'Api');

        $smsCode = rand(1000, 9999);
        
        $result = $SmsApi->sendTemplateSMS($cellphone, array(
            $smsCode,
            C('SMS_EXPIRE_DATE')
        ), C('SMS_TEMPLATE'));
        if ($result->statusCode == '000000') {
            $smsmessage = $result->TemplateSMS;
            $createTime = strtotime($smsmessage->dateCreated);
            $data['message_id'] = (string) $smsmessage->smsMessageSid;
            $data['mobile'] = $input->username;
            $data['code'] = $smsCode;
            $data['status'] = 1;
            $data['created'] = date('Y-m-d H:i:s', $createTime);
            $data['expire_date'] = date('Y-m-d H:i:s', strtotime("+ " . C('SMS_EXPIRE_DATE') . " minutes", $createTime));
        } else {
            return $this->sendError(2500, '验证码发送失败，请重试');
        }
        
        $Sms = M('Sms');
        $Sms->where('mobile = ' . $input->username)->setField('status', 0);
        $Sms->add($data);
        unset($data);
        
        $data = array(
            'sms' => $smsCode
        );
        $result = array(
            'result' => '1',
            'message' => '验证码已发送，请注意查收',
            'data' => $data
        );
        return $this->response($result, 'json');
    }
    
    // 验证短信验证码
    public function validate()
    {        
        $cellphone = I('post.cellphone',NULL);
        $smsCode = I('post.smsCode',NULL);
        if (empty($cellphone)) {
            return $this->sendError(2500, '请输入用户名');
        }
        if (empty($input->sms)) {
            return $this->sendError(2500, '请输入验证码');
        }
            
        $User = D('User');
        $account = $User->getByPhone($cellphone);
        
        // 判断用户是否存在
        if (empty($account)) {
            return $this->sendError(2500, '用户不存在');
        }
            // todo: 查找用户最新的验证码，并且有效期大于当前时间，判断是否一致
            
        $Sms = M('Sms');
        $sms = $Sms->where('mobile = "' . $cellphone . '" AND status=1 AND expire_date> "' . date('Y-m-d H:i:s', time()) . '"')->find();
        
        if (empty($sms) || $sms['code'] != $smsCode) {
            return $this->sendError(2500, '验证码不正确或已过期');
        }
        
        $result = array(
            'result' => '1',
            'message' => '成功'
        );
        return $this->response($result, 'json');
    }

    /**
     * 判断
     *
     * @return [type] [description]
     */
    public function existUser()
    {
        try {

            $User = D('User');

            $name = I('post.name', '', 'string');
            if($name == ''){ 
                $name = I('post.username', '', 'string');
            }
            $uid = I('post.userId', '');
            if($name != ''){
                $users = $User->where("username = '" . $name . "'")->find();
            }

            if($name == "" && $uid != ''){
                $users = $User->where("id = '" . $uid . "'")->find();
            }

            if ($users) {
                $result = array(
                    'result' => '1',
                    'message' => '成功'
                );
                return $this->response($result, 'json');
            } else {
                $result = array(
                    'result' => '0',
                    'message' => '此用户不存在'
                );
                return $this->response($result, 'json');
            }
        } catch (Exception $e) {
            $result = array(
                'result' => '0',
                'message' => '失败'
            );
            return $this->response($result, 'json');
        }
    }

}