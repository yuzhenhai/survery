<?php
/**
 *
 * Copyright: Bodasoft
 * Author   ：dowei<douwei@bodait.com>
 * Date     ：2018-1-23
 * Version  ：1.0.0
 * Desc     ：公告该案例组制器。
 *
 **/

namespace Admin\Controller;
use Think\Upload;

class BulletinController extends ComController
{
    public function index(){

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $prefix = C('DB_PREFIX');

        $order = '';
        $where = "{$prefix}bulletin.close = 0";

        $bulletin = M('bulletin');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $bulletin->field("{$prefix}bulletin.*, {$prefix}member.user")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}bulletin.uid")
            ->where($where)
            ->count();

        $list = $bulletin->field("{$prefix}bulletin.*, {$prefix}member.user")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}bulletin.uid")
            ->where($where)
            ->limit($offset . ',' . $pagesize)
            ->select();
        // $list['content'] = htmlspecialchars_decode($list['content']);

            // htmlspecialchars
        foreach ($list as $key => $value) {
            $list[$key]['content'] = strip_tags(htmlspecialchars_decode($value['content']));
        }
        // $bulletin['content'] = strip_tags(htmlspecialchars_decode($bulletin['content']));

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->display('index');
    }

    public function add()
    {
        $this->display('form');
    }

    public function del()
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

    public function edit($id = 0)
    {
        $id = intval($id);
        $bulletin = M('bulletin')->where("id='$id'")->find();
        if (!$bulletin) {
            $this->error('参数错误！');
        }

        $this->assign('bulletin', $bulletin);
        $this->display('form');
    }

    public function update()
    {
        $id = isset($_POST['id']) ? intval($_POST['id']) : false;
        $data['id'] = I('post.id', '', 'intval');
        $data['uid'] = $this->USER['uid'];
        $data['title'] = I('post.title', '', 'strip_tags');
        $data['content'] = htmlentities($_POST['content']);
        $data['type'] = I('post.type', '', 'intval');
        $data['status'] = I('post.status', '', 'intval');
        $data['date'] = time();
        if($data['uid'] = 1){
            $data['approval'] = '1';
        }else{
            $data['approval'] = '0';
        }
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
                'savePath' => 'upload/bulletin/',
                'subName'  =>  '',
            ));

            $info = $upload->upload($_FILES);
            if($info) {
                $bulletin = M('bulletin')->where("id='{$id}'")->find();
                foreach ($info as $item) {
                    if($item['key'] == 'image'){
                        if($bulletin['cover']){
                            unlink($upload->rootPath.$item['savepath'].$bulletin['cover']);
                        }
                        $data['cover'] = $item['savename'];
                    }
                }
            }
        }

        if ($id) {
            M('bulletin')->data($data)->where("id='{$id}'")->save();
            addlog('编辑公告成功，ID：' . $id);
        } else {
            M('bulletin')->data($data)->add();
            addlog('新增公告，名称：' . $data['title']);
        }

        $this->success('操作成功！', U('index'));
    }




    public function status()
    {
        $ids = implode(',', I('post.ids'));
        $status = I('get.status');
        $approval = I('get.approval');

        if (!$ids) {
            $id = isset($_GET['id']) ? intval($_GET['id']) : false;
            if (!$id) {
                $this->error('参数错误！');
            }

            $bulletin = M('bulletin')->where('id=' . $id)->find();
            if (!$bulletin) {
                $this->error('参数错误！');
            }
            $approval = $bulletin['approval'];
            $status = $bulletin['status'];
            if(isset($_GET['status'])){
                if ($status == 1) {
                   $res = M('bulletin')->data(array('status' => 0))->where('id=' . $id)->save();
                }
                if ($status != 1 ) {
                    $res = M('bulletin')->data(array('status' => 1))->where('id=' . $id)->save();
                }
            }

            if(isset($_GET['approval'])){
                
                if ($approval == 1) {
                   $res = M('bulletin')->data(array('approval' => 0))->where('id=' . $id)->save();
                }
                if ($approval != 1 ) {
                    $res = M('bulletin')->data(array('approval' => 1))->where('id=' . $id)->save();
                }
            }

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{
            if(isset($_GET['approval'])){
                $res = M('bulletin')->data(array('approval' => $approval))->where('id in(' . $ids.')')->save();
            }
            if(isset($_GET['status'])){
                $res = M('bulletin')->data(array('status' => $status))->where('id in(' . $ids.')')->save();
            }
            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->success('更新状态成功！');
            }

        }
    }
}