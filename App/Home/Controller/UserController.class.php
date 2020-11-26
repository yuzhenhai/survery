<?php
namespace Home\Controller;
use Think\Controller;
use Think\Upload;
use Think\idcard\AipOcr;
const APP_ID = '11060986';
const API_KEY = 'pCZyDlnIWcX5ecMThwyznKGM';
const SECRET_KEY = 'ShOcGLDXSfvYYdAOPRi8VTiNAnFHb8jy';

class UserController extends ComController {
    public function profile(){
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $uid = isset($_GET['uid']) ? intval($_GET['uid']) : $this->USER['uid'];

        $prefix = C('DB_PREFIX');
        $order = "{$prefix}talk_topic.date desc";
        $where = array();
        $where[] = "{$prefix}talk_topic.uid = $uid";
        $where[] = "{$prefix}talk_topic.status = '1'";
        $where[] = "{$prefix}talk_topic.approval = '1'";
        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }

        $talks = M('talk_topic');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

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

        foreach ($list as $i => $item) {
            $subjects = M("talk_topic_subject")->field("{$prefix}talk_subject.*")
                        ->join("{$prefix}talk_subject ON {$prefix}talk_subject.subject_id = {$prefix}talk_topic_subject.topic_subject_id")
                        ->where("{$prefix}talk_subject.status=1 AND {$prefix}talk_subject.approval=1 AND {$prefix}talk_subject.close=0 AND {$prefix}talk_topic_subject.tid = ".$item['id'])
                        ->select();
            $list[$i]['subject'] = $subjects;
            // $list[$i]['abstract'] = strip_tags(htmlspecialchars_decode($item['abstract']));
            $list[$i]['description'] = mb_substr(strip_tags(htmlspecialchars_decode($item['description'])),0,C('post_talk_number'),'utf-8');

        }




        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();


        $this->assign('subject_id', $subject_id);
        $this->assign('list', $list);
        $this->assign('page', $page);

        $users = $this->user();
        $this->assign('users', $users);

        $profileuser = M('member')->field("{$prefix}member.*")
            ->where("{$prefix}member.uid = ".$uid)
            ->find();
        $this->assign('profileuser', $profileuser);

        $statistics = $this->getStatisticsByUid($uid);

        $this->assign('statistics', $statistics);

        $this->assign('loginuser', $this->USER);

        $this->display('profile');
    }
    public function profile_tags(){
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $uid = isset($_GET['uid']) ? intval($_GET['uid']) : $this->USER['uid'];
        $auid = $this->USER['uid'];
        $prefix = C('DB_PREFIX');
        if($this->USER['admin'] == 1 || $this->USER['uid'] == $uid){

        }else{
            $this->error('没有权限');
        }

        $remark = explode('，', $this->USER['remark']);
        $user = $this->USER;
        $user['remarks'] = $remark;

        // $tagspackage = M('tagspackage');
        // $pagesize = 10;#每页数量
        // $offset = $pagesize * ($p - 1);//计算记录偏移量
        // $count = $tagspackage->field("{$prefix}tagspackage.*")
        //     ->order($order)
        //     ->where($where)
        //     ->count();

        // $list = $tagspackage->field("{$prefix}tagspackage.*")
        //     ->order($order)
        //     ->where($where)
        //     ->limit($offset . ',' . $pagesize)
        //     ->select();


        // foreach($list as $i=>$item){
        //     $tags= M('taggroups')->field("{$prefix}taggroups.*")
        //     ->join("{$prefix}tag_package_groups ON {$prefix}tag_package_groups.taggroups_id = {$prefix}taggroups.id")
        //     ->where("{$prefix}tag_package_groups.tagspackage_id=".$item['id'])
        //     ->select();
        //     $list[$i]['taggroups']= $tags;
        //     //$list[$i]['counttaggroups'] = count($tags);
        // }
        $answers_annotation = M('member_annotation');

        $annotation = $answers_annotation->field("{$prefix}member_annotation.*")
            ->where("status = 1 AND uid = $uid AND auid = $auid")
            ->select();

        $users = $this->user();
        $this->assign('users', $users);

        $profileuser = M('member')->field("{$prefix}member.*")
            ->where("{$prefix}member.uid = ".$uid)
            ->find();
        $this->assign('profileuser', $profileuser);

        $statistics = $this->getStatisticsByUid($uid);
        $this->assign('annotation', $annotation);
        $this->assign('statistics', $statistics);

        $this->assign('loginuser', $this->USER);

        $this->display('profile_tags');
    }

    public function remark(){
        $auid = $this->USER['uid'];
        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : false;


        $annotation = isset($_POST['annotation']) ? trim($_POST['annotation']) : false;
        if(!$annotation && $uid){
            $this->error("备注不能为空");
        }
        $data['uid'] = $uid;
        $data['auid'] = $auid;
        $data['status'] = '1';
        $data['annotation'] = $annotation;
        $data['date'] = time();
        M("member_annotation")->data($data)->add();
        $this->success("操作成功！");

    }

    public function profile_task(){
        $uid = isset($_GET['uid']) ? intval($_GET['uid']) : $this->USER['uid'];
        $task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) :0;
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';

        $pagesize = 1;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $prefix = C('DB_PREFIX');

        if($this->USER['admin'] == 1 || $this->USER['uid'] == $uid){

        }else{
            $this->error('没有权限');
        }

        $list = M('task')->field("{$prefix}task.task_id,{$prefix}task.title,{$prefix}task.image,{$prefix}task.description,{$prefix}task.start,{$prefix}task.end,{$prefix}member_task.status as member_task_status")
            ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.group_id in ({$prefix}task.research_group)")
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
            ->join("LEFT JOIN {$prefix}member_task ON {$prefix}member_task.uid = {$prefix}member.uid AND {$prefix}member_task.task_id = {$prefix}task.task_id")
            ->where("{$prefix}member.uid='$uid' AND {$prefix}task.status=1 AND {$prefix}task.type = 1 AND {$prefix}member_task.status > 0")
            ->order("{$prefix}task.task_id DESC")
            ->select();
        if($task_id == 0){
             $task_id = $list[0]['task_id'];
        }
        foreach($list as $k=>$item){
            if($task_id == $item['task_id']){
                $countsubject = M('task_subjects')->field("{$prefix}task_subjects.*,{$prefix}task_questions_answers.answer_id,{$prefix}task_questions_answers.created as answer_created")
                    ->join("{$prefix}task_questions_answers ON {$prefix}task_questions_answers.subject_id = {$prefix}task_subjects.subject_id")
                    ->order("{$prefix}task_subjects.o asc")
                    ->where("{$prefix}task_subjects.task_id='$task_id' AND {$prefix}task_questions_answers.skip=0 AND {$prefix}task_questions_answers.uid='$uid' ")
                    ->count();
                $subjectlist = M('task_subjects')->field("{$prefix}task_subjects.*,{$prefix}task_questions_answers.answer_id,{$prefix}task_questions_answers.created as answer_created")
                    ->join("{$prefix}task_questions_answers ON {$prefix}task_questions_answers.subject_id = {$prefix}task_subjects.subject_id")
                    ->order("{$prefix}task_subjects.o asc")
                    ->where("{$prefix}task_subjects.task_id='$task_id' AND {$prefix}task_questions_answers.skip=0 AND {$prefix}task_questions_answers.uid='$uid'")
                    ->limit($offset . ',' . $pagesize)
                    ->select();

                foreach($subjectlist as $i=>$subject){
                    //问题列表
                    $subject_id = $subject['subject_id'];
                    $questions = M('task_questions')->field("{$prefix}task_questions.question_id,{$prefix}task_questions.topic,{$prefix}task_questions.title")
                        ->order("{$prefix}task_questions.topic asc")
                        ->where(" {$prefix}task_questions.subject_id = $subject_id")
                        ->select();

                    $subjectlist[$i]['questions'] = $questions;
                    $subjectlist[$i]['question_answers'] = A('Task')->getAnswers($subject['answer_id']);

                    $remarks = M('task_questions_answers_remark')->field("{$prefix}task_questions_answers_remark.*,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.head,{$prefix}member.admin")
                        ->join("{$prefix}member ON {$prefix}task_questions_answers_remark.uid = {$prefix}member.uid")
                        ->order("{$prefix}task_questions_answers_remark.remark_id ASC")
                        ->where("{$prefix}task_questions_answers_remark.answer_id = ".$subject['answer_id'])
                        ->select();

                    $subjectlist[$i]['remarks'] = $remarks;
                    $subjectlist[$i]['remark_status'] = $remarks[count($remarks)-1]['status'];
                }
                $page = new \Think\Page($countsubject, $pagesize);
                $page = $page->show();
                $this->assign('page', $page);

                $list[$k]['count_subjects'] = A('Task')->getTotalSubjects($task_id);//题目数量
                $list[$k]['count_questions'] = A('Task')->getTotalQuestions($task_id);//问题数量
                $list[$k]['count_users_all'] = A('Task')->getTotalUsers($task_id);//参与人数
                $list[$k]['count_comments'] = 0;//评论
                $list[$k]['leftday'] = intval((strtotime($item['end'])-time())/60/60/24);

                $list[$k]['subjectlist'] = $subjectlist;
            }
        }



        //dump($list);
        $this->assign('list', $list);
        $users = $this->user();
        $this->assign('users', $users);

        $profileuser = M('member')->field("{$prefix}member.*")
            ->where("{$prefix}member.uid = ".$uid)
            ->find();
        $this->assign('profileuser', $profileuser);

        $statistics = $this->getStatisticsByUid($uid);

        $this->assign('statistics', $statistics);

        $this->assign('loginuser', $this->USER);

        $this->display('profile_task');
    }
    public function profile_ask(){
        $uid = isset($_GET['uid']) ? intval($_GET['uid']) : $this->USER['uid'];
        $task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) :0;
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';

        $pagesize = 1;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $prefix = C('DB_PREFIX');
        if($this->USER['admin'] == 1 || $this->USER['uid'] == $uid){

        }else{
            $this->error('没有权限');
        }

        $list = M('task')->field("{$prefix}task.*,{$prefix}member_task.status as member_task_status")
            ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.group_id in ({$prefix}task.research_group)")
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
            ->join("LEFT JOIN {$prefix}member_task ON {$prefix}member_task.uid = {$prefix}member.uid AND {$prefix}member_task.task_id = {$prefix}task.task_id")
            ->where("{$prefix}member.uid='$uid' AND {$prefix}task.status=1 AND {$prefix}task.type = 1 AND {$prefix}member_task.status > 0")
            ->order("{$prefix}task.task_id DESC")
            ->select();
        if($task_id == 0){
             $task_id = $list[0]['task_id'];
        }
        foreach($list as $k=>$item){
            if($task_id == $item['task_id']){
                $countsubject = M('task_subjects')->field("{$prefix}task_subjects.*,{$prefix}task_questions_answers.answer_id")
                    ->join("{$prefix}task_questions_answers ON {$prefix}task_questions_answers.subject_id = {$prefix}task_subjects.subject_id")
                    ->order("{$prefix}task_subjects.o asc")
                    ->where("{$prefix}task_subjects.task_id='$task_id' AND {$prefix}task_questions_answers.skip=0 AND {$prefix}task_questions_answers.uid='$uid' AND (SELECT count(*) FROM {$prefix}task_questions_answers_remark WHERE {$prefix}task_questions_answers_remark.answer_id = {$prefix}task_questions_answers.answer_id ) > 0")
                    ->count();
                $subjectlist = M('task_subjects')->field("{$prefix}task_subjects.*,{$prefix}task_questions_answers.answer_id")
                    ->join("{$prefix}task_questions_answers ON {$prefix}task_questions_answers.subject_id = {$prefix}task_subjects.subject_id")
                    ->order("{$prefix}task_subjects.o asc")
                    ->where("{$prefix}task_subjects.task_id='$task_id' AND {$prefix}task_questions_answers.skip=0 AND {$prefix}task_questions_answers.uid='$uid' AND (SELECT count(*) FROM {$prefix}task_questions_answers_remark WHERE {$prefix}task_questions_answers_remark.answer_id = {$prefix}task_questions_answers.answer_id ) > 0")
                    ->limit($offset . ',' . $pagesize)
                    ->select();
                foreach($subjectlist as $i=>$subject){
                        $remarks = M('task_questions_answers_remark')->field("{$prefix}task_questions_answers_remark.*,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.head,{$prefix}member.admin")
                            ->join("{$prefix}member ON {$prefix}task_questions_answers_remark.uid = {$prefix}member.uid")
                            ->order("{$prefix}task_questions_answers_remark.remark_id ASC")
                            ->where("{$prefix}task_questions_answers_remark.answer_id = ".$subject['answer_id'])
                            ->select();
                        $subjectlist[$i]['remarks'] = $remarks;
                        $subjectlist[$i]['remark_status'] = $remarks[count($remarks)-1]['status'];
                }
                $list[$k]['subjectlist'] = $subjectlist;

                $page = new \Think\Page($countsubject, $pagesize);
                $page = $page->show();
                $this->assign('page', $page);
            }
        }
        //dump($list);
        $this->assign('list', $list);

        $users = $this->user();
        $this->assign('users', $users);

        $profileuser = M('member')->field("{$prefix}member.*")
            ->where("{$prefix}member.uid = ".$uid)
            ->find();
        $this->assign('profileuser', $profileuser);

        $statistics = $this->getStatisticsByUid($uid);

        $this->assign('statistics', $statistics);

        $this->assign('loginuser', $this->USER);

        $this->display('profile_ask');
    }
    public function profile_gallery(){
        $uid = isset($_GET['uid']) ? intval($_GET['uid']) : $this->USER['uid'];

        $prefix = C('DB_PREFIX');

        $users = $this->user();
        $this->assign('users', $users);


        $topic = M('talk_topic')->field("{$prefix}talk_topic.*")
                ->order("{$prefix}talk_topic.date DESC")
                ->where("{$prefix}talk_topic.uid = '$uid'")
                ->select();


        $profileuser = M('member')->field("{$prefix}member.*")
            ->where("{$prefix}member.uid = ".$uid)
            ->find();
        $this->assign('profileuser', $profileuser);
        $this->assign('topic', $topic);

        $statistics = $this->getStatisticsByUid($uid);

        $this->assign('statistics', $statistics);

        $this->assign('loginuser', $this->USER);
        $this->display('profile_gallery');
    }
    public function getStatisticsByUid($uid){
        $prefix = C('DB_PREFIX');
        $count_task = 0;
        $count_topic = 0;
        $count_comments = 0;

        $count_task = M('task')->field("{$prefix}task.*")
            ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.group_id in ({$prefix}task.research_group)")
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
            ->where("{$prefix}member.uid='$uid' AND {$prefix}task.status=1 AND {$prefix}task.type = 1")
            ->count();

        $count_topic = M('talk_topic')->field("{$prefix}talk_topic.*")
            ->where("{$prefix}talk_topic.uid = ".$uid)
            ->count();

        $topiclist = M('talk_topic')->field("{$prefix}talk_topic.id")
            ->where("{$prefix}talk_topic.uid = ".$uid)
            ->select();

        foreach ($topiclist as $topic) {

            $count_comment = M('talk_comments')->field("{$prefix}talk_comments.*")
            ->where("{$prefix}talk_comments.tid = ".$topic['id'])
            ->count();

            $count_reply = M('talk_replies')->field("{$prefix}talk_replies.*")
            ->where("{$prefix}talk_replies.tid = ".$topic['id'])
            ->count();

            $count_comments += $count_comment + $count_reply;
        }

        $statistics = array();
        $statistics['count_task'] = $count_task;
        $statistics['count_topic'] = $count_topic;
        $statistics['count_comments'] = $count_comments;
        return $statistics;
    }
	public function member(){
		$uid = $this->USER['uid'];

		$prefix = C('DB_PREFIX');

        $this->assign('user', $this->USER);
		$this->display('member');
	}

 	public function updatemember()
    {
        $uid = $this->USER['uid'];

        $user = isset($_POST['user']) ? htmlspecialchars($_POST['user'], ENT_QUOTES) : '';

        if(strlen($_POST['idcard'])>0){
            if(strlen($_POST['idcard']) != 18){
                $this->error('请输入正确的身份证！');
            }
        }

        $data['head'] = isset($_POST['head']) ? trim($_POST['head']) : '';
        $data['sex'] = isset($_POST['sex']) ? intval($_POST['sex']) : 0;
        $data['realname'] = isset($_POST['realname']) ? trim($_POST['realname']) : '';
        // $data['user'] = isset($_POST['user']) ? trim($_POST['user']) : '';

        $data['birthday'] = isset($_POST['birthday']) ? strtotime($_POST['birthday']) : 0;
        // $data['phone'] = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        // $data['email'] = isset($_POST['email']) ? trim($_POST['email']) : '';
        // $data['idcard'] = isset($_POST['idcard']) ? trim($_POST['idcard']) : '';
        $data['signature'] = isset($_POST['signature']) ? trim($_POST['signature']) : '';
        $data['position'] = isset($_POST['position']) ? trim($_POST['position']) : '';
        $data['edu'] = isset($_POST['edu']) ? trim($_POST['edu']) : '';
        $data['income'] = isset($_POST['income']) ? trim($_POST['income']) : '';
        $data['fincome'] = isset($_POST['fincome']) ? trim($_POST['fincome']) : '';
        $data['interest'] = isset($_POST['interest']) ? trim($_POST['interest']) : '';
        $data['update_date'] = time();


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
                'savePath' => 'upload/avatars/',
                'subName'  =>  '',
            ));
            $info = $upload->upload($_FILES);
            if($info){
                foreach ($info as $item) {
                    if($item['key'] == 'image'){
                        unlink($data['head']);
                        $data['head'] = $item['savename'];
                    }
                }
            }else{
                $data['head'] = I('post.head', '', 'strip_tags');
            }
        }

        if (!$uid) {
            $this->error('参数错误');
        } else {
            M('member')->data($data)->where("uid=$uid")->save();
            addlog('编辑会员信息，会员UID：' . $uid,$user,2);
        }
        $this->success('操作成功！',U('member'));
    }
    public function member_info(){
		$uid = $this->USER['uid'];

		$prefix = C('DB_PREFIX');

        $this->assign('user', $this->USER);

		$this->display('member_info');

    }

    public function updateinfo(){
        $uid = $this->USER['uid'];

        $data['qq'] = isset($_POST['qq']) ? trim($_POST['qq']) : '';
        $data['weixin'] = isset($_POST['weixin']) ? trim($_POST['weixin']) : '';
        $data['douban'] = isset($_POST['douban']) ? trim($_POST['douban']) : '';
        $data['zhihu'] = isset($_POST['zhihu']) ? trim($_POST['zhihu']) : '';
        $data['email'] = isset($_POST['email']) ? trim($_POST['email']) : '';

        $password = isset($_POST['password']) ? trim($_POST['password']) : false;
        $data['update_date'] = time();

		$prefix = C('DB_PREFIX');

        $users = M('member')->field("{$prefix}member.*")
            ->where("{$prefix}member.uid = ".$uid)
            ->find();
        if (!$uid) {
            $this->error('参数错误');
        } else {

	        if(!$password){
            	M('member')->data($data)->where("uid=$uid")->save();
		    } else {
		    	$password = password($password);

		    	if($users['password'] != $password){
		            $this->error('原密码不匹配！');
		        }

		        $password1 = isset($_POST['password1']) ? trim($_POST['password1']) : false;
		        $password2 = isset($_POST['password2']) ? trim($_POST['password2']) : false;

		        if($password1){
		        	if($password1 != $password2){
		            	$this->error('两次输入密码不匹配！');
					}

		        	$data['password'] = password($password1);
		        }

            	M('member')->data($data)->where("uid=$uid")->save();
		    }

            addlog('编辑会员信息，会员UID：' . $uid,$user,2);
        }
        $this->success('操作成功！',U('member_info'));
    }

    public function member_talk(){
		$uid = $this->USER['uid'];

		// $prefix = C('DB_PREFIX');

        // $user = M('member')->field("{$prefix}member.*")
        //     ->where("{$prefix}member.uid = ".$uid)
        //     ->find();

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : false;
		$prefix = C('DB_PREFIX');
        $order = "{$prefix}talk_topic.date asc";
        $where = array();
        $where[] = "{$prefix}talk_topic.uid = $uid";
        $where[] = "{$prefix}talk_topic.status = '1'";
        $where[] = "{$prefix}talk_topic.approval = '1'";
        $where[] = "{$prefix}talk_topic.close = '0'";

        if($subject_id){
            $where[] = "{$prefix}talk_topic_subject.topic_subject_id = ".$subject_id;
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
                        ->join("{$prefix}talk_topic ON {$prefix}talk_topic.id = {$prefix}talk_topic_subject.tid")
                        ->join("{$prefix}member ON {$prefix}talk_topic.uid = {$prefix}member.uid")
                        ->where($wherestring)
                        ->count();

            $list = M("talk_topic_subject")->field("{$prefix}talk_topic.*,{$prefix}member.realname,{$prefix}member.head,(SELECT count({$prefix}talk_comments.tid) FROM {$prefix}talk_comments WHERE {$prefix}talk_comments.tid = {$prefix}talk_topic.id) as count")
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
            // $list[$i]['abstract'] = strip_tags(htmlspecialchars_decode($item['abstract']));
            $list[$i]['description'] = mb_substr(strip_tags(htmlspecialchars_decode($item['description'])),0,C('post_talk_number'),'utf-8');
        }
        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();

        $this->assign('subject_id', $subject_id);
        $this->assign('list', $list);
        $this->assign('page', $page);

		$this->display('member_talk');

    }

    public function member_score(){
		$uid = $this->USER['uid'];

		$prefix = C('DB_PREFIX');

        $scores = M('member_score')
            ->where("uid = ".$uid)
            ->order("date desc")
            ->select();

        $this->assign('scores', $scores);

		$this->display('member_score');
    }

    public function member_convert(){
		$uid = $this->USER['uid'];

		$prefix = C('DB_PREFIX');

        $user = M('member')->field("{$prefix}member.*")
            ->where("{$prefix}member.uid = ".$uid)
            ->find();

        $converts = M('member_score')->field("{$prefix}member_score.*")
            ->where("{$prefix}member_score.uid = ".$uid." AND score<0")
            ->select();

        $this->assign('users', $user);
        $this->assign('converts', $converts);
		$this->display('member_convert');
    }
    public function member_message(){
        $uid = $this->USER['uid'];

        $prefix = C('DB_PREFIX');

        $messages = M('member_message')->field("{$prefix}member_message.*")
            ->where("{$prefix}member_message.uid = ".$uid)
            ->select();
        $this->assign('messages', $messages);

		$this->display('member_message');
    }

    public function member_approve(){
        $uid = $this->USER['uid'];

        $prefix = C('DB_PREFIX');

        $user = M('member_approve')->field("{$prefix}member_approve.*")
            ->where("{$prefix}member_approve.uid = ".$uid)
            ->find();
        

            $error_code = false;
        $front = $this->idcard('front',$user['front']);
        $back = $this->idcard('back',$user['reverse']);
        if(!empty($user['front']) || !empty($user['back'])){
            if($front['error_code'] || $back['error_code']){
                $error_code = true;
            }
        }



        $card0 = 'Public/themes/images/card-0.png';
        $card1 = 'Public/themes/images/card-1.png';
        $this->assign('error_code', $error_code);
        $this->assign('front', $front);
        $this->assign('back', $back);
        $this->assign('card0', $card0);
        $this->assign('card1', $card1);
        $this->assign('user', $user);
        $this->display('approve');
    }

    public function updateapprove(){
        $uid = $this->USER['uid'];
        $prefix = C('DB_PREFIX');
        $user = M('member_approve')->field("{$prefix}member_approve.*")
            ->where("{$prefix}member_approve.uid = ".$uid)
            ->find();

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
                'rootPath' => 'Public/',
                'savePath' => 'themes/images/',
                'subName'  =>  '',
            ));
            $info = $upload->upload($_FILES);

            if($info){
                foreach ($info as $item) {
                    if($item['key'] == 'fronts'){
                        unlink($data['front']);
                        $data['front'] = $upload->rootPath.$item['savepath'].$item['savename'];
                    }
                    if($item['key'] == 'reverses'){
                        unlink($data['reverse']);
                        $data['reverse'] = $upload->rootPath.$item['savepath'].$item['savename'];
                    }
                }
            }else{
                $data['front'] = I('post.front', '', 'strip_tags');
                $data['reverse'] = I('post.reverse', '', 'strip_tags');
            }
        }
        $front = $this->idcard('front',$data['front']);
        $back = $this->idcard('back',$data['reverse']);

        if($front['error_msg']||$back['error_msg']){
            $data['approcal'] = 0;
        } else {
            $data['approcal'] = 1;
        }

        // $front = $this->idcard('front',$data['front']);
        // $back = $this->idcard('front',$data['reverse']);

        $data['date'] = time();
        $data['uid'] = $uid;

        if($user){
            M('member_approve')->data($data)->where("uid=$uid")->save();
        }else{
            M('member_approve')->data($data)->add();
        }

        $this->success('操作成功！');

    }

    public function idcard($idCardSide='',$img=""){
        // require_once __PUBLIC__.'/themes/idcard/AipOcr.php';
        // Public/themes/images/5abde4be4a518.jpg


        $client = new AipOcr(APP_ID,API_KEY,SECRET_KEY);

        $image = file_get_contents($img);
        // $idCardSide = "back";
        //front
        // 调用身份证识别
        $card = $client->idcard($image, $idCardSide);
        return $card;
        // var_dump($card);
    }
}