<?php
/**
 *
 * Copyright: Bodasoft
 * Author   ：dowei<douwei@bodait.com>
 * Date     ：2018-1-23
 * Version  ：1.0.0
 * Desc     ：后台首页
 *
 **/

namespace Admin\Controller;

class IndexController extends ComController
{
    public function index()
    {
        $prefix = C('DB_PREFIX');

        //总任务
        $count_task = M('task')->where("{$prefix}task.type = 1")->count();
        $this->assign('count_task', $count_task);
        //题目数量
        $count_subjects = M('task_subjects')->count();
        $this->assign('count_subjects', $count_subjects);
        //调研人员
        $count_member = M('member')->where("{$prefix}member.admin = 0 AND {$prefix}member.close = 0")->count();
        $this->assign('count_member', $count_member);
        //w问题数量
        $count_questions = M('task_questions')->count();
        $this->assign('count_questions', $count_questions);
        //有效问卷
        $count_task_users_completed = M('member_task')->where("{$prefix}member_task.status = 2")->count();
        $this->assign('count_task_users_completed', $count_task_users_completed);
        //进行中的任务
        $tasks = M('task')->where("{$prefix}task.type = 1 AND {$prefix}task.state = 1")->select();
        $total_task_users = 0;
        $total_task_users_completed = 0;
        foreach ($tasks as $i => $item) {
            $count_users_all = 0;
            if($item['research_group']){
                $count_users_all = M('member')->field("{$prefix}member.uid")
                    ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
                    ->where("{$prefix}auth_group_access.group_id in (".$item['research_group'].")")
                    ->count();
            }
            $count_users_completed = M('member_task')->field("{$prefix}member_task.id")
                    ->where("{$prefix}member_task.task_id = ".$item['task_id']." AND status = 2")
                    ->count();
            $tasks[$i]['count_users_all'] = $count_users_all;
            $tasks[$i]['count_users_completed'] = $count_users_completed;
            $tasks[$i]['percent'] = round(floatval($count_users_completed/$count_users_all*100),2);

            $total_task_users += $count_users_all;
            $total_task_users_completed += $count_users_completed;
        }
        $this->assign('tasks', array_slice($tasks,0,4));

        //当前任务百分比
        $task_percent = round(floatval($total_task_users_completed/$total_task_users*100),2);
        $this->assign('task_percent', $task_percent);

        //今日问卷
        $today_task_users_completed_count = M('member_task')->where("{$prefix}member_task.status = 2 AND date(from_unixtime({$prefix}member_task.complete_time)) = curdate()")->count();
        $this->assign('today_task_users_completed_count', $today_task_users_completed_count);
        //今日上线
        $today_online_count = M("user_log")
                    ->where("type = '1' AND date(from_unixtime(t)) = curdate()")
                    ->count("distinct(uid)");
        $this->assign('today_online_count', $today_online_count);

        //任务回复
        $task_questions_answers = M('task_questions_answers')->field("distinct {$prefix}task_questions_answers.*,{$prefix}task.name as task_name,{$prefix}task_subjects.title as subject_title,{$prefix}task_subjects.description as subject_description,{$prefix}task_subjects.o as subject_o,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.head")
            ->join("{$prefix}task_subjects ON {$prefix}task_questions_answers.subject_id = {$prefix}task_subjects.subject_id")
            ->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
            ->join("{$prefix}member ON {$prefix}task_questions_answers.uid = {$prefix}member.uid")
            ->order(" {$prefix}task_questions_answers.answer_id DESC")
            ->limit('0,8')
            ->select();

        $this->assign('task_questions_answers', $task_questions_answers);

        //追问
        $answers_remarks = M('task_questions_answers_remark')->field("{$prefix}task_questions_answers_remark.*,{$prefix}member.head,{$prefix}member.realname")
                    ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}task_questions_answers_remark.uid")
                    ->where("{$prefix}task_questions_answers_remark.type = '2'")
                    ->limit('0,5')
                    ->select();

        foreach ($answers_remarks as $key => $value) {
             $answers_remarks[$key]['dgmdate'] = dgmdate($value['datetime']);
        }
        $this->assign('answers_remarks', $answers_remarks);

        //用户
        $subQuery = M('user_log')->field("{$prefix}user_log.uid,{$prefix}user_log.t,{$prefix}member.head,{$prefix}member.realname")
            ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}user_log.uid")
            ->order("{$prefix}user_log.id DESC")
            ->select(false);

        $members = M()->table("($subQuery) as a")->group("uid")->limit('0,12')->select();

        foreach ($members as $key => $value) {
             $members[$key]['dgmdate'] = dgmdate($value['t']);
        }
        $this->assign('members', $members);

        //访问来源
        $referer = M("user_log")->field("{$prefix}user_log.*")->where("{$prefix}user_log.type=1")->select();
        $windows = array();
        $ipad = array();
        $phone = array();
        $times =array();
        foreach ($referer as $key => $value) {

        }
       
        
        foreach ($referer as $key => $value) {
            $time = date('Y-m-d',$value['t']);

            if($value['login'] == 'windows'){
                $windows[] = array($value['uid'],$time,'windows');
            }
            if($value['login'] == 'ipad'){
                $ipad[] = array($value['uid'],$time,'ipad');
            }
            if($value['login'] == 'android' || $value['login'] == 'iphone'){
                $mobile[] = array($value['uid'],$time,'mobile ');
            }
        }
         $windows = array_map('unserialize', array_unique(array_map('serialize', $windows)));
         $ipad = array_map('unserialize', array_unique(array_map('serialize', $ipad)));
         $mobile = array_map('unserialize', array_unique(array_map('serialize', $mobile)));
        $windows = count($windows);
        $ipad = count($ipad);
        $mobile = count($mobile);
        
        $this->assign('windows', $windows);
        $this->assign('ipad', $ipad);
        $this->assign('mobile', $mobile);

        //交流圈
        $talks = M("talk_topic")->field("{$prefix}talk_topic.*,{$prefix}member.head,{$prefix}member.realname")
                    ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}talk_topic.uid")
                   ->select();
        $this->assign('talks', $talks);

        $talk_topic = M('talk_topic')->field("{$prefix}talk_topic.*,{$prefix}member.head,{$prefix}member.realname")
                    ->order("{$prefix}talk_topic.date DESC")
                    ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}talk_topic.uid")
                    ->limit('0,8')
                    ->select();

        foreach ($talk_topic as $key => $value) {
            $talk_topic[$key]['dgmdate'] = dgmdate(strtotime($value['date']));
        }
        $this->assign('talk_topic', $talk_topic);

        $this->assign('nav', array('', '', ''));//导航
        $this->display();
    }
    public function getTotalUsers($task_id){
        $prefix = C('DB_PREFIX');
        $task = M('task')->where("task_id='$task_id'")->find();

        $count_users_all = 0;
        $research_group= explode(',',$task['research_group']);
        if(count($research_group) == 1){
            $count_users_all = M('member')->field("{$prefix}member.uid")
                ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
                ->where("{$prefix}auth_group_access.group_id ='".$task['research_group']."'")
                ->count();
        }
        if(count($research_group) > 1){
            $count_users_all = M('member')->field("{$prefix}member.uid")
                ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
                ->where("{$prefix}auth_group_access.group_id in (".$task['research_group'].")")
                ->count();
        }
        return $count_users_all;//参与人数
    }
}