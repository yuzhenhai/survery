<?php
namespace Home\Controller;
use Think\Controller;
use Think\Upload;
class TaskController extends ComController {
	public function index(){
		$uid = $this->USER['uid'];
		//dump($this->USER);

		$prefix = C('DB_PREFIX');

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';

        $answer_status = isset($_GET['answer_status']) ? $_GET['answer_status'] : 'all';

		$this->assign('answer_status', $answer_status);

		$order = "";
		$where = '';

		if(isset($_GET['answer_status']) ){
	        if( $answer_status == 0){
	        	$answer_status = null;
	        }
		}
        $taskgroups = M('task_group')->field("{$prefix}task_group.*")
    		->order("{$prefix}task_group.taskgroup_id ASC")
    		->where("{$prefix}task_group.status = 1")
            ->select();
        foreach($taskgroups as $g=>$taskgroup){
        	$taskgroup_id = $taskgroup['taskgroup_id'];
	        if($this->USER['admin'] == 1){
		        $list = M('task')->field("{$prefix}task.*")
		    		->where("{$prefix}task.state>0 AND {$prefix}task.state<5 AND {$prefix}task.type = 1 AND {$prefix}task.taskgroup_id = '$taskgroup_id'")
		    		->order("{$prefix}task.task_id DESC")
		            ->select();
	        }else{
				if(isset($_GET['answer_status']) ){
					if($answer_status == 0){

				        $list = M('task')->field("{$prefix}task.*,{$prefix}member_task.status as member_task_status")
					        ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.group_id in ({$prefix}task.research_group)")
					        ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
					        ->join("LEFT JOIN {$prefix}member_task ON {$prefix}member_task.uid = {$prefix}member.uid AND {$prefix}member_task.task_id = {$prefix}task.task_id")
				    		->where("{$prefix}member.uid='$uid' AND {$prefix}task.state>0 AND {$prefix}task.state<5 AND {$prefix}task.type = 1 AND {$prefix}member_task.status is null AND {$prefix}task.taskgroup_id = '$taskgroup_id'")
				    		->order("cast({$prefix}member_task.status as SIGNED INTEGER) ASC")
				            ->select();
					}else{
						$status = intval($answer_status);

				        $list = M('task')->field("{$prefix}task.*,{$prefix}member_task.status as member_task_status")
					        ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.group_id in ({$prefix}task.research_group)")
					        ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
					        ->join("LEFT JOIN {$prefix}member_task ON {$prefix}member_task.uid = {$prefix}member.uid AND {$prefix}member_task.task_id = {$prefix}task.task_id")
				    		->where("{$prefix}member.uid='$uid' AND {$prefix}task.state>0 AND {$prefix}task.state<5 AND {$prefix}task.type = 1 AND {$prefix}member_task.status='$status' AND {$prefix}task.taskgroup_id = '$taskgroup_id'")
				    		->order("cast({$prefix}member_task.status as SIGNED INTEGER) ASC")
				            ->select();
					}
				}else{
			        $list = M('task')->field("{$prefix}task.*,{$prefix}member_task.status as member_task_status")
				        ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.group_id in ({$prefix}task.research_group)")
				        ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
				        ->join("LEFT JOIN {$prefix}member_task ON {$prefix}member_task.uid = {$prefix}member.uid AND {$prefix}member_task.task_id = {$prefix}task.task_id")
			    		->where("{$prefix}member.uid='$uid' AND {$prefix}task.state>0 AND {$prefix}task.state<5 AND {$prefix}task.type = 1 AND {$prefix}task.taskgroup_id = '$taskgroup_id'")
			    		->order("cast(member_task_status as SIGNED INTEGER) ASC")
			            ->select();
				}
			}
	        foreach($list as $i=>$item){
	        	$list[$i]['lefttime'] = $this->timeLeft($item['end']);  // 比较日期差
	        	$list[$i]['count_questions'] = $this->getTotalQuestions($item['task_id']);//问题数量
	        	$list[$i]['count_users_all'] = $this->getTotalUsers($item['task_id']);//参与人数
	        	//$list[$i]['count_comments'] = 0;//评论
	        }
	        if(count($list)){
	        	$taskgroups[$g]['list'] = $list;
	        }else{
	        	unset($taskgroups[$g]);
	        }

        }
        $this->assign('taskgroups', $taskgroups);


        $users = $this->user();
        $this->assign('users', $users);

        $answers_remarks = $this->getAnswersRemarks();
        $this->assign('answers_remarks', $answers_remarks);

		$this->display('index');
	}
    public function detail($task_id = 0)
    {
    	$uid = $this->USER['uid'];
    	$task_id = intval($task_id);
    	if($this->USER['admin'] == 1){
    		$this->adminPreview($task_id);
    	}else{
			$prefix = C('DB_PREFIX');

	        $task = $this->getTaskInfo($task_id);
	        $this->assign('task', $task);

		    $member_task = M('member_task')->where("task_id='$task_id' AND uid='$uid'")->find();

			if($member_task){
		        $this->nextSubject($task_id);
			}

	        $subjectlist = $this->getSubjectList($task_id,$uid);
			$this->assign('subjectlist', $subjectlist);

	        $this->display('detail');
	    }
    }
    public function start(){
    	$uid = $this->USER['uid'];
		$prefix = C('DB_PREFIX');
		$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;
		if($task_id){
			$task = M('task')->where("task_id='$task_id'")->find();

			//是否有权限做任务
    		$member =  M('member')->field("{$prefix}member.*")
        		->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
        		->where("{$prefix}member.uid='$uid' AND {$prefix}auth_group_access.group_id in (".$task['research_group'].")")
        		->find();

        	if($member['uid']){
        		//是否已开启任务
				$count_member_task = M('member_task')->field("{$prefix}member_task.id")
					->where("{$prefix}member_task.task_id ='".$task_id."' AND {$prefix}member_task.uid='".$uid."'")
					->count();
				if($count_member_task){
					//已开启
				}else{

					$prev_task_id = $this->getPrevTask($task_id);
					if($prev_task_id){
						$prev_member_task = M('member_task')->field("{$prefix}member_task.id")
							->where("{$prefix}member_task.task_id ='".$prev_task_id."' AND {$prefix}member_task.uid='".$uid."'")
							->find();
		        		if($prev_member_task && $prev_member_task['status']==2){

		        		}else{
		        			$this->error('参数错误,有未完成任务！',U('detail',array('task_id'=>$prev_task_id)));
		        		}
					}

					$member_task_data['id'] = 0;
					$member_task_data['uid'] = $uid;
					$member_task_data['task_id'] = (int)$task_id;
					$member_task_data['answered'] = 0;
					$member_task_data['status'] = 1;
					$member_task_data['start_time'] = time();

					M('member_task')->data($member_task_data)->add();
					addlog('任务开启，任务名称：' . $task['name'],false,3);
	        	}
	        	$this->nextSubject($task_id);

			}else{
				$this->error('您没有权限做该任务！',U('index'));
			}
		}

    }
    public function getPrevTask($task_id){
		$uid = $this->USER['uid'];
		$auth_group_id = $this->USER['gid'];
		$prefix = C('DB_PREFIX');

        $auth_group_id = M('auth_group_access')->field("{$prefix}auth_group_access.group_id")
	        ->join("{$prefix}task ON {$prefix}auth_group_access.group_id in ({$prefix}task.research_group)")
	        ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
    		->where("{$prefix}member.uid='$uid' AND {$prefix}task.task_id='$task_id'")
            ->getField('group_id');

        $task = M('task_research_group')->field("{$prefix}task_research_group.*")
            ->where("{$prefix}task_research_group.task_id='$task_id' AND {$prefix}task_research_group.auth_group_id='$auth_group_id'")
            ->find();
        $o = $task['o'];

        $prevtask = M('task_research_group')->field("{$prefix}task_research_group.*")
            ->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_research_group.task_id")
            ->where("{$prefix}task_research_group.o<'$o' AND {$prefix}task_research_group.auth_group_id='$auth_group_id' AND {$prefix}task.status=1 ")
            ->order("{$prefix}task_research_group.o DESC")
            ->find();
        return $prevtask['task_id'];

    }
	public function complete(){
		$uid = $this->USER['uid'];

		$prefix = C('DB_PREFIX');
        $task_subjects = M('task_subjects');
        $task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;

        $task = $this->getTaskInfo($task_id);
        $this->assign('task', $task);

        $member_task = M('member_task')->where("task_id='$task_id' AND uid='$uid'")->find();

        $subjectlist = $this->getSubjectList($task_id,$uid);
		$this->assign('subjectlist', $subjectlist);


		$res = M('member_task')->data(array('status' => 2,'complete_time' => time()))->where("task_id='$task_id' AND uid='$uid'")->save(); //已完成
		if($res){
			addlog('任务完成，任务名称：' . $task['name'],false,3);

			//任务完成获得积分
	        if($task['points'] > 0){
	        	$score = intval($task['points']);
	            $fatie = array();
	            $fatie['uid'] = $uid;
	            $fatie['score'] = $score;
	            $fatie['tid'] = 0;
	            $fatie['relevance_name'] = 'member_task';
	            $fatie['relevance_module'] = 'id';
	            $fatie['relevance_value'] = $member_task['id'];
	            $fatie['info'] = '任务完成,任务名称:'.$task['title'];
	            $fatie['type'] = 4;
	            $fatie['status'] = -1;
	            $fatie['date'] = time();
	            M('member_score')->data($fatie)->add();
	            $user_data = array();
	            $interim_integral = $this->USER['interim_integral'];
	            $user_data['interim_integral'] = $score + $interim_integral;
	            M('member')->data($user_data)->where("uid=$uid")->save();
	        }
		}
		$this->display('complete');
	}
    public function subject(){
    	$uid = $this->USER['uid'];
    	$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : '1';
    	$task_id  = 0;
    	if($this->USER['admin'] == 1){
    		$this->adminPreview(0,$subject_id);
    	}else{
			$prefix = C('DB_PREFIX');

			if ($subject_id <> '') {

				$this->assign('subject_id', $subject_id);

				$subject = $this->getSubject($subject_id,$uid);
				$this->assign('subject', $subject);

				$task_id = intval($subject['task_id']);
	        }

	        $task = $this->getTaskInfo($task_id);
	        $this->assign('task', $task);

	        $subjectlist = $this->getSubjectList($task_id,$uid);
			$this->assign('subjectlist', $subjectlist);

			$this->display('subject');
		}
    }
	public function getPrevSubjectByOrder($task_id,$o){
		$prefix = C('DB_PREFIX');

        $subject = M('task_subjects')->field("{$prefix}task_subjects.*")
            ->where("{$prefix}task_subjects.task_id='$task_id' AND {$prefix}task_subjects.o<'$o'")
            ->order("{$prefix}task_subjects.o DESC")
            ->find();

        $subject_id = isset($subject['subject_id'])?$subject['subject_id']:0;
        return $subject_id;
	}
	public function getNextSubjectByOrder($task_id,$o){
		$prefix = C('DB_PREFIX');

        $subject = M('task_subjects')->field("{$prefix}task_subjects.*")
            ->where("{$prefix}task_subjects.task_id='$task_id' AND {$prefix}task_subjects.o>'$o'")
            ->order("{$prefix}task_subjects.o ASC")
            ->find();

        $subject_id = isset($subject['subject_id'])?$subject['subject_id']:0;
        return $subject_id;
	}
	public function getSubject($subject_id,$uid){
		$prefix = C('DB_PREFIX');

        $subject = M('task_subjects')->field("{$prefix}task_subjects.*,{$prefix}task.title as tasktitle")
            ->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
            ->where("subject_id='$subject_id'")->find();

        if($uid == -1){
        	$subject_answers = M('task_questions_answers')->field("{$prefix}task_questions_answers.*,{$prefix}member.realname,{$prefix}member.head")
        		->join("{$prefix}member on {$prefix}member.uid={$prefix}task_questions_answers.uid")
	        	->where("{$prefix}task_questions_answers.subject_id='$subject_id'")
	        	->select();
	        foreach($subject_answers as $o=>$answer){
	        	$subject_answers[$o]['question_answers'] = $this->getAnswers($answer['answer_id']);
	        	$subject_answers[$o]['islike'] = $this->islike($answer['answer_id'],$uid);
	        	$subject_answers[$o]['countlike'] = $this->getCountLike($answer['answer_id']);

	            $remarks = M('task_questions_answers_remark')->field("{$prefix}task_questions_answers_remark.*,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.head,{$prefix}member.admin")
	                ->join("{$prefix}member ON {$prefix}task_questions_answers_remark.uid = {$prefix}member.uid")
	                ->order("{$prefix}task_questions_answers_remark.remark_id ASC")
	                ->where("{$prefix}task_questions_answers_remark.answer_id = ".$answer['answer_id'])
	                ->select();

	            $subject_answers[$o]['remarks'] = $remarks;

	            $comments = M('task_questions_answers_comment')->field("{$prefix}task_questions_answers_comment.*,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.head,{$prefix}member.admin")
	                ->join("{$prefix}member ON {$prefix}task_questions_answers_comment.uid = {$prefix}member.uid")
	                ->where("{$prefix}task_questions_answers_comment.answer_id = ".$answer['answer_id'])
	                ->select();

	            $subject_answers[$o]['comments'] = $comments;
	        }
	        $subject['subject_answers'] = $subject_answers;
        }else{
	    	$subject_answer = M('task_questions_answers')->field("{$prefix}task_questions_answers.*,{$prefix}member.realname,{$prefix}member.head")
	    		->join("{$prefix}member on {$prefix}member.uid = {$prefix}task_questions_answers.uid")
	        	->where("{$prefix}task_questions_answers.subject_id='$subject_id' AND {$prefix}task_questions_answers.uid='$uid'")
	        	->find();

	        if($subject_answer){
	        	if($subject_answer['skip'] == 1){
	        		$subject_id = $this->getNextSubjectByOrder($subject['task_id'],$subject['o']);
	        		return $this->getSubject($subject_id,$uid);
	        	}
	            $question_answers = $this->getAnswers($subject_answer['answer_id']);//用户回答
	            $subject_answer['question_answers'] = $question_answers;

	       		$subject_answer['countlike'] = $this->getCountLike($subject_answer['answer_id']);

	            $remarks = M('task_questions_answers_remark')->field("{$prefix}task_questions_answers_remark.*,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.head,{$prefix}member.admin")
	                ->join("{$prefix}member ON {$prefix}task_questions_answers_remark.uid = {$prefix}member.uid")
	                ->order("{$prefix}task_questions_answers_remark.remark_id ASC")
	                ->where("{$prefix}task_questions_answers_remark.answer_id = ".$subject_answer['answer_id'])
	                ->select();

	            $subject_answer['remarks'] = $remarks;


	            $comments = M('task_questions_answers_comment')->field("{$prefix}task_questions_answers_comment.*,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.head,{$prefix}member.admin")
	                ->join("{$prefix}member ON {$prefix}task_questions_answers_comment.uid = {$prefix}member.uid")
	                ->where("{$prefix}task_questions_answers_comment.answer_id = ".$subject_answer['answer_id'])
	                ->select();

	            $subject_answer['comments'] = $comments;


	        	$other_subject_answers = M('task_questions_answers')->field("{$prefix}task_questions_answers.*,{$prefix}member.realname,{$prefix}member.head")
	        		->join("{$prefix}member on {$prefix}member.uid={$prefix}task_questions_answers.uid")
		        	->where("{$prefix}task_questions_answers.subject_id='$subject_id' AND {$prefix}task_questions_answers.answer_id!='".$subject_answer['answer_id']."'")
		        	->select();

		        foreach($other_subject_answers as $o=>$other_answer){
		        	$other_subject_answers[$o]['question_answers'] = $this->getAnswers($other_answer['answer_id']);
		        	$other_subject_answers[$o]['islike'] = $this->islike($other_answer['answer_id'],$uid);
		        	$other_subject_answers[$o]['countlike'] = $this->getCountLike($other_answer['answer_id']);

		            $remarks = M('task_questions_answers_remark')->field("{$prefix}task_questions_answers_remark.*,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.head,{$prefix}member.admin")
		                ->join("{$prefix}member ON {$prefix}task_questions_answers_remark.uid = {$prefix}member.uid")
		                ->where("{$prefix}task_questions_answers_remark.answer_id = ".$other_answer['answer_id'])
		                ->select();

		            $other_subject_answers[$o]['remarks'] = $remarks;

		            $comments = M('task_questions_answers_comment')->field("{$prefix}task_questions_answers_comment.*,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.head,{$prefix}member.admin")
		                ->join("{$prefix}member ON {$prefix}task_questions_answers_comment.uid = {$prefix}member.uid")
		                ->where("{$prefix}task_questions_answers_comment.answer_id = ".$other_answer['answer_id'])
		                ->select();

		            $other_subject_answers[$o]['comments'] = $comments;
		        }
		        $subject_answer['other_subject_answers'] = $other_subject_answers;
	        }
			$subject['subject_answer'] = $subject_answer;
        }
        //问题列表
		$questions = M('task_questions')->field("{$prefix}task_questions.*")
			->order("{$prefix}task_questions.topic asc")
			->where(" {$prefix}task_questions.subject_id = $subject_id")
			->select();

		foreach($questions as $i=>$question){
			$questions[$i]['title'] = $this->getQuestionTitle($uid,$question['title'],$task_id);

			if($question['type'] == 'radio'||$question['type'] == 'check'||$question['type'] == 'matrix'){
				$selectlist =  M('task_questions_item')->field("{$prefix}task_questions_item.*")
					->order("{$prefix}task_questions_item.item_id asc")
					->where(" {$prefix}task_questions_item.question_id = ".$question['question_id'])
					->select();

				foreach($selectlist as $select){
					$questions[$i]['select'][] = array(
						"item_id"=>$select['item_id'],
						"item_title"=>$select['item_title'],
						"item_radio"=>$select['item_radio'],
						"item_value"=>$select['item_value'],
						"item_jump"=>$select['item_jump'],
						"item_tb"=>$select['item_tb'],
						"item_tbr"=>$select['item_tbr'],
						"item_img"=>$select['item_img'],
						"item_imgtext"=>$select['item_imgtext'],
						"item_desc"=>$select['item_desc'],
						"item_label"=>$select['item_label'],
						"item_huchi"=>$select['item_huchi']
					);
				}
				if($question['type'] == 'matrix'){
					$questions[$i]['titlerows'] = explode("\n",$question["rowtitle"]);
				}
			}

		}
		$subject['questions'] = $questions;
		$subject['surveydata']= $this->get_string_from_list($questions);
		$subject['prev'] = $this->getPrevSubjectByOrder($subject['task_id'],$subject['o']);
		$next_subject_id = $this->getNextSubjectByOrder($subject['task_id'],$subject['o']);
		if($subject['subject_answer']){
			$subject['next'] = $next_subject_id;
		}

		return $subject;
	}
	public function nextSubject($task_id = 0){
		$prefix = C('DB_PREFIX');
		$uid = $this->USER['uid'];
		$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;

        $subject_id = $this->getNextSubject($uid,$task_id);
		if ($subject_id <> '') {
			$this->assign('subject_id', $subject_id);

	        $subject = M('task_subjects')->field("{$prefix}task_subjects.*,{$prefix}task.title as tasktitle")
	            ->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
	            ->where("subject_id='$subject_id'")->find();

	        $member_task = M('member_task')->where("task_id='$task_id' AND uid='$uid'")->find();
	        if($member_task){

	        }else{
				$this->error('参数错误,任务还未开启！',U('detail',array('task_id'=>$task_id)));
			}

			$have_unanswered = $this->check_have_unanswered($uid,$task_id,$subject['o']);
			if($have_unanswered){
        		$this->error('参数错误,有未完成的前置题目！',U('detail',array('task_id'=>$task_id)));
			}

	        //题目关联条件判断是否跳过
	        $relations = M('task_subject_relation')->field("{$prefix}task_subject_relation.*")
	            ->where("{$prefix}task_subject_relation.subject_id='$subject_id'")
	            ->select();
	        if(count($relations)){
	        	$check = false;
	        	foreach($relations as $relation){
	        		$relation_subject_id = $relation['relation_subject_id'];
	        		$relation_question_id = $relation['relation_question_id'];
	        		$relation_question_item_id = $relation['relation_question_item_id'];
	        		$relation_compare = $relation['relation_compare'];
	        		$relation_value = $relation['relation_value'];
	        		$question_answers = $this->getUserSubjectQuestionAnswer($uid,$relation_subject_id,$relation_question_id);
	        		if($check == true){
	        			break;
	        		}
	        		foreach($question_answers as $answer_item){
		        		if($check == true){
		        			break;
		        		}
	        			if($relation_compare == 'item'){
		        			if($relation_question_item_id == $answer_item['item_id']){
		        				$check = true;
		        				break;
		        			}
	        			}
	        			if($relation_compare == '%'){
							 if(stripos($answer_item['itemtext'],$relation_value)!== false){
							    $check = true;
		        				break;
							}
							 if(stripos($answer_item['itemvalue'],$relation_value)!== false){
							    $check = true;
		        				break;
							}
	        			}
	        			if($relation_compare == 'eq'){
							if($answer_item['itemvalue'] === $relation_value){
							    $check = true;
		        				break;
							}

	        			}
	        			if($relation_compare == 'gt'){
							if($answer_item['itemvalue'] > $relation_value){
							    $check = true;
		        				break;
							}
	        			}
	        			if($relation_compare == 'it'){
							if($answer_item['itemvalue'] < $relation_value){
							    $check = true;
		        				break;
							}
	        			}

	        		}
	        	}

	        	if($check == false){
	        		//添加跳过题目
	        		$subject_answer = M('task_questions_answers')
						->where("{$prefix}task_questions_answers.subject_id='$subject_id' AND {$prefix}task_questions_answers.uid='$uid'")
						->find();
	        		if($subject_answer){

	        		}else{
						$answer_data['answer_id'] = 0;
						$answer_data['task_id'] = (int)$subject['task_id'];
						$answer_data['subject_id'] = $subject['subject_id'];
						$answer_data['uid'] = $uid;
						$answer_data['skip'] = 1;
						$answer_data['created'] = date('Y-m-d H:i:s');
						$answer_id = M('task_questions_answers')->data($answer_data)->add();

						$task = M('task')->where("task_id='$task_id'")->find();
						addlog('跳过题目，任务名称：' . $task['name'].', 第'.$subject['o'].'题提交');

						M('member_task')->where("task_id='$task_id' AND uid='$uid'")->setInc('answered',1);
					}
					$this->nextSubject($task_id);
	        	}
	        }
	        $this->redirect('Task/subject', ['subject_id' => $subject_id]);
        }
	}
	public function getNextSubject($uid,$task_id){

		$prefix = C('DB_PREFIX');

		$member_task = M('member_task')->where("task_id='$task_id' AND uid='$uid'")->find();
		$total = intval($member_task['answered']);
        if($member_task['status'] == 2){
			$total = 0;
        }
        /*
		//作废回答
		$subject_answer = M('task_questions_answers')
			->where("{$prefix}task_questions_answers.uid='$uid' AND status=-1")
			->order("{$prefix}task_questions_answers.answer_id ASC")
			->find();
		if($subject_answer){
			return $subject_answer['subject_id'];
		}
		*/

        $subjects = M('task_subjects')->field("{$prefix}task_subjects.*")
            ->where("{$prefix}task_subjects.task_id='$task_id'")
            ->order("{$prefix}task_subjects.o ASC")
		    ->limit(intval($total) . ',1')
            ->select();

        $subject_id = isset($subjects[0]['subject_id'])?$subjects[0]['subject_id']:0;
        return $subject_id;
	}
	public function check_have_unanswered($uid,$task_id,$o){
		$prefix = C('DB_PREFIX');
        $have_unanswered = M('task_subjects')->field("{$prefix}task_subjects.*")
        	->join("LEFT JOIN {$prefix}task_questions_answers ON {$prefix}task_questions_answers.subject_id = {$prefix}task_subjects.subject_id")
            ->where("{$prefix}task_subjects.o<'$o' AND {$prefix}task_subjects.task_id='$task_id' AND {$prefix}task_questions_answers.answer_id is null AND  {$prefix}task_questions_answers.uid='$uid' ")
            ->order("{$prefix}task_subjects.o ASC,{$prefix}task_subjects.subject_id ASC")
            ->count();

        return $have_unanswered;
	}
    public function analysis(){
    	$uid = $this->USER['uid'];
    	if($this->USER['admin'] == 1){
			$prefix = C('DB_PREFIX');
	        $task_subjects = M('task_subjects');
	        $subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : '1';
			if ($subject_id <> '') {
				$this->assign('subject_id', $subject_id);

		        $tasksubject = $task_subjects->field("{$prefix}task_subjects.*,{$prefix}task.title as tasktitle")
		            ->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
		            ->where("subject_id='$subject_id'")->find();
				$this->assign('tasksubject', $tasksubject);

		        $task_id = intval($tasksubject['task_id']);
		        $task = $this->getTaskInfo($task_id);
		        $this->assign('task', $task);

		        $subjectlist = $this->getSubjectList($task_id,-1);
				$this->assign('subjectlist', $subjectlist);

				$questions = M('task_questions')->field("{$prefix}task_questions.*")
					->order("{$prefix}task_questions.topic asc")
					->where("{$prefix}task_questions.subject_id = ".$subject_id)
					->select();

				foreach($questions as $j=>$qitem){

					$allanswer = M('task_questions_answers_item')->field("count({$prefix}task_questions_answers_item.answer_item_id) as allanswer")
						->where("{$prefix}task_questions_answers_item.question_id = ".$qitem['question_id'] )
						->find();


					if($qitem['type'] == 'radio' || $qitem['type'] == 'check'){
						$selectlist = array();
						$selectlist = M('task_questions_item')->field("{$prefix}task_questions_item.*")
							->order("{$prefix}task_questions_item.item_id asc")
							->where("{$prefix}task_questions_item.question_id = ".$qitem['question_id'] )
							->select();

						foreach($selectlist as $ai=>$select){
							$selectlist[$ai] = array(
								"item_id"=>$select['item_id'],
								"item_title"=>$select['item_title'],
								"item_radio"=>$select['item_radio'],
								"item_value"=>$select['item_value'],
								"item_jump"=>$select['item_jump'],
								"item_tb"=>$select['item_tb'],
								"item_tbr"=>$select['item_tbr'],
								"item_img"=>$select['item_img'],
								"item_imgtext"=>$select['item_imgtext'],
								"item_desc"=>$select['item_desc'],
								"item_label"=>$select['item_label'],
								"item_huchi"=>$select['item_huchi']
							);

							$answer = M('task_questions_answers_item')->field("count({$prefix}task_questions_answers_item.answer_item_id) as countanswer")
								->where("{$prefix}task_questions_answers_item.itemValue = ".$select['item_value']." AND {$prefix}task_questions_answers_item.question_id = ".$select['question_id'] )
								->find();
							$selectlist[$ai]['countanswer'] = $answer['countanswer'];
							$selectlist[$ai]['percent'] = round(intval($answer['countanswer'])/intval($allanswer['allanswer'])*100,2);

						}

						$questions[$j]['selectlist'] = $selectlist;
						$questions[$j]['allanswer'] = $allanswer['allanswer'];
					}else{
						unset($questions[$j]);
						continue;
					}

				}
				$this->assign('questions', $questions);

				//项目标签
				$task_item = M('task')->field("{$prefix}task.*")
					->where("{$prefix}task.task_id = ".$tasksubject['task_id'])
					->find();

				$taglist = M('tags')->field("distinct {$prefix}tags.*")
					->join("{$prefix}tag_package_groups ON {$prefix}tag_package_groups.taggroups_id = {$prefix}tags.groupid")
					->where("{$prefix}tags.status=1 AND {$prefix}tag_package_groups.tagspackage_id in (".$task_item['project_tags'].")")
					->select();

		        $this->assign('taglist', $taglist);
	        }


			$this->display('analysis');
		}
    }
    public function crossanalysis(){
    	$uid = $this->USER['uid'];
    	if($this->USER['admin'] == 1){
			$prefix = C('DB_PREFIX');
	        $task_subjects = M('task_subjects');
	        $subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : '1';
			if ($subject_id <> '') {
				$this->assign('subject_id', $subject_id);

		        $tasksubject = $task_subjects->field("{$prefix}task_subjects.*,{$prefix}task.title as tasktitle")
		            ->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
		            ->where("subject_id='$subject_id'")->find();
				$this->assign('tasksubject', $tasksubject);

		        $task_id = intval($tasksubject['task_id']);
		        $task = $this->getTaskInfo($task_id);
		        $this->assign('task', $task);

		        $subjectlist = $this->getSubjectList($task_id,-1);
				$this->assign('subjectlist', $subjectlist);

				$questions = M('task_questions')->field("{$prefix}task_questions.*")
					->order("{$prefix}task_questions.topic asc")
					->where("{$prefix}task_questions.subject_id = ".$subject_id)
					->select();

				foreach($questions as $j=>$qitem){

					$allanswer = M('task_questions_answers_item')->field("count({$prefix}task_questions_answers_item.answer_item_id) as allanswer")
						->where("{$prefix}task_questions_answers_item.question_id = ".$qitem['question_id'] )
						->find();


					if($qitem['type'] == 'radio' || $qitem['type'] == 'check'){
						$selectlist = array();
						$selectlist = M('task_questions_item')->field("{$prefix}task_questions_item.*")
							->order("{$prefix}task_questions_item.item_id asc")
							->where("{$prefix}task_questions_item.question_id = ".$qitem['question_id'] )
							->select();

						foreach($selectlist as $ai=>$select){
							$selectlist[$ai] = array(
								"item_id"=>$select['item_id'],
								"item_title"=>$select['item_title'],
								"item_radio"=>$select['item_radio'],
								"item_value"=>$select['item_value'],
								"item_jump"=>$select['item_jump'],
								"item_tb"=>$select['item_tb'],
								"item_tbr"=>$select['item_tbr'],
								"item_img"=>$select['item_img'],
								"item_imgtext"=>$select['item_imgtext'],
								"item_desc"=>$select['item_desc'],
								"item_label"=>$select['item_label'],
								"item_huchi"=>$select['item_huchi']
							);

							$answer = M('task_questions_answers_item')->field("count({$prefix}task_questions_answers_item.answer_item_id) as countanswer")
								->where("{$prefix}task_questions_answers_item.itemValue = ".$select['item_value']." AND {$prefix}task_questions_answers_item.question_id = ".$select['question_id'] )
								->find();
							$selectlist[$ai]['countanswer'] = $answer['countanswer'];
							$selectlist[$ai]['percent'] = round(intval($answer['countanswer'])/intval($allanswer['allanswer'])*100,2);

						}

						$questions[$j]['selectlist'] = $selectlist;
						$questions[$j]['allanswer'] = $allanswer['allanswer'];
					}else{
						unset($questions[$j]);
						continue;
					}

				}
				$this->assign('questions', $questions);
	        }
	        $color = array();
	        $color[] = ' ';
	        $color[] = ' progress-bar-success';
	        $color[] = ' progress-bar-warning';
	        $color[] = ' progress-bar-danger';
	        $color[] = ' progress-bar-pink';
	        $color[] = ' progress-bar-inverse';
	        $color[] = ' ';
	        $color[] = ' progress-bar-success';
	        $color[] = ' progress-bar-warning';
	        $color[] = ' progress-bar-danger';
	        $color[] = ' progress-bar-pink';
	        $color[] = ' progress-bar-inverse';
	        $color[] = ' ';
	        $color[] = ' progress-bar-success';
	        $color[] = ' progress-bar-warning';
	        $color[] = ' progress-bar-danger';
	        $color[] = ' progress-bar-pink';
	        $color[] = ' progress-bar-inverse';
	        $this->assign('color', $color);

			$this->assign('color', $color);
			$this->display('crossanalysis');
		}

    }
	public function directsubmit(){
		$uid = $this->USER['uid'];

		$prefix = C('DB_PREFIX');

        $subject_id = isset($_REQUEST['subject_id']) ? intval($_REQUEST['subject_id']) : 0;
		if($subject_id > 0){
        	$subject = M('task_subjects')->where("{$prefix}task_subjects.subject_id='$subject_id'")->find();

        	$task_id = (int)$subject['task_id'];

	        $member_task = M('member_task')->where("task_id='$task_id' AND uid='$uid'")->find();

        	$subject_answer = M('task_questions_answers')
				->where("{$prefix}task_questions_answers.subject_id='$subject_id' AND {$prefix}task_questions_answers.uid='$uid'")
				->find();
        	if($subject_answer){
        		$answer_id = $subject_answer['answer_id'];
        		/*
        		if($subject_answer['status'] == -1){
        			M('task_questions_answers_item')->where("answer_id='$answer_id'")->delete();
        			M('task_questions_answers')->data(array('status' => 0,'created' => date('Y-m-d H:i:s')))->where("answer_id='{$answer_id}'")->save();
        			$dosave = true;
        		}
        		*/
        	}else{
				$answer_data['answer_id'] = 0;
				$answer_data['task_id'] = (int)$subject['task_id'];
				$answer_data['subject_id'] = $subject_id;
				$answer_data['uid'] = $uid;
				$answer_data['created'] = date('Y-m-d H:i:s');
				$answer_id = M('task_questions_answers')->data($answer_data)->add();

				$task = M('task')->where("task_id='$task_id'")->find();
				addlog('题目提交，任务名称：' . $task['name'].', 第'.$subject['o'].'题提交',false,3);

				M('member_task')->where("task_id='$task_id' AND uid='$uid'")->setInc('answered',1);

				$dosave = true;
			}
			if($dosave == true){

		        $questions = M('task_questions')->field("{$prefix}task_questions.*")
		            ->order("{$prefix}task_questions.topic asc")
		            ->where("{$prefix}task_questions.subject_id = ".$subject_id)
		            ->select();
				$question_id = $questions[0]['question_id'];

				$q1 = isset($_REQUEST['q1']) ? $_REQUEST['q1'] : '';
				$images = isset($_REQUEST['images']) ? $_REQUEST['images'] : '';

				$answer_item_id = 0;

				$answer_item_data['answer_item_id'] = $answer_item_id;
				$answer_item_data['answer_id'] = $answer_id;
				$answer_item_data['question_id'] = $question_id;
				$answer_item_data['subject_id'] = $subject_id;
				$answer_item_data['type'] = 'question_direct';
				$answer_item_data['value'] = $q1;
				$answer_item_data['itemValue'] = $q1;
				$answer_item_data['itemText'] = $images;

				$answer_item_data['topic'] = 0;
				$answer_item_data['item_value'] = $q1;
				M('task_questions_answers_item')->data($answer_item_data)->add();
			}
			$next = $this->getNext($subject['task_id'],$subject['o']);
			if(intval($next)){
				$this->redirect('Task/subject', ['subject_id' => $next]);
			}else{
				$this->redirect('Task/complete', ['task_id' => $task_id]);
			}
		}else{
			$this->error('提交错误!');
		}
	}
	public function submitsurvey(){
		$uid = $this->USER['uid'];

		$prefix = C('DB_PREFIX');

        $submitdata = isset($_REQUEST['submitdata']) ? $_REQUEST['submitdata'] : false;

        $subject_id = isset($_REQUEST['subject_id']) ? intval($_REQUEST['subject_id']) : 0;
		if($subject_id > 0){
			/*
			$data['subject_id'] = $subject_id;
			$data['submitdata'] = $submitdata;

			M('task_subjects')->data($data)->where("subject_id='{$subject_id}'")->save();
			*/

        	$subject = M('task_subjects')->where("{$prefix}task_subjects.subject_id='$subject_id'")->find();

        	$task_id = (int)$subject['task_id'];

	        $member_task = M('member_task')->where("task_id='$task_id' AND uid='$uid'")->find();

        	$subject_answer = M('task_questions_answers')
				->where("{$prefix}task_questions_answers.subject_id='$subject_id' AND {$prefix}task_questions_answers.uid='$uid'")
				->find();
        	if($subject_answer){
        		$answer_id = $subject_answer['answer_id'];
        		/*
        		if($subject_answer['status'] == -1){
        			M('task_questions_answers_item')->where("answer_id='$answer_id'")->delete();
        			M('task_questions_answers')->data(array('status' => 0,'created' => date('Y-m-d H:i:s')))->where("answer_id='{$answer_id}'")->save();
        			$dosave = true;
        		}
        		*/
        	}else{
				$answer_data['answer_id'] = 0;
				$answer_data['task_id'] = (int)$subject['task_id'];
				$answer_data['subject_id'] = $subject_id;
				$answer_data['uid'] = $uid;
				$answer_data['created'] = date('Y-m-d H:i:s');
				$answer_id = M('task_questions_answers')->data($answer_data)->add();

				$task = M('task')->where("task_id='$task_id'")->find();
				addlog('题目提交，任务名称：' . $task['name'].', 第'.$subject['o'].'题提交',false,3);

				M('member_task')->where("task_id='$task_id' AND uid='$uid'")->setInc('answered',1);

				$dosave = true;
			}
			if($dosave == true){
				$spChars = array("$", "}", "^", "|", "<");
				$spToChars = array("ξ", "｝", "ˆ", "¦", "&lt;");

				$g = explode($spChars[1],$submitdata);

				for ($d = 0; $d < count($g); $d++) {
					$DataArray[$d] = $this->set_string_to_dataNode($g[$d],$subject_id);
				}
				foreach($DataArray as $dataNode){
					if($dataNode->type == 'radio' || $dataNode->type == 'check' || $dataNode->type == 'question' || $dataNode->type == 'slider'|| $dataNode->type == 'matrix'|| $dataNode->type == 'fileupload'){
						$answer_item_id = 0;
						$answer_item_data['answer_item_id'] = $answer_item_id;
						$answer_item_data['answer_id'] = $answer_id;
						$answer_item_data['question_id'] = $dataNode->question_id;
						$answer_item_data['subject_id'] = $subject_id;
						$answer_item_data['type'] = $dataNode->type;
						$answer_item_data['value'] = $dataNode->value;
						$answer_item_data['itemValue'] = $dataNode->itemValue;
						$answer_item_data['itemText'] = $dataNode->itemText;

						$answer_item_data['topic'] = 0;
						$answer_item_data['item_value'] = $dataNode->value;
					}
					if($dataNode->type == 'radio' || $dataNode->type == 'question' || $dataNode->type == 'slider'|| $dataNode->type == 'fileupload'){
						M('task_questions_answers_item')->data($answer_item_data)->add();
					}

					if($dataNode->type == 'check' || $dataNode->type == 'matrix'){
						$selectlist = $dataNode->select;
						foreach($selectlist as $select){
							$answer_item_data['topic'] = $select['topic'];
							$answer_item_data['item_value'] = $select['item_value'];

							$answer_item_data['itemValue'] = $select['itemValue'];
							$answer_item_data['itemText'] = $select['itemText'];
							M('task_questions_answers_item')->data($answer_item_data)->add();
						}
					}
				}
			}
			$next = $this->getNext($subject['task_id'],$subject['o']);
			if(intval($next)){
				$this->ajaxReturn('10〒'.U('subject',array('subject_id'=>$next)),'JSON',1);
			}else{
				$this->ajaxReturn('10〒'.U('complete',array('task_id'=>$subject['task_id'])),'JSON',1);//ThinkPHP的固定格式，此框架，要求固定推回三个参数给前台
			}

		}else{

			$this->ajaxReturn('11〒提交错误','JSON',1);
		}
	}
	public function set_string_to_dataNode($r,$subject_id){
		//1$1^测试单选}2$1^测试多选1|2^测试多选2}3$测试问答题}4$1<1^备注1,2<2^备注2}5$1<1^注明1,2<2^注明2}6$1<1^11;2^22;3^33,2<1^1111;2^,3<3^3333}7$1<测试1111^2<测试2222}8$1<10^2<26}9$59

		$spChars = array("$", "}", "^", "|", "<");
		$spToChars = array("ξ", "｝", "ˆ", "¦", "&lt;");

		$f = new \stdClass();
		$d = explode($spChars[0],$r);

		$f->topic = $d[0];

		$task_question = $this->getQuestionByTopic($d[0],$subject_id);

		$f->type = $task_question['type'];
		$f->question_id = $task_question['question_id'];
		$f->tag = $task_question['tag'];
		$f->value = $d[1];

		switch ($f->type) {
            case "question":
				$f->itemValue = $f->value;
				$f->itemText = '';
                break;
            case "slider":
				$f->itemValue = $f->value;
				$f->itemText = '';
                break;
            case "fileupload":
				$f->itemValue = $f->value;
				$f->itemText = '';
                break;
            case "radio":
				//1^测试单选  radio  spChars[2]
				$item_value = explode($spChars[2],$f->value);
				$f->itemValue = $item_value[0];//答案
				$f->itemText = isset($item_value[1])?$item_value[1]:'';//备注
                break;
            case "check":
				//1^测试多选1|2^测试多选  check   spChars[2] spChars[3]
				$items = explode($spChars[3],$f->value);

				if(count($items)){
					$f->select = Array();
					for ($u = 0; $u < count($items); $u++) {
						$w = Array();
						$w = explode($spChars[2],$items[$u]);
						$f->select[] = array(
							"item_value"=>$items[$u],
							"topic"=>$u+1,
							"itemValue"=>$w[0],
							"itemText"=>isset($w[1])?$w[1]:''
						);
					}
				}
                break;
            case "matrix":
				//  6$1<1^11;2^22;3^33,2<1^1111;2^,3<3^3333
				//  6$1<1^11;2^22;3^33,2<1^1111;2^,3<3^3333}7$1<测试1111^2<测试2222}8$1<10^2<26}9$59

				if ($f->tag == "101" || $f->tag == "102" || $f->tag == "103") {
					//6$1<1^11;2^22;3^33,2<1^1111;2^,3<3^3333}
					//1$1<1^222,2<2^555
					//1<1;2,2<2;3,3<1;2
					$lines = explode(',',$f->value);

					if(count($lines)){
						$f->select = Array();
						for ($u = 0; $u < count($lines); $u++) {
							//1<1^11;2^22;3^33
							//1<1;2
							$w = explode($spChars[4],$lines[$u]);
							$items = explode(';',$w[1]);
							for ($t = 0; $t < count($items); $t++) {
								//1^11
								$T = explode($spChars[2],$items[$t]);
								$f->select[] = array(
									"item_value"=>$items[$t],
									"topic"=>$w[0],
									"itemValue"=>$T[0],
									"itemText"=>isset($T[1])?$T[1]:''
								);
							}

						}
					}
				}
				if ($f->tag == "201" || $f->tag == "202") {
					//1$1<11^2<38
					$items = explode($spChars[2],$f->value);

					if(count($items)){
						$f->select = Array();
						for ($u = 0; $u < count($items); $u++) {
							$w = Array();
							$w = explode($spChars[4],$items[$u]);
							$f->select[] = array(
								"item_value"=>$items[$u],
								"topic"=>$w[0],
								"itemValue"=>$w[1],
								"itemText"=>''
							);
						}
					}
				}
				break;
        }
		return $f;
	}
	public function getQuestionTitle($uid,$title,$task_id){
		$prefix = C('DB_PREFIX');
		$replace_str =  substr(strstr(strstr($title, '[['), ']]', true),2);

		if($replace_str<>''){
			$values = explode(".",$replace_str);
			if(count($values)==2){
				$o = $values[0];
				$subject = $this->getSubjectByOrder($task_id,$o);
				$subject_id = $subject['subject_id'];

				$topic = $values[1];
				$question = $this->getQuestionByTopic($topic,$subject_id);
				$question_id = $question['question_id'];

				$item_title = $this->getQuestionItemTitleByAnswer($uid,$subject_id,$question_id);
				if(empty($item_title)){
					return $title;
				}else{
					return str_replace('[['.$replace_str.']]',$item_title,$title);
				}
			}else{
				return $title;
			}
		}else{
			return $title;
		}


	}
    public function getQuestionItemTitleByAnswer($uid,$subject_id,$question_id){
        $prefix = C('DB_PREFIX');
        $answer = M('task_questions_answers')->field("{$prefix}task_questions_answers.*")
            ->where("{$prefix}task_questions_answers.uid='$uid' AND {$prefix}task_questions_answers.subject_id='$subject_id'")
            ->find();

        $question_answers = M('task_questions_answers_item')->field("{$prefix}task_questions_answers_item.itemValue as itemvalue,{$prefix}task_questions_answers_item.itemText as itemtext")
            ->order("{$prefix}task_questions_answers_item.answer_item_id asc")
            ->where("({$prefix}task_questions_answers_item.answer_id = '".$answer['answer_id']."' AND {$prefix}task_questions_answers_item.question_id = '".$question_id."')")
            ->select();

        $item_title = '';

        foreach($question_answers as $question_answer){
	        $questions_item = M('task_questions_item')->field("{$prefix}task_questions_item.item_title")
	            ->where("({$prefix}task_questions_item.item_value = '".$question_answer['itemvalue']."' AND {$prefix}task_questions_item.question_id = '".$question_id."')")
	            ->find();

	        $item_title = $questions_item['item_title'];
        }
        return $item_title;

    }
	public function getSubjectByOrder($task_id,$o){
		$prefix = C('DB_PREFIX');
        $subject = M('task_subjects')->field("{$prefix}task_subjects.*")
            ->where("task_id='$task_id' and o='$o'")->find();

		return $subject;
	}
	public function getQuestionByTopic($topic,$subject_id){
		$prefix = C('DB_PREFIX');
        $task_question = M('task_questions')->field("{$prefix}task_questions.*")
            ->where("subject_id='$subject_id' and topic='$topic'")->find();

		return $task_question;
	}
	public function get_string_from_list($list){
		$qstr = 'false§true¤page§1§§§';

		foreach($list as $item){
			$item['requir'] = $item['requir']=='1'?'true':'false';
			$item['norepeat'] = $item['norepeat']=='1'?'true':'false';
			$item['hasjump'] = $item['hasjump']=='1'?'true':'false';
			$item['needOnly'] = $item['needOnly']=='1'?'true':'false';
			$item['hasList'] = $item['hasList']=='1'?'true':'false';
			switch ($item['type']) {
				case "question":
					$qstr .= '¤'.$item['type'].'§'.$item['topic'].'§'.$item['height'].'§'.$item['maxword'].'§'.$item['requir'].'§'.$item['norepeat'].'§'.$item['hasjump'].'§'.$item['anytimejumpto'].'§'.$item['verify'].'§'.$item['needOnly'].'§'.$item['hasList'].'§'.$item['listId'].'§'.$item['minword'];
					break;
				case "slider":
					$qstr .= '¤'.$item['type'].'§'.$item['topic'].'§'.$item['requir'].'§'.$item['minvalue'].'§'.$item['maxvalue'].'§'.$item['hasjump'].'§'.$item['anytimejumpto'];
					break;
				case "fileupload":
					$qstr .= '¤'.$item['type'].'§'.$item['topic'].'§'.$item['requir'].'§'.$item['maxsize'].'§'.$item['ext'].'§'.$item['hasjump'].'§'.$item['anytimejumpto'];
					break;
				case "radio":
				case "check":
				case "matrix":
					$qstr .= '¤'.$item['type'].'§'.$item['topic'].'§'.$item['numperrow'].'§'.$item['hasvalue'].'§'.$item['hasjump'].'§'.$item['anytimejumpto'];

					if ($item['type'] == "check" || ($item['type']== "matrix" && $item['tag'] == "102")) {
						$qstr .= '§'.$item['requir'].','.$item['lowlimit'].','.$item['uplimit'];
					} else {
						$qstr .= '§'.$item['requir'];
					}

					$qstr .='§'.$item['isTouPiao'].'§'.$item['verify'].'§'.intval($item['tag']).'§'.$item['referTopic'].'§'.$item['referedTopics'];
					if(count($item['select'])){
						foreach($item['select'] as $question_item){
							$question_item['item_radio'] = $question_item['item_radio']=='1'?'true':'false';
							$question_item['item_huchi'] = $question_item['item_huchi']=='1'?'true':'false';

							$qstr .= '§'.$question_item['item_radio'].'〒'.$question_item['item_value'].'〒'.$question_item['item_jump'].'〒'.$question_item['item_huchi'];
						}
					}
					break;

				default:
					break;
			}
		}

		return $qstr;
	}
	public function getFirstSubject($task_id){
		$prefix = C('DB_PREFIX');
        $subject = M('task_subjects')->field("{$prefix}task_subjects.*")
            ->where("{$prefix}task_subjects.task_id='$task_id'")
            ->order("{$prefix}task_subjects.o ASC")
            ->find();

        return isset($subject['subject_id'])?$subject['subject_id']:0;
	}
	public function getPrev($task_id,$o){
		$uid = $this->USER['uid'];
		$prefix = C('DB_PREFIX');
        $tasksubject = M('task_subjects')->field("{$prefix}task_subjects.*")
            ->where("{$prefix}task_subjects.o<'$o' AND {$prefix}task_subjects.task_id='$task_id'")
            ->order("{$prefix}task_subjects.o DESC")
            ->find();

        $subject_id = $tasksubject['subject_id'];
        $answer = M('task_questions_answers')->field("{$prefix}task_questions_answers.*")
            ->where("{$prefix}task_questions_answers.uid='$uid' AND {$prefix}task_questions_answers.subject_id='$subject_id'")
            ->find();
        if($answer['skip'] == 1){
        	$subject_id = $this->getPrev($task_id,$tasksubject['o']);
        }
        return $subject_id;

	}
	public function getNext($task_id,$o){
		$uid = $this->USER['uid'];

		$prefix = C('DB_PREFIX');

		$subject_id = 0;

        $currentsubject = M('task_subjects')->field("{$prefix}task_subjects.*")
            ->where("{$prefix}task_subjects.o='$o' AND {$prefix}task_subjects.task_id='$task_id'")
            ->order("{$prefix}task_subjects.o ASC")
            ->find();

        $answer = M('task_questions_answers')->field("{$prefix}task_questions_answers.*")
            ->where("{$prefix}task_questions_answers.uid='$uid' AND {$prefix}task_questions_answers.subject_id='".$currentsubject['subject_id']."'")
            ->find();
        if($answer){
	        $tasksubject = M('task_subjects')->field("{$prefix}task_subjects.*")
	            ->where("{$prefix}task_subjects.o>'$o' AND {$prefix}task_subjects.task_id='$task_id'")
	            ->order("{$prefix}task_subjects.o ASC")
	            ->find();

	        $subject_id = $tasksubject['subject_id'];
        }

        return $subject_id;
	}
    public function getUserSubjectQuestionAnswer($uid,$subject_id,$question_id){
        $prefix = C('DB_PREFIX');
        $answer = M('task_questions_answers')->field("{$prefix}task_questions_answers.*")
            ->where("{$prefix}task_questions_answers.uid='$uid' AND {$prefix}task_questions_answers.subject_id='$subject_id'")
            ->find();

        $question_answers = M('task_questions_answers_item')->field("{$prefix}task_questions_answers_item.itemValue as itemvalue,{$prefix}task_questions_answers_item.itemText as itemtext")
            ->order("{$prefix}task_questions_answers_item.answer_item_id asc")
            ->where("({$prefix}task_questions_answers_item.answer_id = '".$answer['answer_id']."' AND {$prefix}task_questions_answers_item.question_id = '".$question_id."')")
            ->select();

        foreach($question_answers as $i=>$question_answer){
	        $questions_item = M('task_questions_item')->field("{$prefix}task_questions_item.item_id")
	            ->where("({$prefix}task_questions_item.item_value = '".$question_answer['itemvalue']."' AND {$prefix}task_questions_item.question_id = '".$question_id."')")
	            ->find();
	        $question_answers[$i]['item_id'] = intval($questions_item['item_id']);
        }

        return $question_answers;

    }
	public function getTotalAnswers($task_id,$uid){
		$prefix = C('DB_PREFIX');
        $total = M('task_questions_answers')->field("{$prefix}task_questions_answers.*")
            ->where("{$prefix}task_questions_answers.uid='$uid' AND {$prefix}task_questions_answers.task_id='$task_id'")
            ->count();

        return $total;

	}
	public function getSubjectList($task_id,$uid){
		$prefix = C('DB_PREFIX');

        $subjectlist = M('task_subjects')->field("{$prefix}task_subjects.*")
            ->order("o asc")
            ->where("task_id='". $task_id."'")
            ->select();

        foreach($subjectlist as $i=>$subject){
        	if($uid){
        		if($uid == -1){
        			$subjectlist[$i]['answerd'] = "true";
        		}else{
			        $answer = M('task_questions_answers')->field("{$prefix}task_questions_answers.*")
			            ->where("{$prefix}task_questions_answers.uid='$uid' AND {$prefix}task_questions_answers.subject_id='".$subject['subject_id']."'")
			            ->find();
			        if($answer){
			        	if($answer['skip'] == 1){
			        		$subjectlist[$i]['answerd'] = "skip";
			        	}else{
				        	$subjectlist[$i]['answerd'] = "true";
				        }
			        }else{
			        	$subjectlist[$i]['answerd'] = "false";
			        	unset($subjectlist[$i]);
			        }
			    }
		    }else{
				$subjectlist[$i]['answerd'] = "false";
				unset($subjectlist[$i]);
		    }

        }
        return $subjectlist;

	}
    public function getAnswers($answer_id){
        $prefix = C('DB_PREFIX');
        $answer =  M('task_questions_answers')->field("{$prefix}task_questions_answers.*")
            ->where("{$prefix}task_questions_answers.answer_id=$answer_id")
            ->find();

        $questions = M('task_questions')->field("{$prefix}task_questions.*")
            ->order("{$prefix}task_questions.topic asc")
            ->where("{$prefix}task_questions.subject_id = ".$answer['subject_id'])
            ->select();
        foreach($questions as $a=>$question){
            if($question['type'] == 'radio' || $question['type'] == 'check' ||($question['type'] == 'matrix' && ($question['tag'] !='201' && $question['tag'] !='202'))){
	            $question_answers = M('task_questions_answers_item')->field("{$prefix}task_questions_answers_item.*,{$prefix}task_questions_item.item_title as itemvalue")
	                ->join("{$prefix}task_questions_item ON {$prefix}task_questions_item.question_id = {$prefix}task_questions_answers_item.question_id")
	                ->order("{$prefix}task_questions_answers_item.answer_item_id asc")
	                ->where("({$prefix}task_questions_item.item_value={$prefix}task_questions_answers_item.itemValue AND {$prefix}task_questions_answers_item.answer_id = '".$answer['answer_id']."' AND {$prefix}task_questions_answers_item.question_id = '".$question['question_id']."')")
	                ->select();

            }else{
	            $question_answers = M('task_questions_answers_item')->field("{$prefix}task_questions_answers_item.*")
	                ->order("{$prefix}task_questions_answers_item.answer_item_id asc")
	                ->where("{$prefix}task_questions_answers_item.answer_id = '".$answer['answer_id']."' AND {$prefix}task_questions_answers_item.question_id = '".$question['question_id']."'")
	                ->select();
	            foreach ($question_answers as $key => $answer) {
	            	if($answer['type'] == 'question_direct'){
	            		$images = explode('|',$answer['itemtext']);
	            		$question_answers[$key]['images'] = $images;
	            	}
	            }
	        }
            $questions[$a]['answers']  = $question_answers;
        }
        $question_answers = $questions;

        return $question_answers;

    }
    public function getTotalSubjects($task_id){
    	$prefix = C('DB_PREFIX');
    	$count_subjects = M('task_subjects')->field("{$prefix}task_subjects.*")
			->where("task_id='$task_id'")
			->count();
		return $count_subjects;//题目数量
    }
    public function getTotalQuestions($task_id){
    	$prefix = C('DB_PREFIX');
        $count_questions = M('task_questions')->field("{$prefix}task_questions.*")
            ->join("{$prefix}task_subjects ON {$prefix}task_subjects.subject_id = {$prefix}task_questions.subject_id")
            ->where("{$prefix}task_subjects.task_id='$task_id'")
            ->count();
		return $count_questions;//问题数量
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
    public function getMemberTaskStatus($task_id,$uid){
    	$prefix = C('DB_PREFIX');
    	$member_task = M('member_task')->where("task_id='$task_id' AND uid='$uid'")->find();
    	if($member_task){
    		return $member_task['status'];//用户任务状态
    	}else{
    		return 0;
    	}
    }
    public function timeLeft($end){
        static $chunks = array(array(86400,'天'),array(3600 ,'小时'),array(60,'分'));

        $diff = intval(strtotime($end)-time());
        if($diff){
	        $count =0;
	        $since = '';
	        for($i=0;$i<count($chunks);$i++) {
	            if($diff>=$chunks[$i][0]) {
	                $num   =  floor($diff/$chunks[$i][0]);
	                $since .= sprintf('%d'.$chunks[$i][1],$num);
	                $diff =  (int)($diff-$chunks[$i][0]*$num);
	                $count++;
	            }
	        }
	        return $since;
        }else {
        	return 0;
        }
    }
    public function uploadqiniu(){
    	$this->display('uploadqiniu');
    }
    public function uploadfile(){
     	$uid = $this->USER['uid'];
     	$prefix = C('DB_PREFIX');
     	$data = array();
     	if($uid){
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
					'rootPath' => './Public/',
					'savePath' => 'upload/uploadfile/',
	                'subName'  =>  '',
	            ));
	            $info = $upload->upload($_FILES);
	            if(!$info) {// 上传错误提示错误信息
	                // $error = $upload->getError();
	                $data['file'] = I('post.file', '', 'strip_tags');
	                // image1
	                //$this->error($error);
	            }else{// 上传成功

	                foreach ($info as $item) {
	                    if($item['key'] == 'file'){
	                    	$data['fileName'] = $item['savename'];
	                        $data['filePath'] = $upload->rootPath.$item['savepath'].$item['savename'];
	                    }

	                }

	            }
	        }
     	}
     	$this->ajaxReturn($data,'JSON',1);
    }
    public function adminPreview($task_id = 0,$subject_id = 0){
     	$uid = $this->USER['uid'];
		$prefix = C('DB_PREFIX');
		if($this->USER['admin'] == 1){
	        $task_subjects = M('task_subjects');

	        if($task_id){
	        	$subjects = M('task_subjects')
		        	->where("{$prefix}task_subjects.task_id='$task_id'")
		        	->order("{$prefix}task_subjects.o ASC")
		        	->select();
	        	$subject_id = $subjects[0]['subject_id'];
	        }

			if ($subject_id) {
				$this->assign('subject_id', $subject_id);

		        $tasksubject = M('task_subjects')->field("{$prefix}task_subjects.*,{$prefix}task.title as tasktitle")
		            ->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
		            ->where("subject_id='$subject_id'")->find();
				$this->assign('tasksubject', $tasksubject);

		        $task_id = intval($tasksubject['task_id']);

		        $subjectlist = $this->getSubjectList($task_id,-1);
				$this->assign('subjectlist', $subjectlist);

	        	$subject_answers = M('task_questions_answers')->field("{$prefix}task_questions_answers.*,{$prefix}member.realname,{$prefix}member.head")
	        		->join("{$prefix}member on {$prefix}member.uid={$prefix}task_questions_answers.uid")
		        	->where("{$prefix}task_questions_answers.subject_id='$subject_id'")
		        	->select();
		        foreach($subject_answers as $o=>$answer){
		        	$subject_answers[$o]['question_answers'] = $this->getAnswers($answer['answer_id']);
		        	$subject_answers[$o]['islike'] = $this->islike($answer['answer_id']);
		        	$subject_answers[$o]['countlike'] = $this->getCountLike($answer['answer_id']);

		            $remarks = M('task_questions_answers_remark')->field("{$prefix}task_questions_answers_remark.*,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.head,{$prefix}member.admin")
		                ->join("{$prefix}member ON {$prefix}task_questions_answers_remark.uid = {$prefix}member.uid")
		                ->order("{$prefix}task_questions_answers_remark.remark_id ASC")
		                ->where("{$prefix}task_questions_answers_remark.answer_id = ".$answer['answer_id'])
		                ->select();

		            $subject_answers[$o]['remarks'] = $remarks;

		            $comments = M('task_questions_answers_comment')->field("{$prefix}task_questions_answers_comment.*,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.head,{$prefix}member.admin")
		                ->join("{$prefix}member ON {$prefix}task_questions_answers_comment.uid = {$prefix}member.uid")
		                ->where("{$prefix}task_questions_answers_comment.answer_id = ".$answer['answer_id'])
		                ->select();

		            $subject_answers[$o]['comments'] = $comments;
		        }

	            $this->assign('subject_answers', $subject_answers);//其他用户回答


		        //问题列表
				$order = "topic asc";
				$where = " {$prefix}task_questions.subject_id = $subject_id";

				$task_questions = M('task_questions');

				$count = M('task_questions')->field("{$prefix}task_questions.*")
					->order($order)
					->where($where)
					->count();

				$list = M('task_questions')->field("{$prefix}task_questions.*")
					->order($order)
					->where($where)
					->select();


				foreach($list as $i=>$item){
					if($item['type'] == 'radio'||$item['type'] == 'check'||$item['type'] == 'matrix'){

						$order = "item_id asc";
						$where = " {$prefix}task_questions_item.question_id = ".$item['question_id'];

						$selectlist =  M('task_questions_item')->field("{$prefix}task_questions_item.*")
							->order($order)
							->where($where)
							->select();

						foreach($selectlist as $select){
							$list[$i]['select'][] = array(
								"item_id"=>$select['item_id'],
								"item_title"=>$select['item_title'],
								"item_radio"=>$select['item_radio'],
								"item_value"=>$select['item_value'],
								"item_jump"=>$select['item_jump'],
								"item_tb"=>$select['item_tb'],
								"item_tbr"=>$select['item_tbr'],
								"item_img"=>$select['item_img'],
								"item_imgtext"=>$select['item_imgtext'],
								"item_desc"=>$select['item_desc'],
								"item_label"=>$select['item_label'],
								"item_huchi"=>$select['item_huchi']
							);
						}
						if($item['type'] == 'matrix'){
							$list[$i]['titlerows'] = explode("\n",$item["rowtitle"]);
						}
					}

				}
				$this->assign('list', $list);
				$this->assign('count', $count);

				$surveydata = $this->get_string_from_list($list);

				$this->assign('surveydata', $surveydata);

				$prev = $this->getPreviewPrev($tasksubject['task_id'],$tasksubject['o']);
				$this->assign('prev', intval($prev));

				$next = $this->getPreviewNext($tasksubject['task_id'],$tasksubject['o']);
				$this->assign('next', intval($next));
	        }
	        $task = $this->getTaskInfo($task_id);
	        $this->assign('task', $task);

			$this->display('preview');
		}
    }
	public function getPreviewPrev($task_id,$o){
		$prefix = C('DB_PREFIX');

		$uid = $this->USER['uid'];
    	$task_id = intval($task_id);
    	$subject_id = 0;
    	if($this->USER['admin'] == 1){
	        $tasksubject = M('task_subjects')->field("{$prefix}task_subjects.*")
	            ->where("{$prefix}task_subjects.o<'$o' AND {$prefix}task_subjects.task_id='$task_id'")
	            ->order("{$prefix}task_subjects.o DESC")
	            ->find();

	        $subject_id = $tasksubject['subject_id'];
		}
        return $subject_id;

	}
	public function getPreviewNext($task_id,$o){
		$prefix = C('DB_PREFIX');

		$uid = $this->USER['uid'];
    	$task_id = intval($task_id);
    	$subject_id = 0;
    	if($this->USER['admin'] == 1){
	        $tasksubject = M('task_subjects')->field("{$prefix}task_subjects.*")
	            ->where("{$prefix}task_subjects.o>'$o' AND {$prefix}task_subjects.task_id='$task_id'")
	            ->order("{$prefix}task_subjects.o ASC")
	            ->find();

	        $subject_id = $tasksubject['subject_id'];
		}
        return $subject_id;
	}
	public function islike($answer_id){
		$prefix = C('DB_PREFIX');

		$uid = $this->USER['uid'];

        $islike = M('task_questions_answers_like')->field("{$prefix}task_questions_answers_like.*")
            ->where("{$prefix}task_questions_answers_like.answer_id='$answer_id' AND {$prefix}task_questions_answers_like.uid='$uid'")
            ->count();

        return $islike;
	}
	public function getCountLike($answer_id){
		$prefix = C('DB_PREFIX');

		$uid = $this->USER['uid'];

        $total = M('task_questions_answers_like')->field("{$prefix}task_questions_answers_like.*")
            ->where("{$prefix}task_questions_answers_like.answer_id='$answer_id'")
            ->count();

        return $total;
	}
	public function dolike(){
		$prefix = C('DB_PREFIX');

		$uid = $this->USER['uid'];
		$answer_id = I('get.answer_id', '', 'intval');
		$subject_id = I('get.subject_id', '', 'intval');
        if (empty($answer_id)) {
            $this->error('参数错误！');
        }
        if (empty($subject_id)) {
            $this->error('参数错误！');
        }
		$islike = M('task_questions_answers_like')->field("{$prefix}task_questions_answers_like.*")
            ->where("{$prefix}task_questions_answers_like.answer_id='$answer_id' AND {$prefix}task_questions_answers_like.uid='$uid'")
            ->count();
        if($islike){
			$this->error('已点赞');
        }else{
	        $data['answer_id'] = $answer_id;
	        $data['subject_id'] = $subject_id;
	        $data['uid'] = $uid;
	        $data['status'] = '1';
	        $data['datetime'] = time();
	        $answers_like_id = M('task_questions_answers_like')->data($data)->add();
	        M('task_questions_answers')->where("answer_id='$answer_id'")->setInc("like_hit");
	        //addlog('点赞，ID：' . $answers_like_id);
	        $this->success('点赞成功！');
        }
    }
    public function docomment(){
    	$uid = $this->USER['uid'];
        $answer_id = I('post.answer_id', '', 'intval');
        $subject_id = I('post.subject_id', '', 'intval');
        if (empty($answer_id)) {
            $this->error('参数错误！');
        }
        if (empty($subject_id)) {
            $this->error('参数错误！');
        }

        $comment = I('post.comment', '', 'strip_tags');
        if (empty($comment)) {
            $this->error('评论不能为空');
        }

        $data['answer_id'] = $answer_id;
        $data['subject_id'] = $subject_id;
        $data['uid'] = $uid;
        $data['comment'] = $comment;
        $data['status'] = '1';
        $data['datetime'] = time();

        $remark_id = M('task_questions_answers_comment')->data($data)->add();
        addlog('添加评论，ID：' . $remark_id);
        $this->success('评论成功！');

    }
    public function doremark(){
    	$uid = $this->USER['uid'];
    	$prefix = C('DB_PREFIX');

        $answer_id = I('post.answer_id', '', 'intval');
        $subject_id = I('post.subject_id', '', 'intval');
        if (empty($answer_id)) {
            $this->error('参数错误！');
        }
        if (empty($subject_id)) {
            $this->error('参数错误！');
        }
        $remark = I('post.remark', '', 'strip_tags');
        if (empty($remark)) {
            $this->error('追问不能为空');
        }
    	if($this->USER['admin'] == 1){
	        $data['answer_id'] = $answer_id;
	        $data['subject_id'] = $subject_id;
	        $data['uid'] = $uid;
	        $data['remark'] = $remark;
	        $data['status'] = '1';
	        $data['type'] = '1';
	        $data['datetime'] = time();

	        $remark_id = M('task_questions_answers_remark')->data($data)->add();

        	$answer = M('task_questions_answers')->field("{$prefix}task_questions_answers.*,{$prefix}task.title as task_title")
	        	->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_questions_answers.task_id")
	        	->where("{$prefix}task_questions_answers.answer_id='$answer_id'")
	        	->find();

            $message_data = array();
            $message_data['uid'] = $answer['uid'];
            $message_data['type'] = 2;
            $message_data['message'] = '您的任务《'.$answer['task_title'].'》有新追问,请及时回复！';
            $message_data['status'] = 0;
            $message_data['created'] = time();
            M('member_message')->data($message_data)->add();

	        addlog('添加追问，ID：' . $remark_id);
	        $this->success('追问成功！');
        }else{
        	$this->error('没有权限添加追问');
        }

    }
    public function doAnswerRemark(){
    	$prefix = C('DB_PREFIX');

        $uid = $this->USER['uid'];
        $remark_id = I('post.remark_id', '', 'intval');
        $reply = I('post.reply', '', 'strip_tags');
        if (empty($remark_id)) {
            $this->error('参数错误！');
        }

        if (empty($reply)) {
            $this->error('回答追问不能为空');
        }
        $remark_item = M('task_questions_answers_remark')->field("{$prefix}task_questions_answers_remark.*")
            ->where("{$prefix}task_questions_answers_remark.remark_id='$remark_id'")
            ->find();

        $data['answer_id'] = $remark_item['answer_id'];
        $data['subject_id'] = $remark_item['subject_id'];
        $data['uid'] = $uid;
        $data['remark'] = $reply;
        $data['status'] = '2';
        $data['type'] = '2';
        $data['datetime'] = time();

	    $reply_id = M('task_questions_answers_remark')->data($data)->add();

        $reply_data['ruid'] = $uid;
        $reply_data['reply'] = $reply;
        $reply_data['status'] = '2';
        $reply_data['reply_datetime'] = time();
		M('task_questions_answers_remark')->data($reply_data)->where("remark_id='$remark_id'")->save();
		addlog('回复追问，ID：' . $remark_id);
		$this->success('追问回复成功！');
    }
	public function getAnswersRemarks(){
		$uid = $this->USER['uid'];
		$prefix = C('DB_PREFIX');
        if($this->USER['admin'] == 1){
            $answers_remarks = M('task_questions_answers_remark')->field("{$prefix}task_questions_answers_remark.*,{$prefix}task_subjects.title as subject_title,{$prefix}task_subjects.o as subject_o")
            	->join("{$prefix}task_questions_answers ON {$prefix}task_questions_answers.answer_id = {$prefix}task_questions_answers_remark.answer_id")
            	->join("{$prefix}task_subjects ON {$prefix}task_subjects.subject_id = {$prefix}task_questions_answers.subject_id")
                ->order("{$prefix}task_questions_answers_remark.remark_id DESC")
                ->where("{$prefix}task_questions_answers_remark.status=1")
                ->select();
        }else{
            $answers_remarks = M('task_questions_answers_remark')->field("{$prefix}task_questions_answers_remark.*,{$prefix}task_subjects.title as subject_title,{$prefix}task_subjects.o as subject_o")
            	->join("{$prefix}task_questions_answers ON {$prefix}task_questions_answers.answer_id = {$prefix}task_questions_answers_remark.answer_id")
            	->join("{$prefix}task_subjects ON {$prefix}task_subjects.subject_id = {$prefix}task_questions_answers.subject_id")
                ->order("{$prefix}task_questions_answers_remark.remark_id DESC")
                ->where("{$prefix}task_questions_answers_remark.status=1 AND {$prefix}task_questions_answers.uid='$uid'")
                ->select();
		}

		return $answers_remarks;
	}
	public function getTaskInfo($task_id){
		$uid = $this->USER['uid'];
		$prefix = C('DB_PREFIX');

        $task = M('task')->where("task_id='$task_id'")->find();

		$task['count_subjects'] = $this->getTotalSubjects($task_id);//题目数量

        $task['count_questions'] = $this->getTotalQuestions($task_id);//问题数量

		$task['count_users_all'] = $this->getTotalUsers($task_id);//参与人数

		$task['leftday'] = intval((strtotime($task['end'])-time())/60/60/24);


        if($this->USER['admin'] == 1){
        	$count_completed_subjects = 0;
        }else{
        	$member_task = M('member_task')->where("task_id='$task_id' AND uid='$uid'")->find();
			$count_completed_subjects = (int)$member_task['answered'];
		}
		$task['count_completed_subjects'] = $count_completed_subjects; //已完成

		return $task;
	}
	public function showattachment(){
		$uid = $this->USER['uid'];
		$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : '';

		$prefix = C('DB_PREFIX');
		$error = 0;
		if($subject_id<>''){
        	$subject = M('task_subjects')->where("subject_id='$subject_id'")->find();

        	$attachment_view = $subject['attachment_view'];
        	if($subject['attachment_status'] && $attachment_view){
		        if($this->USER['admin'] == 1){

		        }else{
		        	$member_subject = M('member_subject')->where("subject_id='$subject_id' AND uid='$uid'")->find();
		        	if($member_subject){
		        		if($attachment_view <= $member_subject['view']){
		        			$error = 3;//附件显示结束
		        		}else{
							M('member_subject')->where("uid='$uid' AND subject_id='$subject_id'")->setInc('view',1);
						}
		        	}else{
				        $attachment_data['uid'] = $uid;
				        $attachment_data['subject_id'] = $subject_id;
				        $attachment_data['view'] = 1;
						M('member_subject')->data($attachment_data)->add();
		        	}
		        }

			}else{
	        	$error = 2;//未找到附件
	        }
	        $subject['attachment_showtime'] = $subject['attachment_showtime']*1000;
			$this->assign('subject', $subject);
		}else{
			$error = 1;//参数错误
		}
		$this->assign('error', $error);
		$this->display('attachment');
	}
}