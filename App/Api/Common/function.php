<?php
/**
 * 增加日志
 * @param $log
 * @param bool $name
 */

function addlog($log, $name = false, $type = 0)
{
    $Model = M('user_log');
    if (!$name) {
        session_start();
        $uid = session('uid');
        if ($uid) {
            $user = M('member')->field('user')->where(array('uid' => $uid))->find();
            $data['name'] = $user['user'];
            $data['uid'] = $uid;

        } else {
            $data['name'] = '';
        }
    } else {
        $data['name'] = $name;
    }
    $data['t'] = time();
    $data['ip'] = $_SERVER["REMOTE_ADDR"];
    $data['log'] = $log;
    $data['log'] = $log;
    $data['type'] = $type;
    $Model->data($data)->add();
}



/**
 *
 * 获取用户信息
 *
 **/
function member($uid, $field = false)
{
    $model = M('Member');
    if ($field) {
        return $model->field($field)->where(array('uid' => $uid))->find();
    } else {
        return $model->where(array('uid' => $uid))->find();
    }
}


    function dgmdate($datetime){
        $date = time();
        if($datetime<=$date){
            $time = $date-$datetime;

            if($time > 31104000){
                $return = intval($time / 2592000).'&nbsp;年前';
            } elseif($time > 2592000){
                $return = intval($time / 2592000).'&nbsp;月前';
            } elseif($time > 86400){
                $return = intval($time / 86400).'&nbsp;天前';
            } elseif($time > 3600) {
                $return = intval($time / 3600).'&nbsp;小时前';
            } elseif($time > 1800) {
                $return = '半小时前';
            } elseif($time > 60) {
                $return = intval($time / 60).'&nbsp;分钟前';
            } elseif($time > 0) {
                $return = $time.'&nbsp;秒钟前';
            } elseif($time == 0) {
                $return = '刚刚';
            }
        }
        return $return;

    }
