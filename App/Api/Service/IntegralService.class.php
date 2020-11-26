<?php

namespace Api\Service;

/**
 * IntegralService
 * 圈子内容模型
 */
class IntegralService extends CommonService {

    public function increase($userId, $operation, $relationId){

        if( empty($userId) || empty($operation) || empty($relationId) ){
            return false;
        }


        //获得积分配置
        $Config = M('Config');
        $hotValue = $Config->where("`key`  like '". $operation ."Hot' ")->getfield('value');
        $creditValue = $Config->where("`key` = '". $operation ."Credit' ")->getfield('value');

        //用户增加积分
        //记录热度变更日志
        $Userinfo = M('Userinfo');
        $IntegralLog = D('IntegralLog');
        if($hotValue > 0 ){
            $Userinfo->where("user_id = '". $userId."'")->setInc('hot',$hotValue);
            $IntegralLog->log($userId,1,$hotValue,$operation,$relationId);
        }
        if($creditValue > 0){
            $Userinfo->where("user_id = '". $userId."'")->setInc('credit',$creditValue);
            $IntegralLog->log($userId,2,$creditValue,$operation,$relationId);
        }

        return true;
    }

    //取消获得的积分，包括热度和用户积分，
    public function cancel($userId, $operation, $relationId){
        //查找对应的积分记录并修改状态
        $IntegralLog = D('IntegralLog');
        $where = array(
             'user_id' => $userId,
             'opration'=> $opration,
             'relationId' => $relationId,
             'type'    => '1',
             'stauts'  => '1'
            );
        $logs = $IntegralLog->where($where)->find();
        if($logs){
            /* foreach($logs AS $log){ todo
                echo "1";

            }
            $Userinfo = M('Userinfo');
            $Userinfo->where("user_id = '". $userId."'")->setInc('hot',$hotValue);
            $Userinfo->where("user_id = '". $userId."'")->setInc('credit',$creditValue);*/            
            //减积分
            //重置记录
        }
        //根据积分记录修正用户积分
    }

    public function decrease(){

    }
}