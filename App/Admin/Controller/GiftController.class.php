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

namespace Admin\Controller;
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

		$page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->display();
    }
    public function del()
    {
        $ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : false;
        if (!$ids) {
            $this->error('请勾选删除礼品！');
        }
        if (!is_array($ids)) {
			$this->error('参数错误！');
        }

        $map['id'] = array('in', implode(',', $ids));
        if (M('gift')->where($map)->delete()) {

            addlog('删除礼品ID：' . implode(',', $ids));
            $this->success('礼品删除成功！');
        } else {
            $this->error('参数错误！');
        }
    }

    public function edit($id = 0)
    {
        $id = intval($id);
        $gift = M('gift');
        $currentgift = $gift->where("id='$id'")->find();
        if (!$currentgift) {
            $this->error('参数错误！');
        }

        $this->assign('currentgift', $currentgift);
        $this->display('form');
    }

    public function update()
    {
        $id = isset($_POST['id']) ? intval($_POST['id']) : false;
        $data['id'] = I('post.id', '', 'intval');
        $data['title'] = I('post.title', '', 'strip_tags');
		$data['price'] = I('post.price', '', 'strip_tags');
        $data['points'] = I('post.points', '', 'intval');
        $data['description'] = htmlentities($_POST['description']);
        $data['status'] = I('post.status', '', 'intval');
        $data['o'] = I('post.o', '', 'intval');

        if (!empty($_FILES)) {
            $mimes = array();
            $exts = array(
                'jpg',
                'png',
                'gif'
            );
            $upload = new Upload(array(
                'mimes' => $mimes,
                'exts' => $exts,
                'rootPath' => 'Public/',
                'savePath' => 'upload/images/',
                'subName'  =>  '',
            ));
            $info = $upload->upload($_FILES);
            if($info) {
                $gift = M('gift')->where("id='{$id}'")->find();
                foreach ($info as $item) {
                    if($item['key'] == 'images'){
                        if($gift['image']){
                            unlink($upload->rootPath.$item['savepath'].$gift['image']);
                        }
                        $data['image'] = $item['savename'];
                    }
                }
            }
        }


        if ($id) {
            M('gift')->data($data)->where("id='{$id}'")->save();
            addlog('编辑礼品，ID：' . $id);
        } else {
            M('gift')->data($data)->add();
            addlog('新增礼品，名称：' . $data['title']);
        }

        $this->success('操作成功！', U('index'));
    }


    public function add()
    {
        $this->display('form');
    }

    public function status()
    {
        $ids = implode(',', I('post.ids'));
        $status = I('post.status');
        if (!$ids) {
            $id = isset($_GET['id']) ? intval($_GET['id']) : false;
            if (!$id) {
                $this->error('参数错误！');
            }

            $gift = M('gift')->where('id=' . $id)->find();
            if (!$gift) {
                $this->error('参数错误！');
            }

            $status = $gift['status'];
            if ($status == 1) {
               $res = M('gift')->data(array('status' => 0))->where('id=' . $id)->save();
            }
            if ($status != 1 ) {
                $res = M('gift')->data(array('status' => 1))->where('id=' . $id)->save();
            }
            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{
            $res = M('gift')->data(array('status' => $status))->where('id in(' . $ids.')')->save();

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->success('更新状态成功！');
            }

        }
    }
    public function exchange()
    {
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $status = isset($_GET['status']) ? intval($_GET['status']) : false;
        $orderno = isset($_GET['orderno']) ? intval($_GET['orderno']) : false;

		$order = '';
        $where = array();

        $prefix = C('DB_PREFIX');

        if($status){
            $where[] = "{$prefix}gift_exchange.status =". $status;
        }
        if($orderno){
            $where[] = "{$prefix}gift_exchange.orderno =". $orderno;
        }
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }

		$gift_exchange = M('gift_exchange');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $gift_exchange->field("{$prefix}gift_exchange.*,{$prefix}gift.title,{$prefix}gift.points,{$prefix}member.user as member")
            ->join("{$prefix}gift ON {$prefix}gift.id = {$prefix}gift_exchange.gift_id")
			->join("{$prefix}member ON {$prefix}member.uid = {$prefix}gift_exchange.uid")
            ->order($order)
            ->where($where)
            ->count();

        $list = $gift_exchange->field("{$prefix}gift_exchange.*,{$prefix}gift.title,{$prefix}gift.points,{$prefix}member.user as member")
            ->join("{$prefix}gift ON {$prefix}gift.id = {$prefix}gift_exchange.gift_id")
			->join("{$prefix}member ON {$prefix}member.uid = {$prefix}gift_exchange.uid")
            ->order($order)
            ->where($where)
            ->limit($offset . ',' . $pagesize)
            ->select();

		$page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->display();
    }

    public function pay()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : false;
        if (!$id) {
            $this->error('参数错误！');
        }

        $gift_exchange = M('gift_exchange')->where('id=' . $id)->find();
        if (!$gift_exchange) {
            $this->error('参数错误！');
        }

        $status = $gift_exchange['status'];
        if ($status == 1) {
           $$this->error('已经支付，请勿重复操作');
        }
        if ($status == 0 ) {
            //todo 支付
            $res = M('gift_exchange')->data(array('status' => 1))->where('id=' . $id)->save();
        }
        if ($res) {
            $this->success('支付成功！');
        } else {
            $this->error('更新失败！');
        }
    }
    public function info($id = 0)
    {
        $id = intval($id);

		$prefix = C('DB_PREFIX');

		$order = '';
        $where = "{$prefix}gift_exchange.id=".$id;

        $gift_exchange = M('gift_exchange');
		$currentgift_exchange = $gift_exchange->field("{$prefix}gift_exchange.*,{$prefix}gift.title,{$prefix}gift.points,{$prefix}member.user as member")
            ->join("{$prefix}gift ON {$prefix}gift.id = {$prefix}gift_exchange.gift_id")
			->join("{$prefix}member ON {$prefix}member.uid = {$prefix}gift_exchange.uid")
            ->order($order)
            ->where($where)
            ->limit(1)
            ->select();

		if (!$currentgift_exchange) {
            $this->error('参数错误！');
        }

        $this->assign('currentgift_exchange', $currentgift_exchange[0]);
        $this->display('info');
    }


    public function proclamation(){

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $prefix = C('DB_PREFIX');

        $order = '';
        $where = "{$prefix}gift_proclamation.close = 0";

        $gift = M('gift_proclamation');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $gift->field("{$prefix}gift_proclamation.*, {$prefix}member.user")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}gift_proclamation.uid")
            ->where($where)
            ->count();

        $list = $gift->field("{$prefix}gift_proclamation.*, {$prefix}member.user")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}gift_proclamation.uid")
            ->where($where)
            ->limit($offset . ',' . $pagesize)
            ->select();

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->display('proclamation');
    }

    public function padd()
    {
        $this->display('proclamationform');
    }

    public function pdel()
    {
        $ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : false;

        if (!$ids) {
            $this->error('请勾选删除公告！');
        }
        if (!is_array($ids)) {
            $this->error('参数错误！');
        }

        $close['close'] = 1;
        $map['id'] = array('in', implode(',', $ids));
        if (M('gift_proclamation')->data($close)->where($map)->save()) {

            addlog('删除公告ID：' . implode(',', $ids));
            $this->success('公告删除成功！');
        } else {
            $this->error('参数错误！');
        }
    }

    public function pedit($id = 0)
    {
        $id = intval($id);
        $gift = M('gift_proclamation');
        $proclamation = $gift->where("id='$id'")->find();
        if (!$proclamation) {
            $this->error('参数错误！');
        }

        $this->assign('proclamation', $proclamation);
        $this->display('proclamationform');
    }

    public function pupdate()
    {
        $id = isset($_POST['id']) ? intval($_POST['id']) : false;
        $data['id'] = I('post.id', '', 'intval');
        $data['uid'] = $this->USER['uid'];
        $data['title'] = I('post.title', '', 'strip_tags');
        $data['proclamation'] = I('post.proclamation', '', 'strip_tags');
        $data['status'] = I('post.status', '', 'intval');
        $data['date'] = time();
        if($data['uid'] = 1){
            $data['approval'] = '1';
        }else{
            $data['approval'] = '0';
        }

        if ($id) {
            M('gift_proclamation')->data($data)->where("id='{$id}'")->save();
            addlog('编辑礼品，ID：' . $id);
        } else {
            M('gift_proclamation')->data($data)->add();
            addlog('新增公告，名称：' . $data['title']);
        }

        $this->success('操作成功！', U('proclamation'));
    }




    public function pstatus()
    {
        $ids = implode(',', I('post.ids'));
        $status = I('get.status');
        $approval = I('get.approval');

        if (!$ids) {
            $id = isset($_GET['id']) ? intval($_GET['id']) : false;
            if (!$id) {
                $this->error('参数错误！');
            }

            $gift = M('gift_proclamation')->where('id=' . $id)->find();
            if (!$gift) {
                $this->error('参数错误！');
            }
            $approval = $gift['approval'];
            $status = $gift['status'];
            if(isset($_GET['status'])){
                if ($status == 1) {
                   $res = M('gift_proclamation')->data(array('status' => 0))->where('id=' . $id)->save();
                }
                if ($status != 1 ) {
                    $res = M('gift_proclamation')->data(array('status' => 1))->where('id=' . $id)->save();
                }
            }

            if(isset($_GET['approval'])){
                if ($approval == 1) {
                   $res = M('gift_proclamation')->data(array('approval' => 0))->where('id=' . $id)->save();
                }
                if ($approval != 1 ) {
                    $res = M('gift_proclamation')->data(array('approval' => 1))->where('id=' . $id)->save();
                }
            }

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{
            if(isset($_GET['approval'])){
                $res = M('gift_proclamation')->data(array('approval' => $approval))->where('id in(' . $ids.')')->save();
            }
            if(isset($_GET['status'])){
                $res = M('gift_proclamation')->data(array('status' => $status))->where('id in(' . $ids.')')->save();
            }
            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->success('更新状态成功！');
            }

        }
    }

    public function setting()
    {

        $this->display('setting');
    }

    public function saveSetting()
    {
        $data = $_POST;
        $data['subject_credit'] = isset($_POST['subject_credit']) ? intval(strip_tags($_POST['subject_credit'])) : 0;
        $data['post_credit'] = isset($_POST['post_credit']) ? intval(strip_tags($_POST['post_credit'])) : 0;
        $data['digest_credit'] = isset($_POST['digest_credit']) ? intval(strip_tags($_POST['digest_credit'])) : 0;
        $data['star_credit'] = isset($_POST['star_credit']) ? intval(strip_tags($_POST['star_credit']))  : 0;
        $data['post_credit_comment'] = isset($_POST['post_credit_comment']) ? intval(strip_tags($_POST['post_credit_comment']))  : 0;


        $Model = M('setting');
        foreach ($data as $k => $v) {
            $setting = $Model->where("k='$k'")->find();
            $ret['k'] = $k;
            $ret['v'] = $v;
            $ret['type'] = '0';
            $ret['name'] = '';

            if(isset($setting)){
                $Model->data(array('v' => $v))->where("k='{$k}'")->save();
            }else{
                $Model->data($ret)->add();
            }
        }
        //清除旧的缓存数据
        $cache = \Think\Cache::getInstance();
        $cache->clear();
        addlog('修改交流圈配置。');
        $this->success('交流圈配置修改成功！');
    }

}