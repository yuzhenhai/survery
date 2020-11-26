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

class ThemeController extends ComController
{
    public function index()
    {

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

		$prefix = C('DB_PREFIX');
        if ($order == 'asc') {
            $order = "{$prefix}thread_theme.dateline asc";
        } elseif (($order == 'desc')) {
            $order = "{$prefix}thread_theme.dateline desc";
        } else {
            $order = "{$prefix}thread_theme.dateline asc";
        }
        $where = array();

        if ($keyword <> '') {
            if ($field == 'subject') {
                $where[] = "{$prefix}thread_theme.subject LIKE '%$keyword%'";
            }
            if ($field == 'views') {
                $where[] = "{$prefix}thread_theme.views = '$keyword'";
            }
            if ($field == 'favtimes') {
                $where[] = "{$prefix}thread_theme.favtimes = '$keyword'";
            }
            if ($field == 'username') {
                $where[] = "{$prefix}thread_theme.username = '$keyword'";
            }

            if ($field == 'closed') {
                $where[] = "{$prefix}thread_theme.closed = '$keyword'";
            }

        }else{
            if(!isset($_GET['closed'])){
                $where[] = "{$prefix}thread_theme.closed='1'";
            }
        }

		$wherestring = '';
		if(count($where)){
			$wherestring = implode(' AND ',$where);
		}
		$talks = M('thread_theme');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $talks->field("{$prefix}thread_theme.*,{$prefix}auth_group.title")
            ->order($order)
            ->join("{$prefix}auth_group ON {$prefix}auth_group.id = {$prefix}thread_theme.groupid")
            ->where($wherestring)
            ->count();

        $list = $talks->field("{$prefix}thread_theme.*,{$prefix}auth_group.title")
            ->order($order)
            ->join("{$prefix}auth_group ON {$prefix}auth_group.id = {$prefix}thread_theme.groupid")
            ->where($wherestring)
            ->limit($offset . ',' . $pagesize)
            ->select();


		$page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->display();
    }


    public function status()
    {
        $ids = implode(',', I('post.ids'));

        $status = I('get.status');
        $highlight = I('get.highlight');
        $digest = I('get.digest');
        $id = I('get.id');
        $closed = I('get.closed');

        if (!$ids) {
            $id = isset($_GET['id']) ? intval($_GET['id']) : false;
            if (!$id) {
                $this->error('参数错误！');
            }

            $topic = M('thread_theme')->where('id=' . $id)->find();
            if (!$topic) {

                $this->error('参数错误！');
            }

            // $status = $topic['status'];
            //         var_dump($status);exit;
            if(isset($_GET['status'])){
                if ($status == 1) {
                   $res = M('thread_theme')->data(array('status' => 0))->where('id=' . $id)->save();
                }
                if ($status == 0 ) {
                    $res = M('thread_theme')->data(array('status' => 1))->where('id=' . $id)->save();
                }
            }

            if(isset($_GET['highlight'])){
                if ($highlight == 1) {
                   $res = M('thread_theme')->data(array('highlight' => 0))->where('id=' . $id)->save();
                }
                if ($highlight == 0 ) {
                    $res = M('thread_theme')->data(array('highlight' => 1))->where('id=' . $id)->save();
                }
            }

            if(isset($_GET['digest'])){
                if ($digest == 1) {
                   $res = M('thread_theme')->data(array('digest' => 0))->where('id=' . $id)->save();
                }
                if ($digest == 0 ) {
                    $res = M('thread_theme')->data(array('digest' => 1))->where('id=' . $id)->save();
                }
            }
            if(isset($_GET['closed'])){
                if ($closed == 1) {
                   $res = M('thread_theme')->data(array('closed' => 0))->where('id=' . $id)->save();
                }
                if ($closed == 0 ) {
                    $res = M('thread_theme')->data(array('closed' => 1))->where('id=' . $id)->save();
                }
            }




            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{

            if(isset($_GET['status'])){
                   $res = M('thread_theme')->data(array('status' => $status))->where('id in(' . $ids.')')->save();

            }
            if(isset($_GET['highlight'])){
                $res = M('thread_theme')->data(array('highlight' => $highlight))->where('id in(' . $ids.')')->save();

            }
            if(isset($_GET['digest'])){
                   $res = M('thread_theme')->data(array('digest' => $digest))->where('id in(' . $ids.')')->save();

            }
            if(isset($_GET['closed'])){
                $res = M('thread_theme')->data(array('closed' => $closed))->where('id in(' . $ids.')')->save();
            }

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->success('更新状态成功！');
            }

        }
    }



    public function reply(){

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

        $prefix = C('DB_PREFIX');
        if ($order == 'asc') {
            $order = "{$prefix}thread_replies.datetime asc";
        } elseif (($order == 'desc')) {
            $order = "{$prefix}thread_replies.datetime desc";
        } else {
            $order = "{$prefix}thread_replies.datetime asc";
        }
        $where = array();
        if ($keyword <> '') {
            if ($field == 'subject') {
                $where[] = "{$prefix}thread_theme.subject LIKE '%$keyword%'";
            }
            if ($field == 'realname') {
                $where[] = "{$prefix}member.realname LIKE '%$keyword%'";
            }
            if ($field == 'replies') {
                $where[] = "{$prefix}thread_replies.replies LIKE '%$keyword%'";
            }            
            if ($field == 'status') {
                $where[] = "{$prefix}thread_replies.status = '$keyword'";
            }
            if ($field == 'closed') {
                $where[] = "{$prefix}thread_replies.closed = '$keyword'";
            }

        } else {
            $where[] = "{$prefix}thread_replies.closed = 1";
        }

        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }

        $talks = M('thread_replies');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $talks->field("{$prefix}thread_replies.*,{$prefix}member.realname,{$prefix}thread_theme.subject")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}thread_replies.uid")
            ->join("{$prefix}thread_theme ON {$prefix}thread_theme.id = {$prefix}thread_replies.tid")
            ->where($wherestring)
            ->count();

        $list = $talks->field("{$prefix}thread_replies.*,{$prefix}member.realname,{$prefix}thread_theme.subject")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}thread_replies.uid")
            ->join("{$prefix}thread_theme ON {$prefix}thread_theme.id = {$prefix}thread_replies.tid")
            ->where($wherestring)
            ->limit($offset . ',' . $pagesize)
            ->select();


        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->display('reply');

    }


    public function rstatus()
    {
        $ids = implode(',', I('post.ids'));

        $status = I('get.status');
        $closed = I('get.closed');
        $approval = I('get.approval');

        if (!$ids) {
            $id = isset($_GET['id']) ? intval($_GET['id']) : false;
            if (!$id) {
                $this->error('参数错误！');
            }

            $topic = M('thread_replies')->where('id=' . $id)->find();
            if (!$topic) {

                $this->error('参数错误！');
            }

            // $status = $topic['status'];
            //         var_dump($status);exit;
            if(isset($_GET['status'])){
                if ($status == 1) {
                   $res = M('thread_replies')->data(array('status' => 0))->where('id=' . $id)->save();
                }
                if ($status == 0 ) {
                    $res = M('thread_replies')->data(array('status' => 1))->where('id=' . $id)->save();
                }
            }

            if(isset($_GET['approval'])){
                if ($approval == 1) {
                   $res = M('thread_replies')->data(array('approval' => 0))->where('id=' . $id)->save();
                }
                if ($approval == 0 ) {
                    $res = M('thread_replies')->data(array('approval' => 1))->where('id=' . $id)->save();
                }
            }

            if(isset($_GET['closed'])){
                if ($closed == 1) {
                   $res = M('thread_replies')->data(array('closed' => 0))->where('id=' . $id)->save();
                }
                if ($closed == 0 ) {
                    $res = M('thread_replies')->data(array('closed' => 1))->where('id=' . $id)->save();
                }
            }


            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{

            if(isset($_GET['status'])){
                $res = M('thread_replies')->data(array('status' => $status))->where('id in(' . $ids.')')->save();
            }

            if(isset($_GET['closed'])){
                $res = M('thread_replies')->data(array('closed' => $closed))->where('id in(' . $ids.')')->save();
            }

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->success('更新状态成功！');
            }

        }
    }


 public function comments(){

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

        $prefix = C('DB_PREFIX');
        if ($order == 'asc') {
            $order = "{$prefix}thread_comments.datetime asc";
        } elseif (($order == 'desc')) {
            $order = "{$prefix}thread_comments.datetime desc";
        } else {
            $order = "{$prefix}thread_comments.datetime asc";
        }
        $where = array();        
        if ($keyword <> '') {

            if ($field == 'realname') {
                $where[] = "{$prefix}member.realname LIKE '%$keyword%'";
            }
            if ($field == 'subject') {
                $where[] = "{$prefix}thread_theme.subject LIKE '%$keyword%'";
            }
            if ($field == 'replies') {
                $where[] = "{$prefix}thread_replies.replies LIKE '%$keyword%'";
            }
            if ($field == 'comment') {
                $where[] = "{$prefix}thread_comments.comment LIKE '%$keyword%'";
            }
            if ($field == 'status') {
                $where[] = "{$prefix}thread_comments.status = '$keyword'";
            }

            if ($field == 'closed') {
                $where[] = "{$prefix}thread_comments.closed = '$keyword'";
            }
            
        }else{
            $where[] = "{$prefix}thread_comments.closed = '1'";
            $where[] = "{$prefix}thread_theme.closed = '1'";
            $where[] = "{$prefix}thread_comments.closed = '1'";
        }




        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }

        $talks = M('thread_comments');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $talks->field("{$prefix}thread_comments.*,{$prefix}member.realname,{$prefix}thread_theme.subject,{$prefix}thread_replies.replies")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}thread_comments.uid")
            ->join("{$prefix}thread_theme ON {$prefix}thread_theme.id = {$prefix}thread_comments.tid")
            ->join("{$prefix}thread_replies ON {$prefix}thread_replies.id = {$prefix}thread_comments.rid")

            ->where($wherestring)
            ->count();

        $list = $talks->field("{$prefix}thread_comments.*,{$prefix}member.realname,{$prefix}thread_theme.subject,{$prefix}thread_replies.replies")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}thread_comments.uid")
            ->join("{$prefix}thread_theme ON {$prefix}thread_theme.id = {$prefix}thread_comments.tid")
            ->join("{$prefix}thread_replies ON {$prefix}thread_replies.id = {$prefix}thread_comments.rid")
            ->where($wherestring)
            ->limit($offset . ',' . $pagesize)
            ->select();
        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->display('comments');

    }


    public function cstatus()
    {
        $ids = implode(',', I('post.ids'));

        $status = I('get.status');
        $closed = I('get.closed');

        if (!$ids) {
            $id = isset($_GET['id']) ? intval($_GET['id']) : false;
            if (!$id) {
                $this->error('参数错误！');
            }

            $topic = M('thread_comments')->where('id=' . $id)->find();
            if (!$topic) {

                $this->error('参数错误！');
            }

            // $status = $topic['status'];
            //         var_dump($status);exit;
            if(isset($_GET['status'])){
                if ($status == 1) {
                   $res = M('thread_comments')->data(array('status' => 0))->where('id=' . $id)->save();
                }
                if ($status == 0 ) {
                    $res = M('thread_comments')->data(array('status' => 1))->where('id=' . $id)->save();
                }
            }

            if(isset($_GET['closed'])){
                if ($closed == 1) {
                   $res = M('thread_comments')->data(array('closed' => 0))->where('id=' . $id)->save();
                }
                if ($closed == 0 ) {
                    $res = M('thread_comments')->data(array('closed' => 1))->where('id=' . $id)->save();
                }
            }

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{

            if(isset($_GET['status'])){
                   $res = M('thread_comments')->data(array('status' => $status))->where('id in(' . $ids.')')->save();

            }

            if(isset($_GET['closed'])){
                $res = M('thread_comments')->data(array('closed' => $closed))->where('id in(' . $ids.')')->save();
            }

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->success('更新状态成功！');
            }

        }
    }



}