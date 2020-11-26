<?php
namespace Api\Controller;
use Think\Exception;
class TalkController extends ComController
{

	public function subject(){
		//所有的话题（tags)
		$this->filterLogin();

        $subject = M('talk_subject')->where(array('status' => '1','approval'=>'1','close'=>'0'))->select();
		// var_dump($subject);
		// exit;
		$this->sendSuccess($subject);

	}
	public function topics(){
		$this->filterLogin();
		//sort :1,最新  2：访问量最多
		//按照sort 排序返回最新的10条主题
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : false;

        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量



		$prefix = C('DB_PREFIX');
		$order = "{$prefix}talk_topic.date,{$prefix}talk_topic.views DESC";

        $where = array();

        if($subject_id){
            $where[] = "{$prefix}talk_topic_subject.topic_subject_id = ".$subject_id;
        }

        $where[] = "{$prefix}talk_topic.status = '1'";
        $where[] = "{$prefix}talk_topic.approval = '1'";
		$wherestring = '';
		if(count($where)){
			$wherestring = implode(' AND ',$where);
		}

        if($subject_id){
            $list = M("talk_topic_subject")->field("{$prefix}talk_topic.*,{$prefix}member.realname,{$prefix}member.head,{$prefix}talk_subject.title as subtitle,(SELECT count({$prefix}talk_comments.tid) FROM {$prefix}talk_comments WHERE {$prefix}talk_comments.tid = {$prefix}talk_topic.id) as count")
                    ->join("{$prefix}talk_topic ON {$prefix}talk_topic.id = {$prefix}talk_topic_subject.tid")
                    ->join("{$prefix}member ON {$prefix}talk_topic.uid = {$prefix}member.uid")
                    ->join("{$prefix}talk_subject ON {$prefix}talk_subject.subject_id = {$prefix}talk_topic_subject.topic_subject_id")

                    ->where($wherestring)
                    ->limit($offset . ',' . $pagesize)
                    ->select();
        }else{

		    $list = M('talk_topic')->field("{$prefix}talk_topic.*,{$prefix}member.realname,{$prefix}member.head,(SELECT count({$prefix}talk_comments.tid) FROM {$prefix}talk_comments WHERE {$prefix}talk_comments.tid = {$prefix}talk_topic.id) as count")
                ->order($order)
                ->join("{$prefix}member ON {$prefix}talk_topic.uid = {$prefix}member.uid")
                ->where($wherestring)
                ->limit($offset . ',' . $pagesize)
                ->select();


            foreach($list as $i => $item){
                $talk_subject = M('talk_topic_subject')->field("*")
                     ->join("{$prefix}talk_subject ON {$prefix}talk_subject.subject_id = {$prefix}talk_topic_subject.topic_subject_id")
                    ->where("{$prefix}talk_topic_subject.tid=$item[id]")->select();
                $list[$i]['talk_subject'] = $talk_subject;
                $list[$i]['image'] = C('URL').$item['image'];
            }
        }
		$this->sendSuccess($list);
	}

	public function topic(){
		$this->filterLogin();

		//特定主题的内容
        $id = isset($_POST['id']) ? intval($_POST['id']) : false;
        if(!$id){
            return $this->sendError(2500, '主题不存在');
        }
		$prefix = C('DB_PREFIX');
        $uid = $this->user['uid'];

        $subject = M('talk_topic')->field("{$prefix}talk_topic.*,{$prefix}member.realname,{$prefix}member.head,(SELECT count({$prefix}talk_comments.tid) FROM {$prefix}talk_comments WHERE {$prefix}talk_comments.tid = {$prefix}talk_topic.id) as count")
            ->join("{$prefix}member ON {$prefix}talk_topic.uid = {$prefix}member.uid")

            ->where("{$prefix}talk_topic.id=$id")->find();


         $talk_subject = M('talk_topic_subject')->field("*")
                     ->join("{$prefix}talk_subject ON {$prefix}talk_subject.subject_id = {$prefix}talk_topic_subject.topic_subject_id")
                    ->where("{$prefix}talk_topic_subject.tid=$subject[id]")->select();
         $subject['talk_subject'] = $talk_subject;

        $comments = M('talk_comments')->field("{$prefix}talk_comments.*,{$prefix}member.realname,{$prefix}member.head,(SELECT count({$prefix}talk_comments_like.id) FROM {$prefix}talk_comments_like WHERE {$prefix}talk_comments_like.tid = {$prefix}talk_comments.tid AND {$prefix}talk_comments_like.cid = {$prefix}talk_comments.comment_id) as count")
            ->join("{$prefix}member ON {$prefix}talk_comments.uid = {$prefix}member.uid")
            ->where("{$prefix}talk_comments.tid=$id")->select();
		foreach ($comments as $i=>$value) {
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
                    ->where("{$prefix}talk_comments_like.tid = ".$value['tid']." AND {$prefix}talk_comments_like.uid = ".$uid." AND {$prefix}talk_comments_like.cid =" .$value['comment_id'])->find();
            $comments[$i]['like'] = $like;
            $comments[$i]['reply'] = $reply;
            $comments[$i]['reply_count'] = $reply_count;

        }

         $subject['comments'] = $comments;

		$this->sendSuccess($subject);
	}

	public function topicDetail(){

		//主题的详细内容，包括默认5条回复，每个回复最多3条评论
		$this->filterLogin();
        $id = isset($_GET['id']) ? intval($_GET['id']) : false;
		$prefix = C('DB_PREFIX');
        $uid = $this->user['uid'];

        $subject = M('talk_topic')->field("{$prefix}talk_topic.*,{$prefix}member.realname,{$prefix}member.head,(SELECT count({$prefix}talk_comments.tid) FROM {$prefix}talk_comments WHERE {$prefix}talk_comments.tid = {$prefix}talk_topic.id) as count")
            ->join("{$prefix}member ON {$prefix}talk_topic.uid = {$prefix}member.uid")

            ->where("{$prefix}talk_topic.id=$id")->find();


         $talk_subject = M('talk_topic_subject')->field("*")
                     ->join("{$prefix}talk_subject ON {$prefix}talk_subject.subject_id = {$prefix}talk_topic_subject.topic_subject_id")
                    ->where("{$prefix}talk_topic_subject.tid=$subject[id]")->select();
         $subject['talk_subject'] = $talk_subject;

        $comments = M('talk_comments')->field("{$prefix}talk_comments.*,{$prefix}member.realname,{$prefix}member.head,(SELECT count({$prefix}talk_comments_like.id) FROM {$prefix}talk_comments_like WHERE {$prefix}talk_comments_like.tid = {$prefix}talk_comments.tid AND {$prefix}talk_comments_like.cid = {$prefix}talk_comments.comment_id) as count")
            ->join("{$prefix}member ON {$prefix}talk_comments.uid = {$prefix}member.uid")
            ->where("{$prefix}talk_comments.tid=$id")->limit('0,5')->select();
		foreach ($comments as $i=>$value) {
            $reply = M('talk_replies')->field("{$prefix}talk_replies.*,{$prefix}member.realname,{$prefix}member.head")
                ->join("{$prefix}member ON {$prefix}talk_replies.uid = {$prefix}member.uid")
                ->where("{$prefix}talk_replies.tid=".$value['tid']." AND {$prefix}talk_replies.cid=".$value['comment_id']." AND {$prefix}talk_replies.child=0")->limit('0,3')->select();
            $reply_count = M('talk_replies')->field("{$prefix}talk_replies.*,{$prefix}member.realname,{$prefix}member.head")
                ->join("{$prefix}member ON {$prefix}talk_replies.uid = {$prefix}member.uid")
                ->where("{$prefix}talk_replies.tid=".$value['tid']." AND {$prefix}talk_replies.cid=".$value['comment_id']." AND {$prefix}talk_replies.child=0")->count();

                foreach ($reply as $key => $r) {
                    $childs = M('talk_replies')->field("{$prefix}talk_replies.*,{$prefix}member.realname,{$prefix}member.head,m.realname as name, m.head as heads")
                        ->join("{$prefix}member ON {$prefix}talk_replies.uid = {$prefix}member.uid")
                        ->join("{$prefix}member m ON {$prefix}talk_replies.ruid = m.uid")
                        ->where("{$prefix}talk_replies.rid=".$r['reply_id']." AND {$prefix}talk_replies.tid=".$r['tid']." AND {$prefix}talk_replies.cid=".$value['comment_id']." AND {$prefix}talk_replies.child=1")->limit('0,3')->select();
                        $reply[$key]['childs'] = $childs;
                }


            $like = M('talk_comments_like')->field("{$prefix}talk_comments_like.*")
                    ->where("{$prefix}talk_comments_like.tid = ".$value['tid']." AND {$prefix}talk_comments_like.uid = ".$uid." AND {$prefix}talk_comments_like.cid =" .$value['comment_id'])->select();
            $comments[$i]['like'] = $like;
            $comments[$i]['reply'] = $reply;
            $comments[$i]['reply_count'] = $reply_count;

        }

         $subject['comments'] = $comments;

		$this->sendSuccess($subject);

	}



	public function addTopic(){
		$this->filterLogin();
		//发表主题
		$prefix = C('DB_PREFIX');
        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : false;
        $name = isset($_POST['name']) ? trim($_POST['name']) : false;
        $data['uid'] = isset($_POST['uid']) ? intval($_POST['uid']) : '';
        $data['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
        $data['abstract'] = isset($_POST['abstract']) ? trim($_POST['abstract']) : '';
        $data['description'] = isset($_POST['description']) ? trim($_POST['description']) : '';
        $data['image'] = isset($_POST['image']) ? trim($_POST['image']) : '';
        $data['approval'] = 1;
        $data['status'] = 1;
        $data['date'] = date('Y-m-d H:i:s',time());
		$data['image'] = $this->upload();
        $title = isset($_POST['title']) ? trim($_POST['title']) : false;

   		if(!$name){
        	return $this->sendError(4001,'参数错误,名称不能为空！');
        }

        if($uid){
            $tid = M('talk_topic')->data($data)->add();

            foreach ($_POST['subject'] as  $value) {
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
                $sub['status'] = 1;
                $sub['approval'] = 1;
                $sub['type'] = 1;
                $sub['date'] = time();

                $subjectid = M('talk_subject')->data($sub)->add();

                $top = array();
                $top['topic_subject_id'] = $subjectid;
                $top['tid'] = $tid;


                M('talk_topic_subject')->data($top)->add();

            }
            $this->success('操作成功！',U('index'));
        }else{
        	return $this->sendError(4001,'参数错误,不存在用户id！');
        }
	}

	public function upload(){
		//上传图片
		$this->filterLogin();



	}
	public function replys(){
		$this->filterLogin();
		//回复，时间排序
		$uid = isset($_GET['uid']) ? intval($_GET['uid']) : false;
        $tid = isset($_GET['tid']) ? intval($_GET['tid']) : false;
        $cid = isset($_GET['cid']) ? intval($_GET['cid']) : false;
        $rid = isset($_GET['rid']) ? intval($_GET['rid']) : false;
        $ruid = isset($_GET['ruid']) ? intval($_GET['ruid']) : false;
        $prefix = C('DB_PREFIX');
        $order = "{$prefix}talk_replies.datetime DESC";
        $where = array();
        if(!$cid){
            return $this->sendError(4003,'参数错误,不存在评论ID！');
        } else {
            $where[] = "{$prefix}talk_replies.cid = '$cid'";

        }
        if(!$tid){
            return $this->sendError(4004,'参数错误,不存在主题ID！');
        } else {
            $where[] = "{$prefix}talk_replies.tid = '$tid'";
        }

        if($uid){
            $where[] = "{$prefix}talk_replies.uid = '$uid'";
        }

        if($ruid){
            $where[] = "{$prefix}talk_replies.ruid = '$ruid'";
        }


        $where[] = "{$prefix}talk_replies.approval = '1'";
        $where[] = "{$prefix}talk_replies.close = '0'";
        $where[] = "{$prefix}talk_replies.status = '1'";
        $where[] = "{$prefix}talk_replies.child=0";
        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }
        $reply = M('talk_replies')->field("{$prefix}talk_replies.*,{$prefix}member.realname,{$prefix}member.head")
                ->order($order)
                ->join("{$prefix}member ON {$prefix}talk_replies.uid = {$prefix}member.uid")
                ->where($wherestring)->select();

        //判断多层回复
        if($rid){

            foreach ($reply as $key => $r) {

                $were = array();
                if($uid){
                    $were[] = "{$prefix}talk_replies.uid = '$uid'";
                }
                $were[] = "{$prefix}talk_replies.tid = " .$r['tid'];
                $were[] = "{$prefix}talk_replies.cid = " .$r['cid'];
                $were[] = "{$prefix}talk_replies.rid = " .$r['reply_id'];
                $were[] = "{$prefix}talk_replies.approval = '1'";
                $were[] = "{$prefix}talk_replies.close = '0'";
                $were[] = "{$prefix}talk_replies.status = '1'";
                $were[] = "{$prefix}talk_replies.child=1";
                $werestring = '';
                if(count($where)){
                    $werestring = implode(' AND ',$were);
                }
                $childs = M('talk_replies')->field("{$prefix}talk_replies.*,{$prefix}member.realname,{$prefix}member.head,m.realname as name, m.head as heads")
                    ->join("{$prefix}member ON {$prefix}talk_replies.uid = {$prefix}member.uid")
                    ->join("{$prefix}member m ON {$prefix}talk_replies.ruid = m.uid")
                    ->where($werestring)->select();
                    $reply[$key]['childs'] = $childs;
            }

        }
        $this->sendSuccess($reply);
	}


	public function addReply(){
		$this->filterLogin();
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
        	return $this->sendError(4005,'参数错误,回复不能为空！');

            $this->error('回复不能为空！');
        }

        if(!$uid&&!$tid){
        	return $this->sendError(4006,'参数错误,参数不存在！');
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
	public function delReply(){
		$this->filterLogin();
		$ids = implode(',', I('post.ids'));
        if (!$ids) {
	        $id = isset($_GET['id']) ? intval($_GET['id']) : false;
	        if(!$id){
	        	return $this->sendError(4006,'参数错误,参数不存在! ');
	        }
	       $res = M('talk_replies')->data(array('close'=>'1'))->where("id=$id")->save();
		}else{
			$res = M('talk_replies')->data(array('close' => '1'))->where('id in(' . $ids.')')->save();
		}
	    if ($res) {
	     	return $this->sendSuccess('操作成功！');
        } else {
        	return $this->sendError(4007,'删除回复失败！');
        }
	}

	public function comments(){
		$this->filterLogin();
		$uid = isset($_GET['uid']) ? intval($_GET['uid']) : false;
        $tid = isset($_GET['tid']) ? intval($_GET['tid']) : false;
		$prefix = C('DB_PREFIX');
		if($uid){
    	$where[] = "{$prefix}talk_comments.uid = '$uid'";
		}
        if($tid){
        $where[] = "{$prefix}talk_comments.tid = '$tid'";
        }
    	$where[] = "{$prefix}talk_comments.approval = '1'";
    	$where[] = "{$prefix}talk_comments.close = '0'";
    	$where[] = "{$prefix}talk_comments.status = '1'";

		$wherestring = '';
		if(count($where)){
			$wherestring = implode(' AND ',$where);
		}

		$order = "{$prefix}talk_comments.datetime DESC";

		 $comments = M('talk_comments')->field("{$prefix}talk_comments.*,{$prefix}member.realname,{$prefix}member.head,(SELECT count({$prefix}talk_comments_like.id) FROM {$prefix}talk_comments_like WHERE {$prefix}talk_comments_like.tid = {$prefix}talk_comments.tid AND {$prefix}talk_comments_like.cid = {$prefix}talk_comments.comment_id) as count")
		 	->order($order)
            ->join("{$prefix}member ON {$prefix}talk_comments.uid = {$prefix}member.uid")
            ->where($wherestring)->select();
        $this->sendSuccess($comments);
	}
	public function addComment(){
		$this->filterLogin();
		$score = isset($_POST['score']) ? intval($_POST['score']) : 0;
        $tid = isset($_POST['tid']) ? intval($_POST['tid']) : false;
        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : false;
        $comment = isset($_POST['comment']) ? trim($_POST['comment']) : false;
        if(!$comment){
        	return $this->sendError(4008,'参数错误,评论内容不能为空！');

        }

        if(!$uid&&!$tid){
        	return $this->sendError(4006,'参数错误,参数不存在！');

        }

        $data['tid'] = isset($_POST['tid']) ? intval($_POST['tid']) : '';
        $data['uid'] = $uid;
        $data['comment'] = isset($_POST['comment']) ? trim($_POST['comment']) : '';
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
	     return $this->sendSuccess('操作成功！');
	}
	public function delComment(){
		$this->filterLogin();
		$ids = implode(',', I('post.ids'));
        if (!$ids) {

	        $id = isset($_GET['id']) ? intval($_GET['id']) : false;
	        if(!$id){
	        	return $this->sendError(4006,'参数错误,参数不存在');
	        }
	       $res = M('talk_comments')->data(array('close'=>'1'))->where("id=$id")->save();
		}else{
			$res = M('talk_comments')->data(array('close' => '1'))->where('id in(' . $ids.')')->save();
		}
	    if ($res) {
	     	return $this->sendSuccess('操作成功！');
        } else {
        	return $this->sendError(4009,'删除评论失败！');
        }
	}
}