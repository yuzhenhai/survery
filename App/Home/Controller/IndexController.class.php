<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends ComController {
    public function index(){
        $uid = $this->USER['uid'];

		$prefix = C('DB_PREFIX');
		$order = '';
		$where = '';
        $pagesize = 3;#每页数量
		$offset = 0;
        if($this->USER['admin'] == 1){
            $tasklist =  M('task')->field("{$prefix}task.*")
                ->where("{$prefix}task.status=1 AND {$prefix}task.type = 1")
                ->order("{$prefix}task.task_id DESC")
                ->limit($offset . ',' . $pagesize)
                ->select();

            $answers_remarks = M('task_questions_answers_remark')->field("{$prefix}task_questions_answers_remark.*,{$prefix}task_subjects.title as subject_title,{$prefix}task_subjects.o as subject_o")
                ->join("{$prefix}task_questions_answers ON {$prefix}task_questions_answers.answer_id = {$prefix}task_questions_answers_remark.answer_id")
                ->join("{$prefix}task_subjects ON {$prefix}task_subjects.subject_id = {$prefix}task_questions_answers.subject_id")
                ->order("{$prefix}task_questions_answers_remark.remark_id DESC")
                ->where("{$prefix}task_questions_answers_remark.status=1")
                ->select();
            $this->assign('answers_remarks', $answers_remarks);

        }else{
            $answers_remarks = M('task_questions_answers_remark')->field("{$prefix}task_questions_answers_remark.*,{$prefix}task_subjects.title as subject_title,{$prefix}task_subjects.o as subject_o")
                ->join("{$prefix}task_questions_answers ON {$prefix}task_questions_answers.answer_id = {$prefix}task_questions_answers_remark.answer_id")
                ->join("{$prefix}task_subjects ON {$prefix}task_subjects.subject_id = {$prefix}task_questions_answers.subject_id")
                ->order("{$prefix}task_questions_answers_remark.remark_id DESC")
                ->where("{$prefix}task_questions_answers_remark.status=1 AND {$prefix}task_questions_answers.uid='$uid'")
                ->select();
            $this->assign('answers_remarks', $answers_remarks);

            $tasklist = M('task')->field("{$prefix}task.*,{$prefix}member_task.status as member_task_status")
                ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.group_id in ({$prefix}task.research_group)")
                ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
                ->join("LEFT JOIN {$prefix}member_task ON {$prefix}member_task.uid = {$prefix}member.uid AND {$prefix}member_task.task_id = {$prefix}task.task_id")
                ->where("{$prefix}member.uid='$uid' AND {$prefix}task.status=1 AND {$prefix}task.type = 1")

                ->order("{$prefix}task.task_id DESC")
                ->limit($offset . ',' . $pagesize)
                ->select();
        }

        foreach($tasklist as $i=>$item){

            $tasklist[$i]['count_questions'] = A('Task')->getTotalQuestions($item['task_id']);//问题数量
            $tasklist[$i]['count_users_all'] = A('Task')->getTotalUsers($item['task_id']);//参与人数
            $tasklist[$i]['count_comments'] = 0;//评论

            $tasklist[$i]['lefttime'] = A('Task')->timeLeft($item['end']);  // 比较日期差
        }
        $this->assign('tasklist', $tasklist);
		$this->assign('controller_name', CONTROLLER_NAME);

        $order = '';
        $pagesize = 5;#每页数量
        $offset = 0;

        $talk_topic = M('talk_topic')->field("{$prefix}talk_topic.*, {$prefix}member.realname,{$prefix}member.head, (SELECT count({$prefix}talk_comments.tid) FROM {$prefix}talk_comments WHERE {$prefix}talk_comments.tid = {$prefix}talk_topic.id) as count")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}talk_topic.uid")
            ->where("{$prefix}talk_topic.status=1 AND {$prefix}talk_topic.approval=1 AND {$prefix}talk_topic.close=0")
            ->limit($offset . ',' . $pagesize)
            ->select();
            foreach ($talk_topic as $key => $value) {
               $talk_subject = M('talk_topic_subject')->field("*")
                     ->join("{$prefix}talk_subject ON {$prefix}talk_subject.subject_id = {$prefix}talk_topic_subject.topic_subject_id") 
                    ->where("{$prefix}talk_topic_subject.tid=$value[id] AND {$prefix}talk_subject.status=1 AND {$prefix}talk_subject.approval=1 AND {$prefix}talk_subject.close=0")->select();
                $talk_topic[$key]['talk_subject'] = $talk_subject;
            }
        foreach ($talk_topic as $key => $item) {
            $talk_topic[$key]['date'] = dgmdate(strtotime($item['date']));

            $talk_topic[$key]['description'] = mb_substr(strip_tags(htmlspecialchars_decode($item['description'])),0,35,'utf-8').'...';

        }

        $users = $this->user();
        $bulletin = M('bulletin');

        $bulletins = $bulletin->field("{$prefix}bulletin.*, {$prefix}member.user")
            ->order("{$prefix}bulletin.date desc")
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}bulletin.uid")
            ->where("{$prefix}bulletin.type = 1 AND {$prefix}bulletin.status=1 AND {$prefix}bulletin.approval = 1 AND {$prefix}bulletin.close = 0")
            ->find();
        if(!empty($bulletins)){
            $bulletins['content'] = strip_tags(htmlspecialchars_decode($bulletins['content']));
        }
// var_dump($bulletin);
        $this->assign('bulletin', $bulletins);

        $this->assign('users', $users);
        $this->assign('topiclist', $talk_topic);

		$this->display('index');

    }

    public function bulls(){
        $id = isset($_GET['id']) ? $_GET['id'] : 1;
        $prefix = C('DB_PREFIX');
        $bulletin = M('bulletin');
        $bulletin = $bulletin->field("{$prefix}bulletin.*, {$prefix}member.user, {$prefix}member.head")
            ->order("{$prefix}bulletin.date desc")
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}bulletin.uid")
            ->where("{$prefix}bulletin.type = 1 AND {$prefix}bulletin.id = $id AND {$prefix}bulletin.status=1 AND {$prefix}bulletin.approval = 1 AND {$prefix}bulletin.close = 0")
            ->find();

            $bulletin['content'] = htmlspecialchars_decode($bulletin['content']);

        $this->assign('bulletin', $bulletin);
        $this->display('bulls');
    }

    public function information(){
        
        $this->display('information');
    }
}