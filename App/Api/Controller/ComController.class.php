<?php
namespace Api\Controller;
use Think\Controller\RestController;

/**
 * ComController
 * 通用控制器
 */
class ComController extends RestController {

	protected $userId = 0;
    protected $user   = null;
    protected $input  = '';

	public function _initialize() {
        C(setting());
		//解决跨域问题
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');
        //$input = json_decode(preg_replace("#(\\\ue[0-9a-f]{3})#ie","addslashes('\\1')" ,file_get_contents('php://input')));

        $input = json_decode(file_get_contents('php://input'));
        $this->input = $input;
        foreach($input AS $key => $val ){
            $_POST[$key] = $val;
            if($key == 'p'){
                $_GET['p'] = $val;
            }
        }

        //对application/json方式的请求重建$_POST
        /* if( isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] == 'application/json'){
        	$input = json_decode(file_get_contents('php://input'));
        	foreach($input AS $key => $val ){
        		$_POST[$key] = $val;
        	}
        }*/
    }

	public function _empty(){
		$result = array('result'=>'0','message'=>'错误的请求');
		return $this->response($result,'json','404');
	}

    public function filterLogin(){
        //通过token 获得用户ID，但不做权限验证。找不到用户id或者没有token，用户id设为 0
        $accessToken = I('post.accessToken',NULL);
        $uid = I('post.uid',0);
        $Token = D('Token');
        $this->userId = $Token->getUser($accessToken);
        if($uid !== $this->userId ){
            return $this->sendError(2010,'Token无效或已过期');
        }
        /* $M = M('member')->where(array('uid' => $uid))->find();
        $this->userId = $M['uid'];
        $this->user = M('member')->where(array('uid' =>  $this->userId))->find();

        if(empty($this->userId) || $this->user['phone'] != $cellphone ){
            return $this->sendError(2010,'Token无效或已过期');
        }*/
        return true;
    }

    public function datetime(){
        return date('Y-m-d H:i:s',time());
    }

    public function sendSuccess($data = [], $message = 'success', $code = 200, $type = 'json',  $options = [])
    {
        $responseData['error'] = 0;
        $responseData['message'] = (string)$message;
        if (!empty($data)) $responseData['data'] = $data;
        $responseData = array_merge($responseData, $options);
        return $this->response($responseData, $type, $code);
    }

    public function sendError($error = 400, $message = 'error',$data = [], $code = 400, $type = 'json', $options = [])
    {
        $responseData['error'] = (int)$error;
        $responseData['message'] = (string)$message;
        if (!empty($data)) $responseData['data'] = $data;
        $responseData = array_merge($responseData, $options);
        return $this->response($responseData, $type, $code);
    }

}