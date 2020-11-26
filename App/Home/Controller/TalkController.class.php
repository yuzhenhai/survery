<?php
namespace Home\Controller;
use Think\Controller;
use Think\Upload;
class TalkController extends ComController {

    public function index(){
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : false;
        $views = isset($_GET['views']) ? intval($_GET['views']) : false;


		$prefix = C('DB_PREFIX');
        $order = "{$prefix}talk_topic.stick DESC,{$prefix}talk_topic.date DESC";
        $where = array();
        $where[] = "{$prefix}talk_topic.close = '0'";
        $where[] = "{$prefix}talk_topic.status = '1'";
        $where[] = "{$prefix}talk_topic.approval = '1'";
        if($subject_id){
            $where[] = "{$prefix}talk_topic_subject.topic_subject_id = ".$subject_id;
        }
        if($views){
            $order = "{$prefix}talk_topic.views DESC";
        }

		$wherestring = '';
		if(count($where)){
			$wherestring = implode(' AND ',$where);
		}

		$talks = M('talk_topic');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量


        if($subject_id){
            $count =  M("talk_topic_subject")->field("{$prefix}talk_topic.*,{$prefix}member.realname,{$prefix}member.head,(SELECT count({$prefix}talk_comments.tid) FROM {$prefix}talk_comments WHERE {$prefix}talk_comments.tid = {$prefix}talk_topic.id) as count")
                        ->order($order)
                        ->join("{$prefix}talk_topic ON {$prefix}talk_topic.id = {$prefix}talk_topic_subject.tid")
                        ->join("{$prefix}member ON {$prefix}talk_topic.uid = {$prefix}member.uid")
                        ->where($wherestring)
                        ->count();

            $list = M("talk_topic_subject")->field("{$prefix}talk_topic.*,{$prefix}member.realname,{$prefix}member.head,(SELECT count({$prefix}talk_comments.tid) FROM {$prefix}talk_comments WHERE {$prefix}talk_comments.tid = {$prefix}talk_topic.id) as count")
                        ->order($order)
                        ->join("{$prefix}talk_topic ON {$prefix}talk_topic.id = {$prefix}talk_topic_subject.tid")
                        ->join("{$prefix}member ON {$prefix}talk_topic.uid = {$prefix}member.uid")
                        ->where($wherestring)
                        ->limit($offset . ',' . $pagesize)
                        ->select();
        }else{
            $count = $talks->field("{$prefix}talk_topic.*,{$prefix}member.realname,{$prefix}member.head,(SELECT count({$prefix}talk_comments.tid) FROM {$prefix}talk_comments WHERE {$prefix}talk_comments.tid = {$prefix}talk_topic.id) as count")
                    ->order($order)
                    ->join("{$prefix}member ON {$prefix}talk_topic.uid = {$prefix}member.uid")
                    ->where($wherestring)
                    ->count();

            $list = $talks->field("{$prefix}talk_topic.*,{$prefix}member.realname,{$prefix}member.head,(SELECT count({$prefix}talk_comments.tid) FROM {$prefix}talk_comments WHERE {$prefix}talk_comments.tid = {$prefix}talk_topic.id) as count")
                ->order($order)
                ->join("{$prefix}member ON {$prefix}talk_topic.uid = {$prefix}member.uid")
                ->where($wherestring)
                ->limit($offset . ',' . $pagesize)
                ->select();
        }

        foreach ($list as $i => $item) {
            $subjects = M("talk_topic_subject")->field("{$prefix}talk_subject.*")
                        ->join("{$prefix}talk_subject ON {$prefix}talk_subject.subject_id = {$prefix}talk_topic_subject.topic_subject_id")
                        ->where("{$prefix}talk_subject.status=1 AND {$prefix}talk_subject.approval=1 AND {$prefix}talk_subject.close=0 AND {$prefix}talk_topic_subject.tid = ".$item['id'])
                        ->select();
            $list[$i]['subject'] = $subjects;
        }

        $subject = M('talk_subject')->field("{$prefix}talk_subject.*,(SELECT count({$prefix}talk_topic_subject.topic_subject_id) FROM {$prefix}talk_topic_subject WHERE {$prefix}talk_topic_subject.topic_subject_id = {$prefix}talk_subject.subject_id) as total")
                    ->where("{$prefix}talk_subject.status=1 AND {$prefix}talk_subject.approval=1 AND {$prefix}talk_subject.close=0")
                    ->limit('0,10')
                    ->select();

        foreach ($list as $key => $item) {
            $list[$key]['date'] = dgmdate(strtotime($item['date']));

            $list[$key]['description'] = mb_substr(strip_tags(htmlspecialchars_decode($item['description'])),0,C('post_talk_number'),'utf-8');
        }

        $activity =  M('talk_activity')->where("{$prefix}talk_activity.status='1' AND {$prefix}talk_activity.approval = '1'")->find();


		$page = new \Think\Page($count, $pagesize);
        $page = $page->show();

        $hots = $this->hot();
        $users = $this->user();

        $this->assign('activity', $activity);

        $this->assign('views', $views);
        $this->assign('users', $users);
        $this->assign('hots', $hots);
        $this->assign('subject_id', $subject_id);
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('subject', $subject);

		$this->display('index');
    }
    public function detail(){
        $uid = $this->USER['uid'];
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $id = isset($_GET['id']) ? intval($_GET['id']) : false;
        $prefix = C('DB_PREFIX');

        $sub = M('talk_topic')->field("{$prefix}talk_topic.*")
                              ->where("{$prefix}talk_topic.id=$id AND {$prefix}talk_topic.status=1 AND {$prefix}talk_topic.approval=1 AND {$prefix}talk_topic.close=0")
                              ->find();

        $da['views'] = $sub['views']+1;
        $views = $sub['views']+1;

        $model = D("talk_topic");
        $model->views = $views;
        // $model->Nation="n001";
        $to = $model->where("id=$id")->save();

        // $to = M('talk_topic')->data($da)->where("id=$id")->save();
        $subject = M('talk_topic')->field("{$prefix}talk_topic.*,{$prefix}member.realname,{$prefix}member.head,(SELECT count({$prefix}talk_comments.tid) FROM {$prefix}talk_comments WHERE {$prefix}talk_comments.tid = {$prefix}talk_topic.id) as count")
            ->join("{$prefix}member ON {$prefix}talk_topic.uid = {$prefix}member.uid")

            ->where("{$prefix}talk_topic.id=$id AND {$prefix}talk_topic.status=1 AND {$prefix}talk_topic.approval=1 AND {$prefix}talk_topic.close=0")->find();

         $talk_subject = M('talk_topic_subject')->field("*")
                     ->join("{$prefix}talk_subject ON {$prefix}talk_subject.subject_id = {$prefix}talk_topic_subject.topic_subject_id")
                    ->where("{$prefix}talk_topic_subject.tid=$subject[id] AND {$prefix}talk_subject.status=1 AND {$prefix}talk_subject.approval=1 AND {$prefix}talk_subject.close=0")->select();
        $subject['date'] = dgmdate(strtotime($subject['date']));
        $subject['description'] = htmlspecialchars_decode($subject['description']);

        $comments = M('talk_comments')->field("{$prefix}talk_comments.*,{$prefix}member.realname,{$prefix}member.head,(SELECT count({$prefix}talk_comments_like.id) FROM {$prefix}talk_comments_like WHERE {$prefix}talk_comments_like.tid = {$prefix}talk_comments.tid AND {$prefix}talk_comments_like.cid = {$prefix}talk_comments.comment_id) as count")
            ->join("{$prefix}member ON {$prefix}talk_comments.uid = {$prefix}member.uid")
            ->where("{$prefix}talk_comments.tid=$id")->select();

        foreach ($comments as $i=>$value) {
            if(!empty($value['images'])){

                if(strpos($value['images'],'|')){

                    $images = explode('|',$value['images']);
                }else{
                    $images = array('0'=>$value['images']);
                }


                $comments[$i]['images'] = $images;
            }
            $reply = M('talk_replies')->field("{$prefix}talk_replies.*,{$prefix}member.realname,{$prefix}member.head")
                ->join("{$prefix}member ON {$prefix}talk_replies.uid = {$prefix}member.uid")
                ->where("{$prefix}talk_replies.tid=".$value['tid']." AND {$prefix}talk_replies.cid=".$value['comment_id']." AND {$prefix}talk_replies.child=0")->select();
            $reply_count = M('talk_replies')->field("{$prefix}talk_replies.*,{$prefix}member.realname,{$prefix}member.head")
                ->join("{$prefix}member ON {$prefix}talk_replies.uid = {$prefix}member.uid")
                ->where("{$prefix}talk_replies.tid=".$value['tid']." AND {$prefix}talk_replies.cid=".$value['comment_id']." AND {$prefix}talk_replies.child=0")->count();

                foreach ($reply as $key => $r) {
                    $childs = M('talk_replies')->field("{$prefix}talk_replies.*,{$prefix}member.realname,{$prefix}member.head,m.realname as name, m.head as heads")
                        ->join("{$prefix}member ON {$prefix}talk_replies.uid = {$prefix}member.uid")
                        ->join("{$prefix}member m ON {$prefix}talk_replies.ruid = m.uid")
                        ->where("{$prefix}talk_replies.rid=".$r['reply_id']." AND {$prefix}talk_replies.tid=".$r['tid']." AND {$prefix}talk_replies.cid=".$value['comment_id']." AND {$prefix}talk_replies.child=1")->select();
                        $reply[$key]['childs'] = $childs;
                }


            $like = M('talk_comments_like')->field("{$prefix}talk_comments_like.*")
                    ->where("{$prefix}talk_comments_like.tid = ".$value['tid']." AND {$prefix}talk_comments_like.uid = ".$uid." AND {$prefix}talk_comments_like.cid =" .$value['comment_id'])->select();
            $comments[$i]['like'] = $like;
            $comments[$i]['reply'] = $reply;
            $comments[$i]['reply_count'] = $reply_count;

        }

        $hots = $this->hot();
        $users = $this->user();

        $this->assign('users', $users);
        $this->assign('hots', $hots);
        $this->assign('user', $this->USER);

        $this->assign('uid', $uid);
        $this->assign('talk_subject', $talk_subject);
        $this->assign('comments', $comments);
        $this->assign('subject', $subject);
        $this->display('detail');

    }
    public function comment(){

        $score = isset($_POST['score']) ? intval($_POST['score']) : 0;
        $tid = isset($_POST['tid']) ? intval($_POST['tid']) : false;
        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : false;
        $comment = isset($_POST['comment']) ? trim($_POST['comment']) : false;
        if(!$comment){
            $this->error('回复不能为空！');
        }

        if(!$uid&&!$tid){
            $this->error('参数错误！');
        }

        $data['tid'] = isset($_POST['tid']) ? intval($_POST['tid']) : '';
        $data['uid'] = isset($_POST['uid']) ? intval($_POST['uid']) : '';
        $data['comment'] = isset($_POST['comment']) ? trim($_POST['comment']) : '';
        $data['images'] = isset($_POST['images']) ? trim($_POST['images']) : '';
        $data['approval'] = 1;
        $data['status'] = 1;
        $data['datetime'] = time();
        $comment_id = M('talk_comments')->data($data)->add();

        if($score>0){
            $fatie = array();
            $fatie['tid'] = $tid;
            $fatie['uid'] = $uid;
            $fatie['score'] = $score;
            $fatie['relevance_name'] = 'talk_comments';
            $fatie['relevance_module'] = 'comment_id';
            $fatie['relevance_value'] = $comment_id;
            $fatie['info'] = '回复主题,回复ID:'.$comment_id;
            $fatie['type'] = 2;

            $fatie['date'] = time();
            M('member_score')->data($fatie)->add();
            $users = array();
            $integral = $this->USER['integral'];
            $users['integral'] = $score + $integral;
            M('member')->data($users)->where("uid=$uid")->save();
        }

        $this->success('操作成功！');

    }
    public function reply(){
        $score = isset($_POST['credit_comment']) ? intval($_POST['credit_comment']) : 0;
        $tid = isset($_POST['tid']) ? intval($_POST['tid']) : false;
        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : false;
        $reply = isset($_POST['reply']) ? trim($_POST['reply']) : false;
        $rid = isset($_POST['rid']) ? intval($_POST['rid']) : false;

        if($rid){
            $data['ruid'] = isset($_POST['ruid']) ? intval($_POST['ruid']) : 0;
            $data['rid'] = isset($_POST['rid']) ? intval($_POST['rid']) : '';
            $data['child'] = '1';

        }

        if(!$reply){
            $this->error('回复不能为空！');
        }

        if(!$uid&&!$tid){
            $this->error('参数错误！');
        }

        $data['tid'] = isset($_POST['tid']) ? intval($_POST['tid']) : '';
        $data['uid'] = isset($_POST['uid']) ? intval($_POST['uid']) : '';
        $data['reply'] = isset($_POST['reply']) ? trim($_POST['reply']) : '';
        $data['cid'] = isset($_POST['cid']) ? intval($_POST['cid']) : '';
        $data['approval'] = 1;
        $data['status'] = 1;
        $data['datetime'] = time();
        $reply_id = M('talk_replies')->data($data)->add();

        if($score>0){
            $fatie = array();
            $fatie['tid'] = $tid;
            $fatie['uid'] = $uid;
            $fatie['score'] = $score;
            $fatie['relevance_name'] = 'talk_replies';
            $fatie['relevance_module'] = 'reply_id';
            $fatie['relevance_value'] = $reply_id;
            $fatie['info'] = '回复评论主题,回复ID:'.$reply_id;
            $fatie['type'] = 2;

            $fatie['date'] = time();
            M('member_score')->data($fatie)->add();
            $users = array();
            $integral = $this->USER['integral'];
            $users['integral'] = $score + $integral;
            M('member')->data($users)->where("uid=$uid")->save();
        }

        $this->success('操作成功！');

    }

    public function zan(){
        $prefix = C('DB_PREFIX');
        $uid = $this->USER['uid'];
        $cid = isset($_GET['comment_id']) ? intval($_GET['comment_id']) : false;
        $tid = isset($_GET['tid']) ? intval($_GET['tid']) : false;
        $zan = M('talk_comments_like')->field("*")
                    ->where("uid=$uid AND tid=$tid AND cid = $cid")->find();
        if($zan){
            M('talk_comments_like')->where("id=$zan[id]")->delete();
        }else{
            $data['tid'] = isset($_GET['tid']) ? intval($_GET['tid']) : '';
            $data['uid'] = $this->USER['uid'];
            $data['cid'] = isset($_GET['comment_id']) ? intval($_GET['comment_id']) : '';
            $data['datetime'] = time();
            M('talk_comments_like')->data($data)->add();
        }
        $this->success('操作成功！');

    }
    public function add(){
        $prefix = C('DB_PREFIX');
        $activity_id = isset($_GET['activity_id']) ? intval($_GET['activity_id']) : false;
        $prefix = C('DB_PREFIX');
        $uid = $this->USER['uid'];
        $subject = M('talk_subject')->field("*")
                    ->where("status=1 AND approval=1")->select();
        if($activity_id) {
            $activity =  M('talk_activity')->where("id='$activity_id'")->find();
            $this->assign('activity', $activity);
        }

        $hots = $this->hot();
        $users = $this->user();
        if($activity_id){
            $activity_subject = M('talk_activity_subject')->field("{$prefix}talk_activity_subject.*,{$prefix}talk_subject.title")
                        ->join("{$prefix}talk_subject ON {$prefix}talk_subject.subject_id = {$prefix}talk_activity_subject.subject_id")
                        ->where("{$prefix}talk_activity_subject.activity_id = $activity_id")
                        ->select(); 
        }

        $this->assign('activity_id', $activity_id);
        $this->assign('activity_subject', $activity_subject);

        $this->assign('users', $users);
        $this->assign('hots', $hots);

        $this->assign('uid', $uid);
        $this->assign('subject', $subject);
        $this->display('form');
    }

    public function update(){

        $prefix = C('DB_PREFIX');
        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : false;
        $activity = isset($_POST['activity']) ? $_POST['activity'] : false;
        $subject = isset($_POST['subject']) ? $_POST['subject'] : false;


        // $_POST['activity'],$_POST['subject']
        $data['uid'] = isset($_POST['uid']) ? intval($_POST['uid']) : '';
        $data['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
        // $data['abstract'] = isset($_POST['abstract']) ? htmlentities($_POST['abstract']) : '';
        $data['description'] = isset($_POST['description']) ? htmlentities($_POST['description']) : '';
        $data['image'] = isset($_POST['image']) ? trim($_POST['image']) : '';
        $data['approval'] = 0;
        $data['status'] = 0;
        $data['date'] = date('Y-m-d H:i:s',time());

        $title = isset($_POST['title']) ? trim($_POST['title']) : false;


        if (!empty($_FILES)) {

            $mimes = array(
                'image/jpg',
                'image/png',
                'image/gif',
                'image/jpeg'
            );
            $exts = array(
                'jpg',
                'png',
                'gif'
            );
            $upload = new Upload(array(
                'mimes' => $mimes,
                'exts' => $exts,
                'rootPath' => './Public/',
                'savePath' => 'upload/images/',
                'subName'  =>  '',
            ));
            $info = $upload->upload($_FILES);
            if($info) {
                foreach ($info as $item) {
                    if($item['key'] == 'image'){
                        unlink($data['image']);
                        $data['image'] = $item['savename'];
                    }
                }
            }
        }

        // if(!$name){
        //     $this->error("名称不能为空！");
        // }
            // if($activity){
            //     $subjects = array_merge($activity,$subject);
            // }else{
            //     $subjects = $subject;
            // }

            // $subjects = array_unique($subjects);
            // foreach ($subjects as  $value) {
            //     $topic = array();
            //     $topic['tid'] = 123;
            //     $topic['topic_subject_id'] = $value;
            //     M('talk_topic_subject')->data($topic)->add();
            // }
            // exit;
        if($uid){
            $tid = M('talk_topic')->data($data)->add();
            $images = isset($_POST['images']) ? $_POST['images'] : false;
            if($images){
                $topic_images = explode('|', $images);
                foreach ($topic_images as $img) {
                    $image = array();
                    $image['tid'] = $tid;
                    $image['image'] = $img;
                    M('talk_topic_image')->data($image)->add();
                }
            }
            if($activity){
                $subjects = array_merge($activity,$subject);
            }else{
                $subjects = $subject;
            }
            $subjects = array_unique($subjects);
            foreach ($subjects as  $value) {
                $topic = array();
                $topic['tid'] = $tid;
                $topic['topic_subject_id'] = $value;
                M('talk_topic_subject')->data($topic)->add();
            }

            $count_talk = M("talk_topic")->field("count(id) as posts")->where("uid = $uid")->find();


            $score = isset($_POST['score']) ? trim($_POST['score']) : 0;
            if($score>0){
                $fatie = array();
                $fatie['tid'] = $tid;
                $fatie['uid'] = $uid;
                $fatie['score'] = isset($_POST['score']) ? trim($_POST['score']) : '';
                $fatie['relevance_name'] = 'talk_topic';
                $fatie['relevance_module'] = 'id';
                $fatie['relevance_value'] = $tid;
                $fatie['info'] = '发布主题,主题ID:'.$tid;
                $fatie['type'] = 1;
                $fatie['date'] = time();
                M('member_score')->data($fatie)->add();
                $users = array();
                $integral = $this->USER['integral'];
                $users['integral'] = $score + $integral;
                $users['last_post'] = time();
                $users['posts'] = $count_talk;

                M('member')->data($users)->where("uid=$uid")->save();
            }
            $sub = array();
            if($title){
                $sub['title'] = isset($_POST['title']) ? trim($_POST['title']) : '';
                $sub['uid'] = $uid;
                $sub['status'] = 0;
                $sub['approval'] = 0;
                $sub['type'] = 1;
                $sub['date'] = time();

                $subjectid = M('talk_subject')->data($sub)->add();

                $top = array();
                $top['topic_subject_id'] = $subjectid;
                $top['tid'] = $tid;


                M('talk_topic_subject')->data($top)->add();

            }

            $last_post = time();
            M('Member')->data(array('last_post' => $last_post))->where("uid=$uid")->save();

            $this->success('操作成功！',U('index'));
        }else{
            $this->error('参数错误');
        }

    }

    public function more(){
        $prefix = C('DB_PREFIX');

        $subject = M('talk_subject')->field("{$prefix}talk_subject.*,(SELECT count({$prefix}talk_topic_subject.topic_subject_id) FROM {$prefix}talk_topic_subject WHERE {$prefix}talk_topic_subject.topic_subject_id = {$prefix}talk_subject.subject_id) as total")
                ->where("{$prefix}talk_subject.status=1 AND {$prefix}talk_subject.approval=1 AND {$prefix}talk_subject.close=0 AND {$prefix}talk_subject.type=0")
                    ->select();
        $sub = M('talk_subject')->field("{$prefix}talk_subject.*,(SELECT count({$prefix}talk_topic_subject.topic_subject_id) FROM {$prefix}talk_topic_subject WHERE {$prefix}talk_topic_subject.topic_subject_id = {$prefix}talk_subject.subject_id) as total")
                ->where("{$prefix}talk_subject.status=1 AND {$prefix}talk_subject.approval=1 AND {$prefix}talk_subject.close=0 AND {$prefix}talk_subject.type=1")
                    ->select();
        $hots = $this->hot();
        $users = $this->user();

        $this->assign('users', $users);
        $this->assign('hots', $hots);

        $this->assign('subject', $subject);
        $this->assign('sub', $sub);

        $this->display('more');

    }


    public function hot(){

        $prefix = C('DB_PREFIX');
        $where = array();
        $where[] = "{$prefix}talk_topic.status = '1'";
        $where[] = "{$prefix}talk_topic.approval = '1'";
        $where[] = "{$prefix}talk_topic.ess = '1'";

        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }
        $talks = M('talk_topic');

        $hots = $talks->field("{$prefix}talk_topic.*")
            ->where($wherestring)
            ->limit('0,5')
            ->select();

        return $hots;
    }
    public function user(){

        $prefix = C('DB_PREFIX');
        $where = array();
        $where[] = "{$prefix}member_celebrity.status = '1'";
        $where[] = "date({$prefix}member_celebrity.begin) <=DATE(NOW())";
        $where[] = "date({$prefix}member_celebrity.end) >=DATE(NOW())";

        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }
        $member = M('member_celebrity');

        $list = $member->field("{$prefix}member_celebrity.*,{$prefix}member.realname,{$prefix}member.head,(SELECT count({$prefix}talk_topic.id) FROM {$prefix}talk_topic WHERE {$prefix}talk_topic.uid = {$prefix}member_celebrity.uid) as count")
                ->join("{$prefix}member ON {$prefix}member_celebrity.uid = {$prefix}member.uid")
                ->where($wherestring)
                ->limit('0,5')
                ->select();

        return $list;
    }

    public function updateimage(){
         if (!empty($_FILES)) {

                $mimes = array(
                    'image/jpg',
                    'image/png',
                    'image/gif',
                    'image/jpeg'
                );
                $exts = array(
                    'jpg',
                    'png',
                    'gif'
                );
                $upload = new Upload(array(
                    'mimes' => $mimes,
                    'exts' => $exts,
                    'rootPath' => './Public/',
                    'savePath' => 'upload/images/',
                    'subName'  =>  '',
                ));
                $info = $upload->upload($_FILES);
                if($info) {
                    foreach ($info as $item) {

                        // unlink($data['image']);
                        // $data['image'] = $item['savename'];
                        $data = array(
                            "errno"=> 0,
                            // data 是一个数组，返回若干图片的线上地址
                            "data"=> array(
                                "http://localhost/survey/Public/".$item['savepath'].$item['savename']
                            )
                        );

                    }
                }
            }

            echo (json_encode($data));
        // var_dump($_FILES);exit;
    }

    public function activity(){
        $activity_id = isset($_GET['activity_id'])?intval($_GET['activity_id']) : false;
        $activity =  M('talk_activity')->where("id='$activity_id' AND status='1' AND approval = '1'")->find();

        $this->assign('activity', $activity);

        $this->display();
 
    }
}