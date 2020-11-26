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

class TopicController extends ComController
{
    public $USER;

    public function index()
    {

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $approval = isset($_GET['approval']) ? $_GET['approval'] : '0';
        $close = isset($_GET['close']) ? $_GET['close'] : '0';
        $status = isset($_GET['status']) ? $_GET['status'] : '1';


		$prefix = C('DB_PREFIX');

        $order = "{$prefix}talk_topic.date DESC";
        $where = array();
        if ($keyword <> '') {
            if ($field == 'name') {
                $where[] = "{$prefix}talk_topic.name LIKE '%$keyword%'";
            }
            if ($field == 'realname') {
                $where[] = "{$prefix}member.realname LIKE '%$keyword%'";
            }
            if ($field == 'uid') {
                $where[] = "{$prefix}talk_topic.uid = '$keyword'";
            }
        }
        if(isset($_GET['status'])){
            $where[] = "{$prefix}talk_topic.status = '$status'";
        }
        if(isset($_GET['approval'])){
            $where[] = "{$prefix}talk_topic.approval = '$approval'";
        }
        if(isset($_GET['close'])){
            $where[] = "{$prefix}talk_topic.close = '$close'";
        }else{
            $where[] = "{$prefix}talk_topic.close = '0'";
        }

		$wherestring = '';
		if(count($where)){
			$wherestring = implode(' AND ',$where);
		}

		$talks = M('talk_topic');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $talks->field("{$prefix}talk_topic.*,{$prefix}member.realname")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}talk_topic.uid")
            ->where($wherestring)
            ->count();

        $list = $talks->field("{$prefix}talk_topic.*,{$prefix}member.realname")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}talk_topic.uid")
            ->where($wherestring)
            ->limit($offset . ',' . $pagesize)
            ->select();
        foreach ($list as $key => $value) {
            if(empty($value['image'])){
                $list[$key]['image'] = '5ab074d8a63e8.png';
            }
        }


		$page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $userid = $this->USER['uid'];
        $this->assign('userid', $userid);
        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->display();
    }
    public function add()
    {

        $this->display('form');
    }

    public function del()
    {

        $ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : false;
        if (!$ids) {
            $this->error('请勾选删除任务！');
        }
        if (!is_array($ids)) {
			$this->error('参数错误！');
        }
        $usergroup = M('auth_group')->field('id,title')->select();
        $this->assign('usergroup', $usergroup);
        $data['close'] = '1';
        $map['id'] = array('in', implode(',', $ids));

        if (M('talk_topic')->data($data)->where($map)->save()) {

            addlog('删除任务ID：' . implode(',', $ids));
            $this->success('任务删除成功！');
        } else {
            $this->error('参数错误！');
        }
    }

    public function edit($id = 0)
    {
		$prefix = C('DB_PREFIX');

        $id = intval($id);
        $topics = M('talk_topic');
        $topic = $topics->where("id='$id'")->find();
        if ($this->USER['uid']!=1) {
            $this->error('您暂无此权限！');
        }


        $this->assign('topic', $topic);
        $this->display('form');
    }

    public function update()
    {

        $id = isset($_POST['id']) ? intval($_POST['id']) : false;
        $data['id'] = I('post.id', '', 'intval');
		$data['name'] = I('post.name', '', 'strip_tags');
		$data['description'] = I('post.description', '', 'strip_tags');
        $data['date'] = date('Y-m-d H:i:s');
        $data['comment'] = I('post.comment', '', 'strip_tags');
        // $data['image'] = I('post.image', '', 'strip_tags');
        $data['abstract'] = I('post.abstract', '', 'strip_tags');
        $data['uid'] = $this->USER['uid'];
        $name = I('post.name', '', 'strip_tags');

        $status = I('post.status', '', 'intval');

        if($status == 1){
            $data['status'] = $status;
        } else {
            $data['status'] = 0;
        }

        $approval = I('post.approval', '', 'intval');

        if($approval == 1){
            $data['approval'] = $approval;
        } else {
            $data['approval'] = 0;
        }

        // $data['image'] = '/Public/themes/images/'.$data['image'];

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
            if($info) {// 上传成功
                $talk_topic = M('talk_topic')->where("id='{$id}'")->find();
                foreach ($info as $item) {
                    if($item['key'] == 'image'){
                        if($talk_topic['image']){
                            unlink($upload->rootPath.$item['savepath'].$talk_topic['image']);
                        }
                        $data['image'] = $item['savename'];
                    }

                }

            }else{
                $data['image'] = I('post.image1', '', 'strip_tags');
            }
        }
        if (empty($name)) {
            $this->error('话题名称不能为空！');
        }

        if ($id) {
            M('talk_topic')->data($data)->where("id='{$id}'")->save();
            addlog('编辑任务，ID：' . $id);
        } else {
            M('talk_topic')->data($data)->add();
            addlog('新增任务，名称：' . $data['name']);
        }

        $this->success('操作成功！',U('index'));
    }

    public function status()
    {
        $ids = implode(',', I('post.ids'));
        $status = I('get.status');
        $approval = I('get.approval');
        $ess = I('get.ess');
        $stick = I('get.stick');

        $digest_credit = C("digest_credit");


        if (!$ids) {
            $id = isset($_GET['id']) ? intval($_GET['id']) : false;
            if (!$id) {
                $this->error('参数错误！');
            }

            $topic = M('talk_topic')->where('id=' . $id)->find();
            if (!$topic) {
                $this->error('参数错误！');
            }

            $status = $topic['status'];
            $approval = $topic['approval'];
            $ess = $topic['ess'];

                    // var_dump($status);exit;
            if(isset($_GET['approval'])){
                if ($approval == 1) {

                   $res = M('talk_topic')->data(array('approval' => 0))->where('id=' . $id)->save();
                }

                if ($approval == 0 ) {
                    $res = M('talk_topic')->data(array('approval' => 1))->where('id=' . $id)->save();
                }
            }
            if(isset($_GET['status'])){
                
                if ($status == 1) {
                   $res = M('talk_topic')->data(array('status' => 0))->where('id=' . $id)->save();
                }
                if ($status == 0 ) {
                    $res = M('talk_topic')->data(array('status' => 1))->where('id=' . $id)->save();
                }
            }

            if(isset($_GET['ess'])){

                $member = M('member')->where('uid=' . $topic['uid'])->find();
                $member_score = M('member_score')->where('uid=' . $topic['uid'] . ' and tid=' .$id . ' and type=3')->find();

                if ($ess == 1) {
                    $integral = $member['integral']-$digest_credit;
                    if($integral>0){
                        $integral = $integral;
                    }else{
                        $integral = 0;
                    }
                    M('member')->data(array('integral' => $integral))->where('uid=' . $topic['uid'])->save();
                    //取消精华
                    // if(!$member_score){

                    //     $score = array();
                    //     $score['uid'] = $topic['uid'];
                    //     $score['tid'] = $id;
                    //     $score['score'] = '-'.$digest_credit;
                    //     $score['relevance_name'] = 'talk_topic';
                    //     $score['relevance_module'] = 'id';
                    //     $score['relevance_value'] = $id;

                    //     $score['info'] = '取消发帖精华';
                    //     $score['type'] = 3;

                    //     $score['date'] = time();
                    //     M('member_score')->data($data)->add();

                    //     addlog('管理员取消精华，取消用户UID：'.$topic['uid'].'减去积分:'.$integral);
                    // } else {
                    //     addlog('管理员取消精华，取消用户UID：'.$topic['uid']);
                    // }
                    // $res = M('talk_topic')->data(array('status' => 1))->where('id=' . $id)->save();

                    // var_dump($res);exit;
                   $res = M('talk_topic')->data(array('ess' => 0))->where('id=' . $id)->save();
                }
 
                if ($ess == 0 ) {
                    $integral = $member['integral']+$digest_credit;
                    M('member')->data(array('integral' => $integral))->where('uid=' . $topic['uid'])->save();
                    $topic = M('talk_topic')->where('id=' . $id)->find();
                    if(!$member_score){
                        
                        $score = array();
                        $score['uid'] = $topic['uid'];
                        $score['tid'] = $id;
                        $score['score'] = C('digest_credit');
                        $score['relevance_name'] = 'talk_topic';
                        $score['relevance_module'] = 'id';
                        $score['relevance_value'] = $id;
                        $score['info'] = '发帖精华';
                        $score['type'] = '3';

                        $score['date'] = time();
                        M('member_score')->data($score)->add();

                        addlog('管理员添加精华，添加精华用户UID：'.$topic['uid']);
                    }else{
                        addlog('管理员添加精华，添加精华用户UID：'.$topic['uid']);
                    }
                    $res = M('talk_topic')->data(array('ess' => 1))->where('id=' . $id)->save();
                }
            }
            if(isset($_GET['stick'])){
                if ($stick == 1) {
                   $res = M('talk_topic')->data(array('stick' => 0))->where('id=' . $id)->save();
                }

                if ($stick == 0 ) {
                    $res = M('talk_topic')->data(array('stick' => 1))->where('id=' . $id)->save();
                }
            }

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{

            if(isset($_GET['status'])){
                $res = M('talk_topic')->data(array('status' => $status))->where('id in(' . $ids.')')->save();
            }
            // var_dump($_GET['approval']);exit;
           if(isset($_GET['approval'])){
                $res = M('talk_topic')->data(array('approval' => $approval))->where('id in(' . $ids.')')->save();
            }

            if(isset($_GET['close'])){
                $res = M('talk_topic')->data(array('close' => $close))->where('id in(' . $ids.')')->save();
            }

            // $res = M('talk_topic')->data(array('status' => $status))->where('id in(' . $ids.')')->save();

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
        $status = isset($_GET['status']) ? $_GET['status'] : '1';
        $approval = isset($_GET['approval']) ? $_GET['approval'] : '1';
        $close = isset($_GET['close']) ? $_GET['close'] : '0';

        $prefix = C('DB_PREFIX');

        $order = "{$prefix}talk_replies.datetime asc";

        $where = array();
        if ($keyword <> '') {
            if ($field == 'name') {
                $where[] = "{$prefix}talk_topic.name LIKE '%$keyword%'";
            }
            if ($field == 'realname') {
                $where[] = "{$prefix}member.realname LIKE '%$keyword%'";
            }
            if ($field == 'reply') {
                $where[] = "{$prefix}talk_replies.reply LIKE '%$keyword%'";
            }
        }

        if(isset($_GET['status'])){
            $where[] = "{$prefix}talk_replies.status = '$status'";
        }
        if(isset($_GET['approval'])){
            $where[] = "{$prefix}talk_replies.approval = '$approval'";
        }
        if(isset($_GET['close'])){
            $where[] = "{$prefix}talk_replies.close = '$close'";
        } else {
            $where[] = "{$prefix}talk_replies.close = '0'";
        }
        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }
        $talks = M('talk_replies');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $talks->field("{$prefix}talk_replies.*,{$prefix}member.realname,{$prefix}talk_topic.name")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}talk_replies.uid")
            ->join("{$prefix}talk_topic ON {$prefix}talk_topic.id = {$prefix}talk_replies.tid")
            ->where($wherestring)
            ->count();

        $list = $talks->field("{$prefix}talk_replies.*,{$prefix}member.realname,{$prefix}talk_topic.name")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}talk_replies.uid")
            ->join("{$prefix}talk_topic ON {$prefix}talk_topic.id = {$prefix}talk_replies.tid")
            ->where($wherestring)
            ->limit($offset . ',' . $pagesize)
            ->select();


        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->display('reply');

    }
    public function rdel()
    {

        $ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : false;

        if (!$ids) {
            $this->error('请勾选删除任务！');
        }
        if (!is_array($ids)) {
            $this->error('参数错误！');
        }

        $ids = implode(',', $ids);

        $data['close'] = '1';
        // $map['id'] = array('in', implode(',', $ids));

        if (M('talk_replies')->data($data)->where('reply_id in(' . $ids.')')->save()) {

            addlog('删除任务ID：' . implode(',', $ids));
            $this->success('任务删除成功！');
        } else {
            $this->error('参数错误！');
        }
    }

    public function rstatus()
    {
        $ids = implode(',', I('post.ids'));

        $status = I('get.status');
        $approval = I('get.approval');

        if (!$ids) {
            $reply_id = isset($_GET['reply_id']) ? intval($_GET['reply_id']) : false;
            if (!$reply_id) {
                $this->error('参数错误！');
            }


            $topic = M('talk_replies')->where('reply_id=' . $reply_id)->find();


            if (!$topic) {
                $this->error('参数错误！');
            }

            if(isset($_GET['status'])){
                if ($status == 1) {
                   $res = M('talk_replies')->data(array('status' => 0))->where('reply_id=' . $reply_id)->save();
                }
                if ($status == 0 ) {
                    $res = M('talk_replies')->data(array('status' => 1))->where('reply_id=' . $reply_id)->save();
                }
            }

            if(isset($_GET['approval'])){
                if ($approval == 1) {
                   $res = M('talk_replies')->data(array('approval' => 0))->where('reply_id=' . $reply_id)->save();
                }
                if ($approval == 0 ) {
                    $res = M('talk_replies')->data(array('approval' => 1))->where('reply_id=' . $reply_id)->save();
                }
            }

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{

            if(isset($_GET['status'])){
                $res = M('talk_replies')->data(array('status' => $status))->where('reply_id in(' . $ids.')')->save();
            }

            if(isset($_GET['approval'])){
                $res = M('talk_replies')->data(array('approval' => $approval))->where('reply_id in(' . $ids.')')->save();
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
        $close = isset($_GET['close']) ? htmlentities($_GET['close']) : '0';
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
        $status = isset($_GET['status']) ? $_GET['status'] : '1';
        $approval = isset($_GET['approval']) ? $_GET['approval'] : '1';

        $prefix = C('DB_PREFIX');
        if ($order == 'asc') {
            $order = "{$prefix}talk_comments.datetime asc";
        } elseif (($order == 'desc')) {
            $order = "{$prefix}talk_comments.datetime desc";
        } else {
            $order = "{$prefix}talk_comments.datetime asc";
        }
        $where = array();
        if ($keyword <> '') {

            if ($field == 'realname') {
                $where[] = "{$prefix}member.realname LIKE '%$keyword%'";
            }
            if ($field == 'name') {
                $where[] = "{$prefix}talk_topic.name LIKE '%$keyword%'";
            }
            // if ($field == 'reply') {
            //     $where[] = "{$prefix}talk_replies.reply LIKE '%$keyword%'";
            // }
            if ($field == 'comment') {
                $where[] = "{$prefix}talk_comments.comment LIKE '%$keyword%'";
            }
            // if ($field == 'status') {
            //     $where[] = "{$prefix}talk_comments.status = '$keyword'";
            // }
        }
        if(isset($_GET['status'])){
            $where[] = "{$prefix}talk_comments.status = '$status'";
        }
        if(isset($_GET['approval'])){
            $where[] = "{$prefix}talk_comments.approval = '$approval'";
        }
   

        if(isset($_GET['close'])){
            $where[] = "{$prefix}talk_comments.close = '$close'";
        }else{
            $where[] = "{$prefix}talk_comments.close = '0'";
        }

        // $where[] = "{$prefix}talk_subject.closed = '0'";

        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }

        $talks = M('talk_comments');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $talks->field("{$prefix}talk_comments.*,{$prefix}member.realname,{$prefix}talk_topic.name")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}talk_comments.uid")
            ->join("{$prefix}talk_topic ON {$prefix}talk_topic.id = {$prefix}talk_comments.tid")
            // ->join("{$prefix}talk_topic_subject ON {$prefix}talk_topic_subject.tid = {$prefix}talk_comments.tid")
            // ->join("{$prefix}talk_subject ON {$prefix}talk_topic_subject.subject_id = {$prefix}talk_subject.subject_id")
            // ->join("{$prefix}talk_replies ON {$prefix}talk_replies.reply_id = {$prefix}talk_comments.rid")

            ->where($wherestring)
            ->count();

        $list = $talks->field("{$prefix}talk_comments.*,{$prefix}member.realname,{$prefix}talk_topic.name")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}talk_comments.uid")
            ->join("{$prefix}talk_topic ON {$prefix}talk_topic.id = {$prefix}talk_comments.tid")
            // ->join("{$prefix}talk_topic_subject ON {$prefix}talk_topic_subject.tid = {$prefix}talk_comments.tid")
            // ->join("{$prefix}talk_subject ON {$prefix}talk_subject.id = {$prefix}talk_comments.tid")
            // ->join("{$prefix}talk_replies ON {$prefix}talk_replies.reply_id = {$prefix}talk_comments.rid")
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
        $close = I('get.close');
        $approval = I('get.approval');

        if (!$ids) {

            $comment_id = isset($_GET['comment_id']) ? intval($_GET['comment_id']) : false;

            if (!$comment_id) {
                $this->error('参数错误！');
            }

            $topic = M('talk_comments')->where('comment_id=' . $comment_id)->find();
            if (!$topic) {

                $this->error('参数错误！');
            }

            if(isset($_GET['status'])){
                if ($status == 1) {
                   $res = M('talk_comments')->data(array('status' => 0))->where('comment_id=' . $comment_id)->save();
                }
                if ($status == 0 ) {
                    $res = M('talk_comments')->data(array('status' => 1))->where('comment_id=' . $comment_id)->save();
                }
            }
            if(isset($_GET['approval'])){
                if ($approval == 1) {
                   $res = M('talk_comments')->data(array('approval' => 0))->where('comment_id=' . $comment_id)->save();
                }
                if ($approval == 0 ) {
                    $res = M('talk_comments')->data(array('approval' => 1))->where('comment_id=' . $comment_id)->save();
                }
            }
            if(isset($_GET['close'])){
                if ($close == 1) {
                   $res = M('talk_comments')->data(array('close' => 0))->where('comment_id=' . $comment_id)->save();
                }
                if ($close == 0 ) {
                    $res = M('talk_comments')->data(array('close' => 1))->where('comment_id=' . $comment_id)->save();
                }
            }

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{

            if(isset($_GET['approval'])){
                   $res = M('talk_comments')->data(array('approval' => $approval))->where('comment_id in(' . $ids.')')->save();

            }
          if(isset($_GET['status'])){
                   $res = M('talk_comments')->data(array('status' => $status))->where('comment_id in(' . $ids.')')->save();

            }
            if(isset($_GET['close'])){
                $res = M('talk_comments')->data(array('close' => $close))->where('comment_id in(' . $ids.')')->save();
            }

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->success('更新状态成功！');
            }

        }
    }

    public function subject(){

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $approval = isset($_GET['approval']) ? $_GET['approval'] : '0';
        $close = isset($_GET['close']) ? $_GET['close'] : '0';
        $status = isset($_GET['status']) ? $_GET['status'] : '1';
        $type = isset($_GET['type']) ? $_GET['type'] : '0';


        $prefix = C('DB_PREFIX');

        $order = "{$prefix}talk_subject.date asc";
        $where = array();
        if ($keyword <> '') {
            if ($field == 'name') {
                $where[] = "{$prefix}talk_subject.title LIKE '%$keyword%'";
            }
            if ($field == 'realname') {
                $where[] = "{$prefix}membere.realname = '%$keyword%'";
            }
        }
        if(isset($_GET['status'])){
            $where[] = "{$prefix}talk_subject.status = '$status'";
        }
        if(isset($_GET['approval'])){
            $where[] = "{$prefix}talk_subject.approval = '$approval'";
        }

       if(isset($_GET['type'])){
            $where[] = "{$prefix}talk_subject.type = '$type'";
        }

        if(isset($_GET['close'])){
            $where[] = "{$prefix}talk_subject.close = '$close'";
        }else{
            $where[] = "{$prefix}talk_subject.close = '0'";
        }

        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }

        $talks = M('talk_subject');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $talks->field("{$prefix}talk_subject.*,{$prefix}member.realname")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}talk_subject.uid")
            ->where($wherestring)
            ->count();

        $list = $talks->field("{$prefix}talk_subject.*,{$prefix}member.realname")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}talk_subject.uid")
            ->where($wherestring)
            ->limit($offset . ',' . $pagesize)
            ->select();
        foreach ($list as $key => $value) {
            if(empty($value['image'])){
                $list[$key]['image'] = '5ab074d8a63e8.png';
            }
        }


        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $userid = $this->USER['uid'];
        $this->assign('userid', $userid);
        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->display('subject');

    }

    public function sadd()
    {

        $this->display('subjectform');
    }

    public function sdel()
    {

        $ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : false;
        if (!$ids) {
            $this->error('请勾选删除任务！');
        }
        if (!is_array($ids)) {
            $this->error('参数错误！');
        }

        $data['close'] = '1';
        $map['subject_id'] = array('in', implode(',', $ids));

        if (M('talk_subject')->data($data)->where($map)->save()) {

            addlog('删除任务ID：' . implode(',', $ids));
            $this->success('任务删除成功！');
        } else {
            $this->error('参数错误！');
        }
    }

    public function sedit($id = 0)
    {
        $prefix = C('DB_PREFIX');

        $subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : false;
        $topics = M('talk_subject');
        $topic = $topics->where("subject_id='$subject_id'")->find();
        if ($this->USER['uid']!=1) {
            $this->error('您暂无此权限！');
        }

        $this->assign('topic', $topic);
        $this->display('subjectform');
    }

    public function supdate()
    {

        $subject_id = isset($_POST['subject_id']) ? intval($_POST['subject_id']) : false;
        $data['subject_id'] = I('post.subject_id', '', 'intval');
        $data['title'] = I('post.title', '', 'strip_tags');
        $data['description'] = htmlentities($_POST['description']);
        $data['date'] = time();
        $data['info'] = htmlentities($_POST['info']);
        $data['uid'] = $this->USER['uid'];
        $title = I('post.title', '', 'strip_tags');

        $status = I('post.status', '', 'intval');

        if($status == 1){
            $data['status'] = $status;
        } else {
            $data['status'] = 0;
        }

        $approval = I('post.approval', '', 'intval');

        if($approval == 1){
            $data['approval'] = $approval;
        } else {
            $data['approval'] = 0;
        }

        // $data['image'] = '/Public/themes/images/'.$data['image'];

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
            if(!$info) {// 上传错误提示错误信息
                // $error = $upload->getError();
                //
                //$this->error($error);
            }else{// 上传成功
                $subject = M('talk_subject')->where('subject_id=' . $subject_id)->find();

                foreach ($info as $item) {
                    if($item['key'] == 'image'){
                        if($subject['image']){
                            unlink($upload->rootPath.$item['savepath'].$subject['image']);
                        }
                        $data['image'] = $item['savename'];
                    }

                }

            }
        }
        if (empty($title)) {
            $this->error('话题主题名称不能为空！');
        }

        if ($subject_id) {
            M('talk_subject')->data($data)->where("subject_id='{$subject_id}'")->save();
            addlog('编辑任务，ID：' . $subject_id);
        } else {
            M('talk_subject')->data($data)->add();
            addlog('新增任务，名称：' . $data['title']);
        }

        $this->success('操作成功！',U('subject'));
    }

    public function sstatus()
    {
        $ids = implode(',', I('post.ids'));
        $status = I('get.status');
        $approval = I('get.approval');


        if (!$ids) {
            $subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : false;
            if (!$subject_id) {
                $this->error('参数错误！');
            }

            $topic = M('talk_subject')->where('subject_id=' . $subject_id)->find();
            if (!$topic) {
                $this->error('参数错误！');
            }

            if(isset($_GET['approval'])){
                if ($approval == 1) {
                   $res = M('talk_subject')->data(array('approval' => 0))->where('subject_id=' . $subject_id)->save();

                }

                if ($approval == 0 ) {
                    $res = M('talk_subject')->data(array('approval' => 1))->where('subject_id=' . $subject_id)->save();
                }
            }

            if(isset($_GET['status'])){
                if ($status == 1) {
                   $res = M('talk_subject')->data(array('status' => 0))->where('subject_id=' . $subject_id)->save();
                }
                if ($status == 0 ) {
                    $res = M('talk_subject')->data(array('status' => 1))->where('subject_id=' . $subject_id)->save();
                }
            }

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{
            // $res = M('talk_subject')->data(array('status' => $status))->where('subject_id in(' . $ids.')')->save();

            if(isset($_GET['status'])){
                $res = M('talk_subject')->data(array('status' => $status))->where('subject_id in(' . $ids.')')->save();
            }

            if(isset($_GET['approval'])){
                $res = M('talk_subject')->data(array('approval' => $approval))->where('subject_id in(' . $ids.')')->save();
            }

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->success('更新状态成功！');
            }

        }
    }


    public function activity(){

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $approval = isset($_GET['approval']) ? $_GET['approval'] : '0';
        $close = isset($_GET['close']) ? $_GET['close'] : '0';
        $status = isset($_GET['status']) ? $_GET['status'] : '1';


        $prefix = C('DB_PREFIX');

        $order = "{$prefix}talk_activity.date asc";
        $where = array();
        if ($keyword <> '') {
            if ($field == 'name') {
                $where[] = "{$prefix}talk_activity.title LIKE '%$keyword%'";
            }
            if ($field == 'realname') {
                $where[] = "{$prefix}membere.realname = '%$keyword%'";
            }
        }
        if(isset($_GET['status'])){
            $where[] = "{$prefix}talk_activity.status = '$status'";
        }

        if(isset($_GET['approval'])){
            $where[] = "{$prefix}talk_activity.approval = '$approval'";
        }



        if(isset($_GET['close'])){
            $where[] = "{$prefix}talk_activity.close = '$close'";
        }else{
            $where[] = "{$prefix}talk_activity.close = '0'";
        }

        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }

        $talks = M('talk_activity');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $talks->field("{$prefix}talk_activity.*,{$prefix}member.realname")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}talk_activity.uid")
            ->where($wherestring)
            ->count();

        $list = $talks->field("{$prefix}talk_activity.*,{$prefix}member.realname")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}talk_activity.uid")
            ->where($wherestring)
            ->limit($offset . ',' . $pagesize)
            ->select();
        foreach ($list as $key => $value) {
            if(empty($value['image'])){
                $list[$key]['image'] = '5ab074d8a63e8.png';
            }
        }


        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
      $userid = $this->USER['uid'];
        $this->assign('userid', $userid);

        $this->assign('list', $list);
        $this->assign('page', $page);



        $this->display();
    }
    public function aadd()
    {
        $subject = M('talk_subject')->field("*")
                    ->where("status=1 AND approval=1")->select(); 
        $this->assign('subject', $subject);
        $this->display('activityform');
    }

    public function adel()
    {

        $ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : false;
        if (!$ids) {
            $this->error('请勾选删除任务！');
        }
        if (!is_array($ids)) {
            $this->error('参数错误！');
        }

        $data['close'] = '1';
        $map['id'] = array('in', implode(',', $ids));

        if (M('talk_activity')->data($data)->where($map)->save()) {

            addlog('删除任务ID：' . implode(',', $ids));
            $this->success('任务删除成功！');
        } else {
            $this->error('参数错误！');
        }
    }

    public function aedit($id = 0)
    {
        $prefix = C('DB_PREFIX');

        $id = isset($_GET['id']) ? intval($_GET['id']) : false;

        $activity =  M('talk_activity')->where("id='$id'")->find();
        if ($this->USER['uid']!=1) {
            $this->error('您暂无此权限！');
        }
        
        $activity_subject = M('talk_activity_subject')->field("*")
                    ->where("activity_id = $id")->select(); 

        $subject = M('talk_subject')->field("{$prefix}talk_subject.*")
                    // ->join("{$prefix}talk_subject.subject_id = {$prefix}talk_activity_subject.subject_id","left")
                    ->where("{$prefix}talk_subject.status=1 AND {$prefix}talk_subject.approval=1")
                    ->select(); 
                   
        $this->assign('activity_subject', $activity_subject);
        $this->assign('subject', $subject);
        $this->assign('activity', $activity);
        $this->display('activityform');
    }

    public function aupdate()
    {

        $id = isset($_POST['id']) ? intval($_POST['id']) : false;
        $data['id'] = I('post.id', '', 'intval');
        $data['title'] = I('post.title', '', 'strip_tags');
        $data['description'] = htmlentities($_POST['description']);
        $data['date'] = time();
        $data['uid'] = $this->USER['uid'];
        $title = I('post.title', '', 'strip_tags');

        $status = I('post.status', '', 'intval');

        if($status == 1){
            $data['status'] = $status;
        } else {
            $data['status'] = 0;
        }

        $approval = I('post.approval', '', 'intval');

        if($approval == 1){
            $data['approval'] = $approval;
        } else {
            $data['approval'] = 0;
        }

        // $data['image'] = '/Public/themes/images/'.$data['image'];

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
            if(!$info) {// 上传错误提示错误信息
                $data['image'] = I('post.image1', '', 'strip_tags');
            }else{// 上传成功
                $activity = M('talk_activity')->where('id=' . $id)->find();

                foreach ($info as $item) {
                    if($item['key'] == 'image'){
                        if($activity['image']){
                            unlink($upload->rootPath.$item['savepath'].$activity['image']);
                        }
                        $data['image'] = $item['savename'];
                    }

                }

            }
        }
        if (empty($title)) {
            $this->error('活动名称不能为空！');
        }





// var_dump($data['image']);exit();
        if ($id) {
            M('talk_activity')->data($data)->where("id='{$id}'")->save();
            M('talk_activity_subject')->where("activity_id = $id")->delete();
            foreach ($_POST['subject'] as $value) {
                $activity = array();
                $activity['activity_id'] = $id;
                $activity['subject_id'] = $value;
                M('talk_activity_subject')->data($activity)->add();
            }

            addlog('编辑任务，ID：' . $id);
        } else {
            $activity_id = M('talk_activity')->data($data)->add();

            foreach ($_POST['subject'] as  $value) {
                $activity = array();
                $activity['activity_id'] = $activity_id;
                $activity['subject_id'] = $value;
                M('talk_activity_subject')->data($activity)->add();
            }

            addlog('新增活动，名称：' . $data['title']);
        }

        $this->success('操作成功！',U('activity'));
    }

    public function astatus()
    {
        $ids = implode(',', I('post.ids'));
        $status = I('get.status');
        $approval = I('get.approval');


        if (!$ids) {
            $id = isset($_GET['id']) ? intval($_GET['id']) : false;
            if (!$id) {
                $this->error('参数错误！');
            }

            $activity = M('talk_activity')->where('id=' . $id)->find();

            if (!$activity) {
                $this->error('参数错误！');
            }

            if(isset($_GET['approval'])){
                if ($approval == 1) {
                   $res = M('talk_activity')->data(array('approval' => 0))->where('id=' . $id)->save();

                }

                if ($approval == 0 ) {
                    $res = M('talk_activity')->data(array('approval' => 1))->where('id=' . $id)->save();
                }
            }

            if(isset($_GET['status'])){
                if ($status == 1) {
                   $res = M('talk_activity')->data(array('status' => 0))->where('id=' . $id)->save();
                }
                if ($status == 0 ) {
                    $res = M('talk_activity')->data(array('status' => 1))->where('id=' . $id)->save();
                }
            }

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{
            // $res = M('talk_subject')->data(array('status' => $status))->where('subject_id in(' . $ids.')')->save();

            if(isset($_GET['status'])){
                $res = M('talk_activity')->data(array('status' => $status))->where('id in(' . $ids.')')->save();
            }

            if(isset($_GET['approval'])){
                $res = M('talk_activity')->data(array('approval' => $approval))->where('id in(' . $ids.')')->save();
            }

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->success('更新状态成功！');
            }

        }
    }

}