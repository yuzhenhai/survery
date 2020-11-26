<?php
/**
 *
 * Copyright: Bodasoft
 * Author   ：dowei<douwei@bodait.com>
 * Date     ：2018-1-23
 * Version  ：1.0.0
 * Desc     ：用户组制器。
 *
 **/
namespace Home\Controller;
use Think\Controller;
use Think\Upload;

class GiftController extends ComController
{
    public function index()
    {


        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
		$order = '';
        $where = '';
        $prefix = C('DB_PREFIX');

		$gift = M('gift');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $gift->field("{$prefix}gift.*")
            ->order($order)
            ->where($where)
            ->count();

        $list = $gift->field("{$prefix}gift.*")
            ->order($order)
            ->where($where)
            ->limit($offset . ',' . $pagesize)
            ->select();

        // $user = M('member')->where(array('uid' => $uid))->find();
        $user = $this->USER;

		$page = new \Think\Page($count, $pagesize);
        $page = $page->show();


        $bulletin = M('bulletin');

        $bulletin = $bulletin->field("{$prefix}bulletin.*, {$prefix}member.user")
            ->order("{$prefix}bulletin.date desc")
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}bulletin.uid")
            ->where("{$prefix}bulletin.type = 2")
            ->select();
        $integral = M('member_score')->field("sum({$prefix}member_score.score) as total")
            ->where("{$prefix}member_score.uid = ".$user['uid'])
            ->find();
        $this->assign('bulletin', $bulletin);
        $this->assign('integral', $integral);

        $users = $this->user();
        $this->assign('user', $user);
        $this->assign('users', $users);
        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->display();
    }
    public function bulls(){
        $id = isset($_GET['id']) ? $_GET['id'] : 1;
        $prefix = C('DB_PREFIX');
        $bulletin = M('bulletin');
        $bulletin = $bulletin->field("{$prefix}bulletin.*, {$prefix}member.user, {$prefix}member.head")
            ->order("{$prefix}bulletin.date desc")
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}bulletin.uid")
            ->where("{$prefix}bulletin.type = 2 and {$prefix}bulletin.id = $id")
            ->find();

        $this->assign('bulletin', $bulletin);
        $this->display('bulls');
    }

    public function exchange(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : false;
        $uid = $this->USER['uid'];
        $prefix = C('DB_PREFIX');

        if(!$id){
            $this->error("参数错误！");
        }
        $gift = M('gift')->field("{$prefix}gift.*")->where("id=$id")->find();


        $data['gift_id'] = $gift['id'];
        $data['uid'] = $uid;
        $data['status'] = 0;
        $data['created'] = date('Y-m-d H:i:s',time());

        $score['uid'] = $uid;
        $score['score'] = '-'.$gift['points'];
        $score['tid'] = 0;
        $score['gid'] = $id;
        $score['relevance_name'] = 'gift';
        $score['relevance_module'] = 'id';
        $score['relevance_value'] = $id;
        $score['type'] = '5';
        $score['info'] = '积分兑换:'.$gift['title'];
        $score['date'] = time();
        $member['integral'] = $this->USER['integral']-$gift['points'];

        // $exchange = M('gift_exchange')->field("{$prefix}gift_exchange.*")->where("gift_id=$id AND uid=$uid")->find();
        // $scores = M('member_score')->field("{$prefix}member_score.*")->where("gid=$id AND uid=$uid")->find();
        
        // if($exchange || $scores){
        //     $this->error("您已兑换该物品！");
        // }

        if ($id) {
            $eid = M('gift_exchange')->data($data)->add();
            $sid = M('member_score')->data($score)->add();

            $y = date('y',time());
            $m = date('m',time());

            $order = '';
            for($i=strlen($eid);$i<4;$i++){
                $order .= 0;
            }
            $ge['orderno'] = $y.$m.$order.$eid;
            if($eid){
                M('gift_exchange')->data($ge)->where("id='$eid'")->save();
            }

            M('member')->data($member)->where("uid='$uid'")->save();
            addlog('积分兑换 兑换记录，ID：' . $eid,false,4);
        }

        $this->success('积分兑换成功！');
    }
}