<?php

namespace Api\Model;
use Think\Model;

/**
 * Token
 * Token模型，用于生成，保存和续期Token，通过Token 查询对应用户
 */
class TokenModel extends Model {

    protected $_auto = array(
        array('created', 'datetime', 3, 'callback')
    );

    /**
     *  缓存用户的Token及UID
     *  key     用户的Token(随机Hash)
     *  value   用户的身份标识
     */
     
    public function setUserToken($uid , $token = null)
    {
    	$User = D('User');
    	$username = $User->where('uid='.$uid)->getField('user');

    	if(empty($token)){
    		$token = $this->buildToken();
    	}
    	$data['uid'] = $uid;
    	$data['username'] = $username;
    	$data['token'] = $token;
        $data['created'] = $this->datetime();
    	$data['expire_date'] =  date('Y-m-d H:i:s' , strtotime("+ ". C('TOKEY_EXPIRE_DATE') ." second" , time() ));
    	$this->create($data);
        $t = $this->add($data,$options,$replace=true);

    	return $token;
    }
    
    /**
     *  通过Token获取用户的身份标识
     *  key        用户的Token(随机Hash)
     *  @return    用户的身份标识
     */
    public function getUser($token)
    {   
    	$uid = $this->where("token='".$token."' AND expire_date >'".date('Y-m-d H:i:s',time())."'")->getField('uid');
    	return $uid;
    }
    
    /**
     *  延长Token的有效期
     *  key   用户的Token(随机Hash)
     */
    public function prolong($token)
    {   
    	$mmc = $this->mmc;
    	$uid = memcache_get($mmc,$key);
    	//删除老的cache
    	$this->delete_user_cache($key);
    	//新增新的cache
    	$this->set_user_token($key,$uid);
    }
    
    /**
     *  删除用户的Token缓存
     *  key   用户的Token(随机Hash)
     */
    public function deleteToken($token)
    {   
    	$this->where("token = '".$token."'")->delete();
    }

    
    /**
     *  删除用户的Token缓存
     *  key   用户的Token(随机Hash)
     */
    public function deleteUserToken($uid)
    {   
        return $this->where("uid = '".$uid."'")->delete();
    }


     /**
     * 生成AccessToken
     * @return string
     */
    protected function buildToken($lenght = 32)
    {
        //生成AccessToken
        $str_pol = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789abcdefghijklmnopqrstuvwxyz";
        $datetime  = $this->datetime();
        return md5($datetime.C('APIKEY').str_shuffle($str_pol));
    }


    protected function datetime(){
        return date('Y-m-d H:i:s',time());
    }   

}