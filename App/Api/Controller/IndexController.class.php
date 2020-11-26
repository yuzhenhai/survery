<?php
namespace Api\Controller;
use Think\Exception;
class IndexController extends ComController
{

	public function index(){
		$this->filterLogin();
		var_dump($this->user);
		exit;
	}

	public function subject(){
		//所有的话题（tags)

		$this->filterLogin();

        $subject = M('talk_subject')->where(array('status' => '1','approval'=>'1','close'=>'0'))->select();
		// var_dump($subject);
		// exit;
		$this->sendSuccess($subject);


	}
	public function topics(){
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
            $list = M("talk_topic_subject")->field("{$prefix}talk_topic.*,{$prefix}member.realname,{$prefix}member.head,(SELECT count({$prefix}talk_comments.tid) FROM {$prefix}talk_comments WHERE {$prefix}talk_comments.tid = {$prefix}talk_topic.id) as count")
                    ->join("{$prefix}talk_topic ON {$prefix}talk_topic.id = {$prefix}talk_topic_subject.tid")
                    ->join("{$prefix}member ON {$prefix}talk_topic.uid = {$prefix}member.uid")
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
		//特定主题的内容
		$this->filterLogin();

		//特定主题的内容
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
		//
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

	public function replys(){
		//回复，时间排序
		//
		$this->filterLogin();
		//回复，时间排序
		$uid = isset($_GET['uid']) ? intval($_GET['uid']) : false;
        $tid = isset($_GET['tid']) ? intval($_GET['tid']) : false;
        $cid = isset($_GET['cid']) ? intval($_GET['cid']) : false;
        $rid = isset($_GET['rid']) ? intval($_GET['rid']) : false;
        $ruid = isset($_GET['ruid']) ? intval($_GET['ruid']) : false;
        $prefix = C('DB_PREFIX');

        $order = "{$prefix}talk_replies.datetime DESC";

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
                	->order("")
                    ->join("{$prefix}member ON {$prefix}talk_replies.uid = {$prefix}member.uid")
                    ->join("{$prefix}member m ON {$prefix}talk_replies.ruid = m.uid")
                    ->where($werestring)->select();
                    $reply[$key]['childs'] = $childs;
            }

        }
        $this->sendSuccess($reply);
	}

	public function comments(){
		//
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


}