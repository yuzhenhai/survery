<?php
/**
 *
 * Copyright: Bodasoft
 * Author   ：dowei<douwei@bodait.com>
 * Date     ：2018-1-23
 * Version  ：1.0.0
 * Desc     ：任务控制器。
 *
 **/

namespace Admin\Controller;
use Think\Upload;

use PHPExcel_IOFactory;
use PHPExcel;

class TaskController extends ComController
{
    public function index()
    {

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $taskgroup_id = isset($_GET['taskgroup_id']) ? $_GET['taskgroup_id'] : '';

        $state = isset($_GET['state']) ? $_GET['state'] : '';

		$prefix = C('DB_PREFIX');

		$order = '';
        $where = "{$prefix}task.type = 1 ";
        if ($field <> '') {
            $where .= " AND {$prefix}task.identifier LIKE '%$field%'";
        }
        if ($taskgroup_id <> '') {
            $where .= " AND ({$prefix}task.taskgroup_id = '$taskgroup_id')";
        }
        if ($keyword <> '') {
            $where .= " AND ({$prefix}task.name LIKE '%$keyword%' OR {$prefix}task.title LIKE '%$keyword%')";
        }
        if($state ==''){
            $where .= " AND ({$prefix}task.state != '5')";
        }else{
            $where .= " AND ({$prefix}task.state = '$state')";
        }
		$task = M('task');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $task->field("{$prefix}task.*")
            ->order($order)
            ->where($where)
            ->count();

        $list = $task->field("{$prefix}task.*")
            ->order($order)
            ->where($where)
            ->limit($offset . ',' . $pagesize)
            ->select();

		//调研对象组
        $research_group = M('auth_group')->field("{$prefix}auth_group.id,{$prefix}auth_group.title")->where('type = 2 and pid=8')->select();

		//项目标签
		$project_tags = M('tagspackage')->field("{$prefix}tagspackage.id,{$prefix}tagspackage.name")->select();

		foreach($list as $i=>$item){
			$research_group_array = array();
			foreach($research_group as $group){
				if(in_array($group['id'],explode(',',$item['research_group']))){
					$research_group_array[] = $group['title'];
				}
			}
			$list[$i]['research_group']= implode(',',$research_group_array);

			//项目标签
            $tags_array = array();
            if($item['project_tags']){
    			$taglist = M('tags')->field("distinct {$prefix}tags.*")
    				->join("{$prefix}tag_package_groups ON {$prefix}tag_package_groups.taggroups_id = {$prefix}tags.groupid")
    				->where("{$prefix}tags.status=1 AND {$prefix}tag_package_groups.tagspackage_id in (".$item['project_tags'].")")
    				->select();


    			foreach($taglist as $tag){
    					$tags_array[] = $tag['title'];
    			}
            }
			$list[$i]['tags_array']= $tags_array;

			$count_subjects = M('task_subjects')->field("{$prefix}task_subjects.*")
				->where("task_id='".$item['task_id']."'")
				->count();
			$list[$i]['count_subjects']= $count_subjects;//题目数量

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
            $count_users_pending = M('member_task')->field("{$prefix}member_task.id")
                    ->where("{$prefix}member_task.task_id = ".$item['task_id']." AND status = 1")
                    ->count();
			$list[$i]['count_users_all'] = $count_users_all;//参与人数
			$list[$i]['count_users_completed'] = $count_users_completed;//已完成
			$list[$i]['count_users_pending'] = $count_users_pending;//进行中

            $count_remark_all = M('task_questions_answers_remark')->field("{$prefix}task_questions_answers_remark.remarka_id")
                    ->join("{$prefix}task_subjects ON {$prefix}task_subjects.subject_id = {$prefix}task_questions_answers_remark.subject_id")
                    ->where("{$prefix}task_subjects.task_id = ".$item['task_id'])
                    ->count();
            $count_remark_pending = M('task_questions_answers_remark')->field("{$prefix}task_questions_answers_remark.remarka_id")
                    ->join("{$prefix}task_subjects ON {$prefix}task_subjects.subject_id = {$prefix}task_questions_answers_remark.subject_id")
                    ->where("{$prefix}task_subjects.task_id = ".$item['task_id'] ." AND {$prefix}task_questions_answers_remark.status = 1 AND {$prefix}task_questions_answers_remark.type = 1")
                    ->count();
            $count_remark_done = M('task_questions_answers_remark')->field("{$prefix}task_questions_answers_remark.remarka_id")
                    ->join("{$prefix}task_subjects ON {$prefix}task_subjects.subject_id = {$prefix}task_questions_answers_remark.subject_id")
                    ->where("{$prefix}task_subjects.task_id = ".$item['task_id'] ." AND {$prefix}task_questions_answers_remark.status = 2 AND {$prefix}task_questions_answers_remark.type = 1")
                    ->count();

            $list[$i]['count_remark_all'] = $count_remark_all;//追问数
            $list[$i]['count_remark_done'] = $count_remark_done;//回答数
            $list[$i]['count_remark_pending'] = $count_remark_pending;//未回答数
		}

		$page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->display();
    }

    public function del()
    {
        $ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : false;
        if (!$ids) {
            $this->error('请勾选删除任务！');
        }

        if (is_array($ids)) {
            foreach ($ids as $k => $v) {
                $ids[$k] = intval($v);
            }
            if (!$ids) {
                $ids = implode(',', $ids);
            }
        }
        $map['task_id'] = array('in', $ids);

        if (M('task')->where($map)->delete()) {
            M('task_subjects')->where($map)->delete();
            M('task_questions')->where($map)->delete();

            addlog('删除任务ID：' . implode(',', $ids));
            $this->success('任务删除成功！');
        } else {
            $this->error('参数错误！');
        }
    }

    public function edit($task_id = 0)
    {
		$prefix = C('DB_PREFIX');
        $task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;
        $task = M('task');
        $currenttask = $task->where("task_id='$task_id'")->find();
        if (!$currenttask) {
            $this->error('参数错误！');
        }


		$have_research_group = array();
		$have_researcher = array();
		$have_customer_group = array();
		$have_project_tags = array();

		if($currenttask){
			$have_research_group = explode(',',$currenttask['research_group']);
			$have_researcher = explode(',',$currenttask['researcher']);
			$have_customer_group = explode(',',$currenttask['customer_group']);
			$have_project_tags = explode(',',$currenttask['project_tags']);
		}
        $this->assign('have_research_group', $have_research_group);
        $this->assign('have_researcher', $have_researcher);
        $this->assign('have_customer_group', $have_customer_group);
        $this->assign('have_project_tags', $have_project_tags);

        $have_research_groups = M('auth_group')->field("{$prefix}auth_group.title")->where('type = 2')->getField('title',true);
        $this->assign('have_research_groups', $have_research_groups);

		//调研对象组
        $research_group = M('auth_group')->field("{$prefix}auth_group.id,{$prefix}auth_group.title")->where('type = 2 and pid=8')->select();
        $this->assign('research_group', $research_group);


		//研究员
		$order = "";
        $where = " {$prefix}auth_group.id= 9";

        $researcher = M('member')->field("{$prefix}member.uid,{$prefix}member.user")
            ->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
            ->join("{$prefix}auth_group ON {$prefix}auth_group.id = {$prefix}auth_group_access.group_id")
            ->order($order)
            ->where($where)
            ->select();
        $this->assign('researcher', $researcher);

		//客户组
		$order = "";
        $where = " g.id= 10";

        $customer_group = M('auth_group')->field("{$prefix}auth_group.id,{$prefix}auth_group.title")
            ->join("{$prefix}auth_group as g ON g.id = {$prefix}auth_group.pid")
            ->order($order)
            ->where($where)
            ->select();

        $this->assign('customer_group', $customer_group);

		//项目标签
        $project_tags = M('tagspackage')->field("{$prefix}tagspackage.id,{$prefix}tagspackage.name")->select();
        $this->assign('project_tags', $project_tags);

        //任务组
        $taskgroups = M('task_group')->field("{$prefix}task_group.*")->select();
        $this->assign('taskgroups', $taskgroups);

        $this->assign('currenttask', $currenttask);
        $this->display('form');
    }

    public function update()
    {
        $task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : false;
        $data['task_id'] = I('post.task_id', '', 'intval');
		$data['identifier'] = I('post.identifier', '', 'strip_tags');
		$data['name'] = I('post.name', '', 'strip_tags');
        $data['title'] = I('post.title', '', 'strip_tags');
		$data['points'] = I('post.points', '', 'strip_tags');
		$data['expected_time'] = I('post.expected_time', '', 'strip_tags');
		$data['start'] = I('post.start');
		$data['end'] = I('post.end');
		$data['view'] = I('post.view', '', 'intval');

        if($data['start'] == ''){
            $data['start'] = date('Y-m-d');
        }
        if($data['end'] == ''){
            $data['end'] = date('Y-m-d');
        }
        if($task_id){
             $task = M('task')->where('task_id=' . $task_id)->find();
             $data['image'] = $task['image'];
        }else{
            $data['image'] = '';
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
				'savePath' => 'upload/images/',
                'subName'  =>  '',
            ));
            $info = $upload->upload($_FILES);
            if(!$info) {// 上传错误提示错误信息
                // $error = $upload->getError();
                // image1
                //$this->error($error);
            }else{// 上传成功

                foreach ($info as $item) {
                    if($item['key'] == 'image'){
                        if($data['image'] <> ''){
                             unlink($upload->rootPath.$item['savepath'].$data['image']);
                        }
                        $imagefile = $upload->rootPath.$item['savepath'].$item['savename'];
                        $data['image'] = $item['savename'];
                    }

                }

            }
        }

		$data['research_group'] = isset($_POST['research_group'])?implode(',',$_POST['research_group']):'';

        $have_research_groups = M('auth_group')->field("{$prefix}auth_group.title")->where('type = 2 and pid=8')->getField('title',true);
		if((!in_array($data['name'],$have_research_groups)) && isset($_POST['research_group']) && in_array('-1',$_POST['research_group'])){

			 $groupdata['title'] = $data['name'];
             $groupdata['remark'] = '';
             $groupdata['pid'] = 8;
             $groupdata['type'] = 2;
			 $groupdata['status'] = 1;
			 $groupdata['rules'] = 0;

			 $last_id = M('auth_group')->data($groupdata)->add();

			 $data['research_group'] = str_replace('-1',$last_id,$data['research_group']);
		}

		$data['researcher'] = isset($_POST['researcher'])?implode(',',$_POST['researcher']):'';
		$data['customer_group'] = isset($_POST['customer_group'])?implode(',',$_POST['customer_group']):'';
        $data['project_tags'] = isset($_POST['project_tags'])?implode(',',$_POST['project_tags']):'';
        $data['description'] = htmlentities($_POST['description']);
		$data['welcome_information'] = htmlentities($_POST['welcome_information']);
        // I('post.welcome_information', '', 'htmlspecialchars');
		$data['end_information'] = htmlentities($_POST['end_information']);
		$data['remark'] = htmlentities($_POST['remark']);

        $data['status'] = I('post.status', '', 'intval');
        $data['state'] = I('post.state', 0, 'intval');

        $data['o'] = I('post.o', '', 'intval');

        $data['taskgroup_id'] = I('post.taskgroup_id', '', 'intval');
        if($data['taskgroup_id'] == -1){
            $taskgroup_id = 0;
            $task_group = M('task_group')->where("title='".$data['title']."'")->find();
            if($task_group){
                $taskgroup_id = $task_group['taskgroup_id'];
            }else{
                $group_data = array();
                $group_data['taskgroup_id'] = 0;
                $group_data['title'] = $data['title'];
                $group_data['description'] = '';
                $group_data['status'] = 1;
                $group_data['created'] = time();
                $taskgroup_id = M('task_group')->data($group_data)->add();
            }

            $data['taskgroup_id'] = $taskgroup_id;
        }
        if ($task_id) {
            M('task')->data($data)->where("task_id='{$task_id}'")->save();
            addlog('编辑任务，ID：' . $task_id);
        } else {
            $task_id = M('task')->data($data)->add();
            addlog('新增任务，名称：' . $data['title']);
        }
        M('task_research_group')->where("task_id='{$task_id}'")->delete();
        $research_groups = explode(',', $data['research_group']);

        $taskgroup_id = $data['taskgroup_id'];
        if(count($research_groups)){
            foreach($research_groups as $research_group){
                $research = M('task_research_group')->order('o desc')->where("taskgroup_id='{$taskgroup_id}' AND auth_group_id='{$research_group}'")->find();

                $research_data = array();
                $research_data['task_id'] = $task_id;
                $research_data['taskgroup_id'] = $taskgroup_id;
                $research_data['auth_group_id'] = $research_group;
                $research_data['o'] = isset($research[o])?intval($research[o])+1:1;
                M('task_research_group')->data($research_data)->add();
            }
        }
        $this->success('操作成功！',U('index'));
    }

    public function add()
    {
		$prefix = C('DB_PREFIX');

		$have_research_group = array();
		$have_researcher = array();
		$have_customer_group = array();
		$have_project_tags = array();
        $this->assign('have_research_group', $have_research_group);
        $this->assign('have_researcher', $have_researcher);
        $this->assign('have_customer_group', $have_customer_group);
        $this->assign('have_project_tags', $have_project_tags);

        $have_research_groups = M('auth_group')->field("{$prefix}auth_group.title")->where('type = 2')->getField('title',true);
        $this->assign('have_research_groups', $have_research_groups);

		//调研对象组
        $research_group = M('auth_group')->field("{$prefix}auth_group.id,{$prefix}auth_group.title")->where('type = 2 and pid=8')->select();
        $this->assign('research_group', $research_group);


		//研究员
		$order = "";
        $where = " {$prefix}auth_group.id= 9";

        $researcher = M('member')->field("{$prefix}member.uid,{$prefix}member.user")
            ->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
            ->join("{$prefix}auth_group ON {$prefix}auth_group.id = {$prefix}auth_group_access.group_id")
            ->order($order)
            ->where($where)
            ->select();
        $this->assign('researcher', $researcher);

		//客户组
		$order = "";
        $where = " g.id= 10";

        $customer_group = M('auth_group')->field("{$prefix}auth_group.id,{$prefix}auth_group.title")
            ->join("{$prefix}auth_group as g ON g.id = {$prefix}auth_group.pid")
            ->order($order)
            ->where($where)
            ->select();

        $this->assign('customer_group', $customer_group);

		//项目标签
        $project_tags = M('tagspackage')->field("{$prefix}tagspackage.id,{$prefix}tagspackage.name")->select();
        $this->assign('project_tags', $project_tags);

        //任务组
        $taskgroups = M('task_group')->field("{$prefix}task_group.*")->select();
        $this->assign('taskgroups', $taskgroups);

        $this->display('form');
    }


    public function status()
    {
        $ids = implode(',', I('post.ids'));
        $status = I('post.status');
        if (!$ids) {
            $task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : false;
            if (!$task_id) {
                $this->error('参数错误！');
            }

            $task = M('task')->where('task_id=' . $task_id)->find();
            if (!$task) {
                $this->error('参数错误！');
            }

			$status = $task['status'];
			if ($status == 1) {
			   $res = M('task')->data(array('status' => 0))->where('task_id=' . $task_id)->save();
			}
			if ($status != 1 ) {
				$res = M('task')->data(array('status' => 1))->where('task_id=' . $task_id)->save();
			}

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{
            $res = M('task')->data(array('status' => $status))->where('task_id in(' . $ids.')')->save();

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }

        }
    }
    public function updatestate()
    {
        $prefix = C('DB_PREFIX');

        $state = isset($_GET['state']) ? intval($_GET['state']) : 0;
        $task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;
        if (!$state) {
            $this->error('参数错误！');
        }
        if (!$task_id) {
            $this->error('参数错误！');
        }
        $task = M('task')->where("{$prefix}task.task_id='$task_id'")->find();
        if (!$task) {
            $this->error('参数错误！');
        }

        $res = M('task')->data(array('state' => $state))->where('task_id=' . $task_id)->save();
        if ($res) {
            if($state == 1){

                $list = M('member')->field("distinct {$prefix}member.uid")
                    ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
                    ->where("{$prefix}auth_group_access.group_id in (".$task['research_group'].")")
                    ->select();
                foreach($list as $item){
                    $message_data = array();
                    $message_data['uid'] = $item['uid'];
                    $message_data['type'] = 1;
                    $message_data['message'] = '您有新任务《'.$task['title'].'》,请及时完成！';
                    $message_data['status'] = 0;
                    $message_data['created'] = time();
                    M('member_message')->data($message_data)->add();
                }
            }
            $this->success('更新状态成功！');
        } else {
            $this->error('更新失败！');
        }

    }

	public function subjects()
    {
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
		$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';

        $prefix = C('DB_PREFIX');

        $order = "";
        $where = '1 = 1 ';
        if ($keyword <> '') {
            $where .= " AND {$prefix}task_subjects.title LIKE '%$keyword%'";
        }
        if ($task_id <> '') {
			$where .= " AND {$prefix}task_subjects.task_id = $task_id";
        }

        $task_subjects = M('task_subjects');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $count = $task_subjects->field("{$prefix}task_subjects.*,{$prefix}task.title as task_title,{$prefix}task.state")
            ->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
            ->order("{$prefix}task_subjects.o asc")
            ->where($where)
            ->count();

        $list = $task_subjects->field("{$prefix}task_subjects.*,{$prefix}task.title as task_title,{$prefix}task.state")
            ->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
            ->order("{$prefix}task_subjects.o asc")
            ->where($where)
            ->select();

        $sort = array();
		foreach($list as $i=>$item){
            $sort[] = $item['subject_id'];
			$task_item = M('task')->field("{$prefix}task.*")
				->where("task_id='".$item['task_id']."'")
				->find();

			$count_questions = M('task_questions')->field("{$prefix}task_questions.*")
				->where("subject_id='".$item['subject_id']."'")
				->count();
			$list[$i]['count_questions']= $count_questions;//题目数量

			$count_users_all = 0;
            if($task_item['research_group']){
    			$count_users_all = M('member')->field("{$prefix}member.uid")
    				->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
    				->where("{$prefix}auth_group_access.group_id in (".$task_item['research_group'].")")
    				->count();
            }

			$list[$i]['count_users_all'] = $count_users_all;//参与人数

            $count_users_completed = M('task_questions_answers')->field("{$prefix}task_questions_answers.answer_id")
                ->where("{$prefix}task_questions_answers.subject_id='".$item['subject_id']."'")
                ->count();
			$list[$i]['count_users_completed'] = $count_users_completed;//已完成
			$list[$i]['count_users_pending'] = 28;//进行中
		}

        $this->assign('list', $list);

		$this->assign('task_id', $task_id);

        $this->assign('sort', implode(",",$sort));

        $this->display('subjects');
    }
    public function subject($subject_id = 0)
    {
		$prefix = C('DB_PREFIX');

		$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;

        $task_subjects = M('task_subjects');

        $currenttasksubject = $task_subjects->field("{$prefix}task_subjects.*,{$prefix}task.title as tasktitle")
            ->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
            ->where("subject_id='$subject_id'")->find();

        if (!$currenttasksubject) {
            $this->error('参数错误！');
        }
        $this->assign('currenttasksubject', $currenttasksubject);

		$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;

        if ($subject_id <> '') {
			$order = " {$prefix}task_questions.topic ASC";
			$where = " {$prefix}task_questions.subject_id = $subject_id";

			$list = M('task_questions')->field("{$prefix}task_questions.*")
				->order($order)
				->where($where)
				->select();

			$surveydata = $this->get_string_from_list($list);

			$this->assign('surveydata', $surveydata);

			$this->assign('list', $list);
        }
		$this->assign('subject_id', $subject_id);


        $this->display('subject');
    }

    public function addsubject()
    {
		$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;

		$this->assign('task_id', $task_id);

        $this->display('subjectform');
    }
    public function delsubject()
    {
        $ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : false;
        if (!$ids) {
            $this->error('请勾选删除题目！');
        }
        if (is_array($ids)) {
            foreach ($ids as $k => $v) {
                $ids[$k] = intval($v);
            }
            if (!$ids) {
                $ids = implode(',', $ids);
            }
        }
        $map['subject_id'] = array('in', $ids);
        if (M('task_subjects')->where($map)->delete()) {
            M('task_questions')->where($map)->delete();

            addlog('删除题目ID：' . implode(',', $ids));
            $this->success('题目删除成功！');
        } else {
            $this->error('参数错误！');
        }
    }

    public function editsubject($id = 0)
    {
		$prefix = C('DB_PREFIX');

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $task_subjects = M('task_subjects');
        $currenttasksubject = $task_subjects->where("subject_id='$id'")->find();
        if (!$currenttasksubject) {
            $this->error('参数错误！');
        }
        $this->assign('currenttasksubject', $currenttasksubject);
        $options = $this->getQuestionItems($id);
        //dump($options);
        $this->assign('options', $options);

        $relationlist = M('task_subject_relation')->field("{$prefix}task_subject_relation.*")
            ->order("{$prefix}task_subject_relation.relation_id asc")
            ->where("{$prefix}task_subject_relation.subject_id='$id'")
            ->select();
        $this->assign('relationlist', $relationlist);

        $this->display('subjectform');
    }

    public function updatesubject()
    {
        $subject_id = isset($_POST['subject_id']) ? intval($_POST['subject_id']) : false;
        $data['subject_id'] = I('post.subject_id', '', 'intval');
		$data['task_id'] = I('post.task_id', '', 'intval');
        $data['title'] = I('post.title', '', 'strip_tags');
        $data['description'] = I('post.description', '', 'htmlspecialchars');
        $data['attachment_type'] = I('post.attachment_type', '', 'strip_tags');
        //$data['attachment_file'] = '';
        $data['attachment_showtime'] = I('post.attachment_showtime', '', 'intval');
        $data['attachment_view'] = I('post.attachment_view', '', 'intval');
        $data['attachment_status'] = I('post.attachment_status', '', 'intval');

		$data['surveydata'] = '';
        $data['status'] = I('post.status', '', 'intval');
        //$data['o'] = I('post.o', '', 'intval');

        if (!empty($_FILES)) {

            $mimes = array();
            $exts = array(
                'jpg',
                'png',
                'gif',
                'mp4'
            );
            $upload = new Upload(array(
                'mimes' => $mimes,
                'exts' => $exts,
                'rootPath' => 'Public/',
                'savePath' => 'upload/subject_attachment/',
                'subName'  =>  '',
            ));
            $info = $upload->upload($_FILES);
            if($info) {

                $subject = M('task_subjects')->where('subject_id=' . $subject_id)->find();

                foreach ($info as $item) {
                    if($item['key'] == 'attachment_file'){
                        if($subject['attachment_file']){
                            unlink($upload->rootPath.$item['savepath'].$subject['attachment_file']);
                        }
                        $data['attachment_file'] = $item['savename'];
                    }

                }

            }
        }
        if ($subject_id) {
            M('task_subjects')->data($data)->where("subject_id='{$subject_id}'")->save();
            addlog('编辑题目，ID：' . $subject_id);
        } else {
            $data['direct_answer'] = I('post.direct_answer', '', 'intval');

            $lastsubject = M('task_subjects')->order('o desc')->where('task_id=' . $data['task_id'])->find();
            $data['o'] = $lastsubject['o']+1;

            $subject_id = M('task_subjects')->data($data)->add();
            $direct_answer = I('post.direct_answer', '', 'intval');
            if($direct_answer == 1){
                $question_id = 0;
                $question_data['question_id'] = $question_id;
                $question_data['task_id'] = $data['task_id'];
                $question_data['subject_id'] = $subject_id;
                $question_data['type'] = 'question';
                $question_data['topic'] = 1;
                $question_data['title'] = $data['title'];

                $question_data['keyword'] = '';
                $question_data['relation'] = '';

                $question_data['hasjump'] = '';
                $question_data['anytimejumpto'] = '';
                $question_data['requir'] = '';
                $question_data['ins'] = '';

                $question_data['verify'] = '';

                $question_data['mainWidth'] = '';
                $question_data['tag'] = '';
                $question_data['numperrow'] = '';
                $question_data['randomChoice'] = '';
                $question_data['hasvalue'] = '';
                $question_data['lowLimit'] = '';
                $question_data['upLimit'] = '';
                $question_data['referTopic'] = '';
                $question_data['referedTopics'] = '';

                $question_data['height'] = '';
                $question_data['maxword'] = '';
                $question_data['norepeat'] = '';
                $question_data['default'] = '';
                $question_data['needOnly'] = '';
                $question_data['needsms'] = '';
                $question_data['hasList'] = '';
                $question_data['listId'] = '';
                $question_data['width'] = '';
                $question_data['underline'] = '';
                $question_data['minword'] = '';

                $question_data['minvalue'] = '';
                $question_data['maxvalue'] = '';
                $question_data['minvaluetext'] = '';
                $question_data['maxvaluetext'] = '';

                $question_data['rowtitle'] = '';
                $question_data['rowtitle2'] = '';
                $question_data['columntitle'] = '';
                $question_data['rowwidth'] = '';
                $question_data['randomRow'] = '';
                $question_data['rowwidth2'] = '';
                $question_data['isTouPiao'] = '';
                $question_data['isCeShi'] = '';

                $question_data['ext'] = '';
                $question_data['maxsize'] = '';
                $question_data['height'] = 1;
                $question_data['maxword'] = '';
                $question_data['norepeat'] = '';
                $question_data['default'] = '';
                $question_data['needOnly'] = '';
                $question_data['needsms'] = '';
                $question_data['hasList'] = '';
                $question_data['listId'] = '';
                $question_data['width'] = '';
                $question_data['underline'] = '';
                $question_data['minword'] = '';
                $question_data['verify'] = '';

                $question_id = M('task_questions')->data($question_data)->add();
            }
            addlog('新增题目，名称：' . $data['title']);
        }

        $this->success('操作成功！',U('subjects',array('task_id'=>$data['task_id'])));
    }

    public function subjectstatus()
    {
        $ids = implode(',', I('post.ids'));
        $status = I('post.status');
        if (!$ids) {
            $subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : false;
            if (!$subject_id) {
                $this->error('参数错误！');
            }
            $task_subjects = M('task_subjects')->where('subject_id=' . $subject_id)->find();
            if (!$task_subjects) {
                $this->error('参数错误！');
            }

            $status = $task_subjects['status'];
            if ($status == 1) {
               $res = M('task_subjects')->data(array('status' => 0))->where('subject_id=' . $subject_id)->save();
            }
            if ($status != 1 ) {
                $res = M('task_subjects')->data(array('status' => 1))->where('subject_id=' . $subject_id)->save();
            }
            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{
            $res = M('task_subjects')->data(array('status' => $status))->where('subject_id in(' . $ids.')')->save();

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->success('更新状态成功！');
            }

        }
    }
    public function sortsubjects(){
        $order = $_POST['order'];
        $neworder = trim($_POST['neworder']);
        if (!empty($neworder)) {
            $sort_arr=explode(",",$neworder);
            $i = 1;
            foreach($sort_arr as $val){
                M('task_subjects')->data(array('o'=>$i))->where("subject_id='{$val}'")->save();
                $i++;
            }
        }

    }
    public function subjectscopy()
    {
        $task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;

        $prefix = C('DB_PREFIX');

        $order = "";
        $where = array();

        if ($task_id <> '') {
            $where[] = " {$prefix}task_subjects.task_id = $task_id";
        }
        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }

        $count = M('task_subjects')->field("{$prefix}task_subjects.*")
            ->order("{$prefix}task_subjects.o asc")
            ->where($wherestring)
            ->count();

        $list = M('task_subjects')->field("{$prefix}task_subjects.*")
            ->order("{$prefix}task_subjects.o asc")
            ->where($wherestring)
            ->select();

        foreach($list as $i=>$item){
            $task_item = M('task')->field("{$prefix}task.*")
                ->where("task_id='".$item['task_id']."'")
                ->find();

            $count_questions = M('task_questions')->field("{$prefix}task_questions.*")
                ->where("subject_id='".$item['subject_id']."'")
                ->count();
            $list[$i]['count_questions']= $count_questions;//题目数量

            $count_users_all = 0;
            $count_users_all = M('member')->field("{$prefix}member.uid")
                ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
                ->where("{$prefix}auth_group_access.group_id in (".$task_item['research_group'].")")
                ->count();

            $list[$i]['count_users_all'] = $count_users_all;//参与人数

            $count_users_completed = M('task_questions_answers')->field("{$prefix}task_questions_answers.answer_id")
                ->where("{$prefix}task_questions_answers.subject_id='".$item['subject_id']."'")
                ->count();
            $list[$i]['count_users_completed'] = $count_users_completed;//已完成

            $list[$i]['count_users_pending'] = 28;//进行中
        }

        $this->assign('list', $list);

        $tasklist = M('task')->field("{$prefix}task.*")
            ->order("{$prefix}task.task_id asc")
            ->select();
        $this->assign('tasklist', $tasklist);

        $this->display('subjectscopy');
    }

    public function copysubjects(){
        $prefix = C('DB_PREFIX');

        $task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;
        if(!$task_id){
            $this->error('请勾选复制到的任务！');
        }

        $ids = isset($_POST['ids']) ? $_POST['ids'] : false;
        if (!$ids) {
            $this->error('请勾选复制的题目！');
        }
        if (!is_array($ids)) {
            $this->error('参数错误！');
        }
        $map['subject_id'] = array('in', implode(',', $ids));
        $subjects = M('task_subjects')->where($map)->select();

        foreach($subjects as $subject){
            $data = array();
            $data = $subject;
            $data['subject_id'] = 0;
            $data['task_id'] = $task_id;
            $new_subject_id = M('task_subjects')->data($data)->add();

            $questions =  M('task_questions')->field("{$prefix}task_questions.*")
                ->order("{$prefix}task_questions.topic ASC")
                ->where("subject_id='".$subject['subject_id']."'")
                ->select();
            foreach($questions as $question){
                $data = array();
                $data = $question;
                $data['question_id'] = 0;
                $data['subject_id'] = $new_subject_id;
                $new_question_id = M('task_questions')->data($data)->add();

                $question_items =  M('task_questions_item')->field("{$prefix}task_questions_item.*")
                    ->order("{$prefix}task_questions_item.item_id ASC")
                    ->where("{$prefix}task_questions_item.question_id = ".$question['question_id'] )
                    ->select();
                foreach($question_items as $item){
                    $data = array();
                    $data = $item;
                    $data['item_id'] = 0;
                    $data['subject_id'] = $new_subject_id;
                    $data['question_id'] = $new_question_id;
                    $new_questions_item_id = M('task_questions_item')->data($data)->add();
                }
            }
        }

        $this->success('题目复制成功',U('subjects',array('task_id'=>$task_id)));

    }

	public function questions()
    {
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
		$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;

        $prefix = C('DB_PREFIX');

        $where = "";

        if ($subject_id <> '') {
			$where = " {$prefix}task_questions.subject_id = $subject_id";
        }

        $count = M('task_questions')->field("{$prefix}task_questions.*")
            ->order('topic asc')
            ->where($where)
            ->count();

        $list = M('task_questions')->field("{$prefix}task_questions.*")
            ->order('topic asc')
            ->where($where)
            ->select();

        $this->assign('list', $list);

        $subject = M('task_subjects')->where("subject_id='$subject_id'")->find();

		$this->assign('subject_id', $subject_id);
        $this->assign('task_id', $subject['task_id']);

        $this->display('questions');
    }

    public function editquestion($question_id = 0)
    {
        $prefix = C('DB_PREFIX');

        $question_id = intval($question_id);
        $task_questions = M('task_questions');
        $currentquestion = M('task_questions')->where("question_id='$question_id'")->find();
        if (!$currentquestion) {
            $this->error('参数错误！');
        }
        $this->assign('currentquestion', $currentquestion);

        if($currentquestion['type']=='radio' || $currentquestion['type']=='check'){
            $selectlist = M('task_questions_item')->field("{$prefix}task_questions_item.*")
                ->order("{$prefix}task_questions_item.item_id asc")
                ->where("{$prefix}task_questions_item.question_id = ".$currentquestion['question_id'] )
                ->select();
            $this->assign('selectlist', $selectlist);
        }
        $this->display('questionform');
    }
    public function delquestions()
    {
        $ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : false;
        if (!$ids) {
            $this->error('请勾选删除问题！');
        }
        if (!is_array($ids)) {
			$this->error('参数错误！');
        }

        $map['question_id'] = array('in', implode(',', $ids));
        if (M('task_questions')->where($map)->delete()) {

            addlog('删除问题ID：' . implode(',', $ids));
            $this->success('问题删除成功！');
        } else {
            $this->error('参数错误！');
        }
    }
    public function updatequestion()
    {
        $question_id = isset($_POST['question_id']) ? intval($_POST['question_id']) : false;
        $data['question_id'] = I('post.question_id', '', 'intval');
		$data['subject_id'] = I('post.subject_id', '', 'intval');
        $data['title'] = I('post.title', '', 'strip_tags');
        if ($question_id) {
            M('task_questions')->data($data)->where("question_id='{$question_id}'")->save();
            addlog('编辑问题，ID：' . $question_id);
        }
        $items = isset($_POST['item_title']) ? $_POST['item_title'] : array();
        if(count($items )){
            foreach ($items as $item_id => $item_title) {
                $item_data['item_id'] = $item_id;
                $item_data['item_title'] = $item_title;
                M('task_questions_item')->data($item_data)->where("item_id='{$item_id}'")->save();
            }
        }
        $this->success('操作成功！',U('questions',array('subject_id'=>$data['subject_id'])));
    }

	public function savequestions(){

        $submitType = isset($_REQUEST['submitType']) ? $_REQUEST['submitType'] : false;

        $surveydata = isset($_REQUEST['surveydata']) ? $_REQUEST['surveydata'] : false;
        $subject_id = isset($_REQUEST['subject_id']) ? intval($_REQUEST['subject_id']) : false;


		if($subject_id > 0){
			$data['subject_id'] = $subject_id;
			$data['surveydata'] = $surveydata;
			if ($subject_id) {
				M('task_subjects')->data($data)->where("subject_id='{$subject_id}'")->save();
				addlog('编辑题目，ID：' . $subject_id);
			}
            $subject = M('task_subjects')->where("subject_id='{$subject_id}'")->find();
            $task_id = $subject['task_id'];
            M('task_questions')->where("subject_id='{$subject_id}'")->delete();
            M('task_questions_item')->where("subject_id='{$subject_id}'")->delete();

            $log_data = array();
            $log_data['subject_id'] = $subject_id;
            $log_data['surveydata'] = $surveydata;
            $log_data['created'] = date('Y-m-d h:i:s');

            M('task_subjects_logs')->data($log_data)->add();

			$g = explode("¤",$surveydata);

			for ($d = 1; $d < count($g); $d++) {
				$DataArray[$d - 1] = $this->set_string_to_dataNode($g[$d]);
			}
			//var_dump($DataArray);
			//exit;

			foreach($DataArray as $dataNode){
				if($dataNode->type == 'radio' || $dataNode->type == 'check' || $dataNode->type == 'question' || $dataNode->type == 'slider' || $dataNode->type == 'matrix'|| $dataNode->type == 'fileupload'){
					$question_id = 0;
					$question_data['question_id'] = $question_id;
                    $question_data['task_id'] = $task_id;
					$question_data['subject_id'] = $subject_id;
					$question_data['type'] = $dataNode->type;
					$question_data['topic'] = $dataNode->topic;
					$question_data['title'] = $dataNode->title;

                    $pattern  = '/\[q(\d+)\]/is';
                    preg_match($pattern, $question_data['title'], $match);
                    if(count($match)){
                        $question_data['titletopic'] = $match[1];
                    }

					$question_data['keyword'] = $dataNode->keyword;
					$question_data['relation'] = $dataNode->relation;

					$question_data['hasjump'] = $dataNode->hasjump;
					$question_data['anytimejumpto'] = $dataNode->anytimejumpto;
					$question_data['requir'] = $dataNode->requir;
					$question_data['ins'] = $dataNode->ins;

					$question_data['verify'] = '';

					$question_data['mainWidth'] = '';
					$question_data['tag'] = '';
					$question_data['numperrow'] = '';
					$question_data['randomChoice'] = '';
					$question_data['hasvalue'] = '';
					$question_data['lowLimit'] = '';
					$question_data['upLimit'] = '';
					$question_data['referTopic'] = '';
					$question_data['referedTopics'] = '';

					$question_data['height'] = '';
					$question_data['maxword'] = '';
					$question_data['norepeat'] = '';
					$question_data['default'] = '';
					$question_data['needOnly'] = '';
					$question_data['needsms'] = '';
					$question_data['hasList'] = '';
					$question_data['listId'] = '';
					$question_data['width'] = '';
					$question_data['underline'] = '';
					$question_data['minword'] = '';

					$question_data['minvalue'] = '';
					$question_data['maxvalue'] = '';
					$question_data['minvaluetext'] = '';
					$question_data['maxvaluetext'] = '';

					$question_data['rowtitle'] = '';
					$question_data['rowtitle2'] = '';
					$question_data['columntitle'] = '';
					$question_data['rowwidth'] = '';
					$question_data['randomRow'] = '';
					$question_data['rowwidth2'] = '';
					$question_data['isTouPiao'] = '';
					$question_data['isCeShi'] = '';

                    $question_data['ext'] = '';
                    $question_data['maxsize'] = '';

				}
				if($dataNode->type == 'radio'){
					$question_data['mainWidth'] = $dataNode->mainWidth;
					$question_data['tag'] = $dataNode->tag;
					$question_data['numperrow'] = $dataNode->numperrow;
					$question_data['randomChoice'] = $dataNode->randomChoice;
					$question_data['hasvalue'] = $dataNode->hasvalue;
					$question_data['referTopic'] = $dataNode->referTopic;
					$question_data['referedTopics'] = $dataNode->referedTopics;
					$question_data['verify'] = $dataNode->verify;

					if ($question_id) {
						M('task_questions')->data($question_data)->where("question_id='{$question_id}'")->save();
					} else {
						$question_id = M('task_questions')->data($question_data)->add();
					}

					$selectlist = $dataNode->select;
					foreach($selectlist as $select){
						$item_id = 0;
						$item_data['item_id'] = $item_id;
						$item_data['question_id'] = $question_id;
						$item_data['subject_id'] = $subject_id;
						$item_data['item_title'] = $select->item_title;
						$item_data['item_radio'] = $select->item_radio;
						$item_data['item_value'] = $select->item_value;
						$item_data['item_jump'] = $select->item_jump;
						$item_data['item_tb'] = $select->item_tb;
						$item_data['item_tbr'] = $select->item_tbr;
						$item_data['item_img'] = $select->item_img;
						$item_data['item_imgtext'] = $select->item_imgtext;
						$item_data['item_desc'] = $select->item_desc;
						$item_data['item_label'] = $select->item_label;
						$item_data['item_huchi'] = $select->item_huchi;


						if ($item_id) {
							M('task_questions_item')->data($item_data)->where("item_id='{$item_id}'")->save();
						} else {
							$item_id = M('task_questions_item')->data($item_data)->add();
						}
					}
				}

				if($dataNode->type == 'check'){
					$question_data['mainWidth'] = $dataNode->mainWidth;
					$question_data['tag'] = $dataNode->tag;
					$question_data['numperrow'] = $dataNode->numperrow;
					$question_data['randomChoice'] = $dataNode->randomChoice;
					$question_data['hasvalue'] = $dataNode->hasvalue;
					$question_data['lowLimit'] = intval($dataNode->lowLimit);
					$question_data['upLimit'] = intval($dataNode->upLimit);
					$question_data['verify'] = $dataNode->verify;

					$question_data['referTopic'] = $dataNode->referTopic;
					$question_data['referedTopics'] = $dataNode->referedTopics;

					if ($question_id) {
						M('task_questions')->data($question_data)->where("question_id='{$question_id}'")->save();
					} else {
						$question_id = M('task_questions')->data($question_data)->add();
					}

					$selectlist = $dataNode->select;
					foreach($selectlist as $select){
						$item_id = 0;
						$item_data['item_id'] = $item_id;
						$item_data['question_id'] = $question_id;
						$item_data['subject_id'] = $subject_id;
						$item_data['item_title'] = $select->item_title;
						$item_data['item_radio'] = $select->item_radio;
						$item_data['item_value'] = $select->item_value;
						$item_data['item_jump'] = $select->item_jump;
						$item_data['item_tb'] = $select->item_tb;
						$item_data['item_tbr'] = $select->item_tbr;
						$item_data['item_img'] = $select->item_img;
						$item_data['item_imgtext'] = $select->item_imgtext;
						$item_data['item_desc'] = $select->item_desc;
						$item_data['item_label'] = $select->item_label;
						$item_data['item_huchi'] = $select->item_huchi;


						if ($item_id) {
							M('task_questions_item')->data($item_data)->where("item_id='{$item_id}'")->save();
						} else {
							$item_id = M('task_questions_item')->data($item_data)->add();
						}
					}
				}
				if($dataNode->type == 'question'){
					$question_data['height'] = $dataNode->height;
					$question_data['maxword'] = $dataNode->maxword;
					$question_data['norepeat'] = $dataNode->norepeat;
					$question_data['default'] = $dataNode->default;
					$question_data['needOnly'] = $dataNode->needOnly;
					$question_data['needsms'] = $dataNode->needsms;
					$question_data['hasList'] = $dataNode->hasList;
					$question_data['listId'] = $dataNode->listId;
					$question_data['width'] = $dataNode->width;
					$question_data['underline'] = $dataNode->underline;
					$question_data['minword'] = $dataNode->minword;
					$question_data['verify'] = $dataNode->verify;

					if ($question_id) {
						M('task_questions')->data($question_data)->where("question_id='{$question_id}'")->save();
					} else {
						$question_id = M('task_questions')->data($question_data)->add();
					}
				}
				if($dataNode->type == 'slider'){
					$question_data['minvalue'] = $dataNode->minvalue;
					$question_data['maxvalue'] = $dataNode->maxvalue;
					$question_data['minvaluetext'] = $dataNode->minvaluetext;
					$question_data['maxvaluetext'] = $dataNode->maxvaluetext;

					if ($question_id) {
						M('task_questions')->data($question_data)->where("question_id='{$question_id}'")->save();
					} else {
						$question_id = M('task_questions')->data($question_data)->add();
					}
				}
				if($dataNode->type == 'matrix'){
					$question_data['mainWidth'] = $dataNode->mainWidth;
					$question_data['tag'] = $dataNode->tag;

					$question_data['rowtitle'] = $dataNode->rowtitle;
					$question_data['rowtitle2'] = $dataNode->rowtitle2;
					$question_data['columntitle'] = $dataNode->columntitle;
					$question_data['rowwidth'] = $dataNode->rowwidth;
					$question_data['randomRow'] = $dataNode->randomRow;
					$question_data['rowwidth2'] = $dataNode->rowwidth2;
					$question_data['minvalue'] = $dataNode->minvalue;
					$question_data['maxvalue'] = $dataNode->maxvalue;
					$question_data['isTouPiao'] = $dataNode->isTouPiao;
					$question_data['isCeShi'] = $dataNode->isCeShi;

					$question_data['hasvalue'] = $dataNode->hasvalue;

					$question_data['verify'] = $dataNode->verify;

					$question_data['referTopic'] = $dataNode->referTopic;
					$question_data['referedTopics'] = $dataNode->referedTopics;

					if ($question_id) {
						M('task_questions')->data($question_data)->where("question_id='{$question_id}'")->save();
					} else {
						$question_id = M('task_questions')->data($question_data)->add();
					}
					if($dataNode->tag != '202'){
						$selectlist = $dataNode->select;
						foreach($selectlist as $select){
							$item_id = 0;
							$item_data['item_id'] = $item_id;
							$item_data['question_id'] = $question_id;
							$item_data['subject_id'] = $subject_id;
							$item_data['item_title'] = $select->item_title;
							$item_data['item_radio'] = $select->item_radio;
							$item_data['item_value'] = $select->item_value;
							$item_data['item_jump'] = $select->item_jump;
							$item_data['item_tb'] = $select->item_tb;
							$item_data['item_tbr'] = $select->item_tbr;
							$item_data['item_img'] = $select->item_img;
							$item_data['item_imgtext'] = $select->item_imgtext;
							$item_data['item_desc'] = $select->item_desc;
							$item_data['item_label'] = $select->item_label;
							$item_data['item_huchi'] = $select->item_huchi;

							if ($item_id) {
								M('task_questions_item')->data($item_data)->where("item_id='{$item_id}'")->save();
							} else {
								$item_id = M('task_questions_item')->data($item_data)->add();
							}
						}
					}
				}

                if($dataNode->type == 'fileupload'){
                    $question_data['width'] = $dataNode->width;
                    $question_data['ext'] = $dataNode->ext;
                    $question_data['maxsize'] = $dataNode->maxsize;
                    if ($question_id) {
                        M('task_questions')->data($question_data)->where("question_id='{$question_id}'")->save();
                    } else {
                        $question_id = M('task_questions')->data($question_data)->add();
                    }
                }
			}
			echo 'true';
		}

	}
	public function set_string_to_dataNode($r){
		$f = new \stdClass();
		$d = explode("§",$r);
		$f->type = $d[0];
		switch ($d[0]) {
			case "fileupload":
				$f->topic = $d[1];
				$v = explode("〒",$d[2]);
				$f->title = $v[0];
				$f->keyword = count($v) == 2 ? $v[1] : "";
				$f->relation = $v[2] ? $v[2] : "";
				if ($d[4] == "true") {
					$f->requir = true;
				} else {
					$f->requir = false;
				}
				$f->width = $d[5] ? intval($d[5]) : 200;
				$f->ext = $d[6] ? $d[6] : "";
				$f->maxsize = $d[7] ? intval($d[7]) : 4096;
				$f->ins = $d[8];
				if ($d[9] == "true") {
					$f->hasjump = true;
				} else {
					$f->hasjump = false;
				}
				$f->anytimejumpto = $d[10];
				if ($d[11]) {
					$t = explode("〒",$d[11]);
					$f->isCeShi = true;
					$f->ceshiValue = $t[0] ? $t[0] : 5;
					$f->ceshiDesc = $t[1] ? $t[1] : "";
				}

				break;
			case "slider":
				$f->topic = $d[1];
				$v = explode("〒",$d[2]);
				$f->title = $v[0];
				$f->keyword = count($v) == 2 ? $v[1] : "";
				$f->relation = $v[2] ? $v[2] : "";
				if ($d[4] == "true") {
					$f->requir = true;
				} else {
					$f->requir = false;
				}
				$f->minvalue = $d[5];
				$f->maxvalue = $d[6];
				$f->minvaluetext = $d[7];
				$f->maxvaluetext = $d[8];
				$f->ins = $d[9];
				if ($d[10] == "true") {
					$f->hasjump = true;
				} else {
					$f->hasjump = false;
				}
				$f->anytimejumpto = $d[11];

				break;
			case "question":
				$f->topic = $d[1];
				$v = explode("〒",$d[2]);
				$f->title = $v[0];
				$f->keyword = count($v) == 2 ? $v[1] : "";
				$f->relation = $v[2] ? $v[2] : "";
				$f->height = $d[4] ? intval($d[4]) : 1;
				$f->maxword = $d[5];
				if ($d[6] == "true") {
					$f->requir = true;
				} else {
					$f->requir = false;
				}
				if ($d[7] == "true") {
					$f->norepeat = true;
				} else {
					$f->norepeat = false;
				}
				$f->default = $d[8];
				$f->ins = $d[9];
				if ($d[10] == "true") {
					$f->hasjump = true;
				} else {
					$f->hasjump = false;
				}
				$f->anytimejumpto = $d[11];
				$f->verify = $d[12];
				if ($d[13]) {
					$l = explode("〒",$d[13]);
					$f->needOnly = $l[0] == "true" ? true: false;
					$f->needsms = $l[1] == "true" ? true: false;
				}
				$f->hasList = $d[14] == "true" ? true: false;
				$f->listId = $d[15] ? intval($d[15]) : -1;
				$f->width = $d[16] ? intval($d[16]) : "";
				$f->underline = $d[17] == "true" ? true: false;
				$f->minword = $d[18] ? intval($d[18]) : "";
				if ($d[19]) {
					if ($f->verify == "多级下拉") {
						$f->levelData = $d[19] ? $d[19] : "";
					} else {
						$h = explode("〒",$d[19]);
						$f->isCeShi = true;
						$f->ceshiValue = $h[0] ? $h[0] : 5;
						$f->answer = $h[1] ? $h[1] : "请设置答案";
						$f->ceshiDesc = $h[2] ? $h[2] : "";
						$f->include = $h[3] == "true";
					}
				}

				break;
			case "radio":
			case "check":
			case "matrix":
				$f->type = $d[0];
				$f->topic = $d[1];
				$v = explode("〒",$d[2]);
				$f->title = $v[0];
				$f->keyword = count($v) == 2 ? $v[1] : "";
				$f->relation = $v[2] ? $v[2] : "";
				$f->mainWidth = $v[3] ? $v[3] : "";
				$f->tag = is_numeric($d[3]) ? intval($d[3]) : 0;
				if ($f->type == "matrix") {
					$n = explode("〒",$d[4]);
					$f->rowtitle = $n[0];
					if (count($n) >= 2) {
						$f->rowtitle2 = $n[1];
					} else {
						$f->rowtitle2 = "";
					}
					if (count($n) == 3) {
						$f->columntitle = $n[2];
					} else {
						$f->columntitle = "";
					}
				} else {
					$x =  explode("〒",$d[4]);
					$f->numperrow = is_numeric($x[0]) ? intval($x[0]) : 1;
					$f->randomChoice = false;
					if (count($x)  == 2) {
						$f->randomChoice = $x[1] == "true" ? true: false;
					}
				}
				if ($d[5] == "true") {
					$f->hasvalue = true;
				} else {
					$f->hasvalue = false;
				}
				if ($d[6] == "true") {
					$f->hasjump = true;
				} else {
					$f->hasjump = false;
				}
				$f->anytimejumpto = $d[7];
				if ($d[0] == "check" || ($d[0] == "matrix" && $f->tag == "102")) {
					$i = explode(",",$d[8]);
					if ($i[0] == "true") {
						$f->requir = true;
					} else {
						$f->requir = false;
					}
					if ($i[1] == "shop") {
						$f->isShop = true;
					} else {
						$f->lowLimit = $i[1];
						$f->upLimit = $i[2];
					}
				} else {
					if ($d[8] == "true") {
						$f->requir = true;
					} else {
						if ($d[0] == "radio") {
							$i = explode(",",$d[8]);
							$f->requir = $i[0] == "true";
							if ($i[1] == "1") {
								$f->isQingJing = true;
							} else {
								if ($i[1] == "2") {
									$f->ispanduan = true;
								}
							}
						} else {
							$f->requir = false;
						}
					}
				}
				if ($f->type == "matrix") {
					$A = explode("〒",$d[9]);
					$B = explode(",",$A[0]);
					$f->rowwidth = strpos($B[0],"%") > -1 ? $B[0] : "";
					$f->randomRow = $B[1] == "true";
					$f->rowwidth2 = "";
					if (count($A) >= 2) {
						$f->rowwidth2 = strpos($A[1],"%") > -1 ? $A[1] : "";
					}
					$f->minvalue = 0;
					$f->maxvalue = 10;
					if ($f->tag == "202" || $f->tag == "301") {
						$f->minvalue = $A[2] ? $A[2] : "";
						$p = $A[3] ? $A[3] : "";
						$f->maxvalue = $p;
						if ($f->tag == "301") {
							$s = explode(",",$p);
							$f->maxvalue = $s[0];
							$f->digitType = $s[1] ? $s[1] : 0;
						}
					} else {
						if ($f->tag == "102" || $f->tag == "103") {
							$f->daoZhi = $A[2] == "true";
						} else {
							if ($f->tag == "201" || $f->tag == "302") {
								$f->hasvalue = false;
								$k = $A[2] ? $A[2] : "";
								$f->rowVerify = Array();
								if ($k) {
									$o = explode(";",$k);
									for ($y = 0; $y < count($o); $y++) {
										if (!$o[$y]) {
											continue;
										}
										$z = new \stdClass();
										$e = explode("¦",$o[$y]);
										if ($e[1] == "指定选项") {
											$z->verify = $e[1];
											$z->choice = $e[2] ? $e[2] : "";
											$z->isRequir = $e[3] == "false" ? false: true;
											$c = intval($e[0]);
											$f->rowVerify[$c] = $z;
										} else {
											$q = explode(",",$o[$y]);
											$z->verify = $q[1];
											$z->minword = $q[2];
											$z->maxword = $q[3];
											$z->width = $q[4] ? $q[4] : "";
											$z->isRequir = $q[5] == "false" ? false: true;
											$z->needOnly = $q[6] == "true";
											$c = intval($q[0]);
											$f->rowVerify[$c] = $z;
										}
									}
								}
							}
						}
					}
					$f->isTouPiao = false;
					$f->isCeShi = false;
				} else {
					$g = explode("〒",$d[9]);
					if ($g[0] == "true") {
						$f->isTouPiao = true;
						$f->touPiaoWidth = is_numeric($g[1]) ? intval($g[1]) : 50;
						$f->displayDesc = $g[2] == "true";
						$f->displayNum = $g[3] == "true";
						$f->displayPercent = $g[4] == "true";
						$f->displayThumb = $g[5] == "true";
						$f->displayDescTxt = $g[6] ? $g[6] : "";
					} else {
						if ($g[0] == "desc") {
							$f->displayDesc = true;
							$f->displayDescTxt = $g[1] ? $g[1] : "";
						}
					}
				}
				$f->ins = $d[10];
				$a = explode(",",$d[11]);
				$f->verify = $a[0];
				if ($a[1] == "true") {
					$f->nocolumn = true;
				}
				$f->referTopic = $d[12];
				$f->referedTopics = $d[13];
				$f->select = Array();
				$b = 14;
				for ($u = $b; $u < count($d); $u++) {
					$w = Array();
					$w = explode("〒",$d[$u]);
					$m = $u - $b + 1;
					$f->select[$m] = new \stdClass();
					$f->select[$m]->item_title = $w[0];
					if ($w[1] == "true") {
						$f->select[$m]->item_radio = true;
					} else {
						$f->select[$m]->item_radio = false;
					}
					$f->select[$m]->item_value = $w[2];
					$f->select[$m]->item_jump = $w[3];
                    $f->select[$m]->item_tb = $w[4] == "true";
                    $f->select[$m]->item_tbr = $w[5] == "true";
                    $f->select[$m]->item_img = $w[6];
                    $f->select[$m]->item_imgtext = $w[7] == "true";
                    $f->select[$m]->item_desc = $w[8];
                    $f->select[$m]->item_label = $w[9];
					if (count($w) >= 9) {
						$f->select[$m]->item_huchi = $w[10] == "true";
					}
				}
				break;
			default:
				break;
		}
		return $f;
	}
	public function allanswers()
    {
        $uid = $this->USER['uid'];

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
		$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;
        $answer_id = isset($_GET['answer_id']) ? intval($_GET['answer_id']) : 0;

        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $annota = isset($_GET['annotation']) ? $_GET['annotation'] : '';
        $haveremark = isset($_GET['haveremark']) ? $_GET['haveremark'] : '';

        $prefix = C('DB_PREFIX');

        $order = " {$prefix}task_questions_answers.answer_id DESC";
        $where = array();
        if ($keyword <> '') {
			//$where[] = "{$prefix}task_subjects.title LIKE '%$keyword%'";
            $keywords = explode(',', $keyword);
            $w = array();
            foreach ($keywords as $val) {
                $w[] = " title = '$val'";
            }
            $tagids = M('tags')->where("(".implode(' OR ',$w).") AND status=1")->getField('id',true);

            if($tagids){
                $uids = M('member_tag_map')->where("tagid in (".implode(',',$tagids).")")->getField('uid',true);
                if($uids){
                    $where[] = " {$prefix}task_questions_answers.uid in (".implode(',',$uids).")";
                }else{
                    $where[] = " {$prefix}task_questions_answers.uid=0";
                }
            }else{
                $where[] = " {$prefix}task_questions_answers.uid=0";
            }
        }
        if ($task_id <> '') {
			$where[] = " {$prefix}task_subjects.task_id = $task_id";
        }
        if ($status <> '') {
            $where[] = " {$prefix}task_questions_answers.status = $status";
        }
        if ($answer_id <> '') {
            $where[] = " {$prefix}task_questions_answers.answer_id = $answer_id";
        }

        if($haveremark == 1){
            $where[] = " {$prefix}task_questions_answers_remark.remark_id > 0";
        }
        if($haveremark == -1){
            $where[] = " {$prefix}task_questions_answers_remark.remark_id is null";
        }
        if ($annota <> '') {
            $members = M('member_annotation')->where("annotation = '$annota' AND auid = ".$uid)->getField('uid',true);
            if($members){
                $where[] = " {$prefix}task_questions_answers.uid in (".implode(',',$members).")";
            }else{
                $where[] = " {$prefix}task_questions_answers.uid=0";
            }
        }

		$wherestring = '';
		if(count($where)){
			$wherestring = implode(' AND ',$where);
		}


        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $count = M('task_questions_answers')
			->join("{$prefix}task_subjects ON {$prefix}task_questions_answers.subject_id = {$prefix}task_subjects.subject_id")
            ->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
            ->join("{$prefix}member ON {$prefix}task_questions_answers.uid = {$prefix}member.uid")
            ->join("LEFT JOIN {$prefix}task_questions_answers_remark  ON {$prefix}task_questions_answers.answer_id = {$prefix}task_questions_answers_remark.answer_id")
            ->order($order)
            ->where($wherestring)
            ->count();

        $lists = M('task_questions_answers')->field("distinct {$prefix}task_questions_answers.*,{$prefix}task.name as task_name,{$prefix}task_subjects.title as subject_title,{$prefix}task_subjects.description as subject_description,{$prefix}task_subjects.o as subject_o,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.head,{$prefix}task.project_tags")
			->join("{$prefix}task_subjects ON {$prefix}task_questions_answers.subject_id = {$prefix}task_subjects.subject_id")
			->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
            ->join("{$prefix}member ON {$prefix}task_questions_answers.uid = {$prefix}member.uid")
            ->join("LEFT JOIN {$prefix}task_questions_answers_remark  ON {$prefix}task_questions_answers.answer_id = {$prefix}task_questions_answers_remark.answer_id")
            ->order($order)
            ->where($wherestring)
            ->limit($offset . ',' . $pagesize)
            ->select();

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $list = array();

		foreach($lists as $i=>$item){
            $list[$i] = $item;
            $order = "";
            $list[$i]['islike'] = $this->islike($item['answer_id'],$uid);
            $list[$i]['question_answers'] = $this->getAnswers($item['answer_id']);
            $questions = M('task_questions')->field("{$prefix}task_questions.*")
                ->order("{$prefix}task_questions.topic asc")
                ->where("{$prefix}task_questions.subject_id = ".$item['subject_id'])
                ->select();


            $surveydata = $this->get_string_from_list($questions);

            $list[$i]['questions'] = $surveydata;

            $remark = M('task_questions_answers_remark')->field("{$prefix}task_questions_answers_remark.*,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.head,{$prefix}member.admin")
                ->join("{$prefix}member ON {$prefix}task_questions_answers_remark.uid = {$prefix}member.uid")
                ->where("{$prefix}task_questions_answers_remark.answer_id = ".$item['answer_id'])
                ->select();

            $list[$i]['remark'] = $remark;

            $comment = M('task_questions_answers_comment')->field("{$prefix}task_questions_answers_comment.*,{$prefix}member.user,{$prefix}member.head")
                ->join("{$prefix}member ON {$prefix}task_questions_answers_comment.uid = {$prefix}member.uid")
                ->where("{$prefix}task_questions_answers_comment.answer_id = ".$item['answer_id'])
                ->select();

            $list[$i]['comment'] = $comment;

            $annotation = M('member_annotation')->field("{$prefix}member_annotation.*")
                ->where("{$prefix}member_annotation.uid = ".$item['uid'] ." AND {$prefix}member_annotation.auid = ".$uid)
                ->select();

            $list[$i]['annotation'] = $annotation;

            //项目标签
            $tags_array = array();
            $tags = array();
            if($item['project_tags']){
                $tags_array = M('tags')->field("distinct {$prefix}tags.*")
                    ->join("{$prefix}tag_package_groups ON {$prefix}tag_package_groups.taggroups_id = {$prefix}tags.groupid")
                    ->where("{$prefix}tags.status=1 AND {$prefix}tag_package_groups.tagspackage_id in (".$item['project_tags'].")")
                    ->getField('id',true);
            }
            if($tags_array){
                $tagids = implode(',',$tags_array);
                $tags = M('member_tag_map')->field("{$prefix}tags.title")
                    ->join("{$prefix}tags ON {$prefix}tags.id = {$prefix}member_tag_map.tagid")
                    ->where("{$prefix}member_tag_map.uid = ".$item['uid'] ." AND {$prefix}member_tag_map.tagid in ($tagids) AND {$prefix}member_tag_map.type!='_import'")
                    ->select();
            }
            $list[$i]['tags'] = $tags;
		}

        $this->assign('list', $list);
        $this->assign('page', $page);

        $tasklist = M('task')->field("{$prefix}task.*")->select();
		$this->assign('tasklist', $tasklist);

        $annotations = M('member_annotation')->field("distinct annotation")
            ->where("auid = ".$uid)
            ->select();
        $this->assign('annotations', $annotations);

        $this->display('allanswers');
    }

    public function annotation(){
        $prefix = C('DB_PREFIX');
        $id = isset($_GET['id']) ? intval($_GET['id']) : false;
        $auid = $this->USER['uid'];
        $uid = I('post.uid', '', 'intval');
        $data['uid'] = I('post.uid', '', 'intval');
        $data['auid'] = $this->USER['uid'];
        $data['annotation'] = I('post.annotation', '', 'strip_tags');
        $data['status'] = '1';
        $data['date'] = time();


        if(isset($_GET['id'])){
            M('member_annotation')->where("id=$id")->delete();
                $this->success('删除标注成功！');
        }else{
            $annotation = I('post.annotation', '', 'strip_tags');
            if (empty($annotation)) {
                $this->error('标注不能为空');
            }

            if(!$id){
                $id = M('member_annotation')->data($data)->add();
                addlog('添加标注，ID：' . $id);
                $this->success('添加标注成功！');
            }
        }

    }
    public function delcomment(){
        $comment_id = isset($_REQUEST['comment_id']) ? $_REQUEST['comment_id'] : false;
        if ($comment_id) {
            M('task_questions_answers_comment')->where("comment_id=$comment_id")->delete();
            $this->success('删除评论成功！');
        }else{
            $this->error('参数错误！');
        }
    }
    public function comment(){
        $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : false;

        $data['answer_id'] = I('post.answer_id', '', 'intval');
        $data['subject_id'] = I('post.subject_id', '', 'intval');
        $data['uid'] = $this->USER['uid'];
        $data['comment'] = I('post.comment', '', 'strip_tags');
        $data['status'] = '1';
        $data['datetime'] = time();
        $del = I('post.del', '', 'strip_tags');

        if($del){
            M('task_questions_answers_comment')->where("comment_id=$comment_id")->delete();
                $this->success('删除评论成功！');
        }else{

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

            if(!$comment_id){
                $remark_id = M('task_questions_answers_comment')->data($data)->add();
                addlog('添加评论，ID：' . $remark_id);
                $this->success('评论成功！');
            } else {
                M('task_questions_answers_comment')->data(array('comment' => $comment))->where("comment_id=$comment_id")->save();
                addlog('编辑评论信息，会员UID：' . $uid);
                $this->success('修改评论成功！');

            }
        }

    }
    public function addlike(){
        $uid = $this->USER['uid'];

        $prefix = C('DB_PREFIX');

        $answer_id = isset($_REQUEST['answer_id']) ? $_REQUEST['answer_id'] : '';
        if($answer_id<>''){
            $answer = M('task_questions_answers')->where("answer_id='$answer_id'")->find();
            if($answer['answer_id']){
                $islike = $this->islike($answer_id,$uid);
                if($islike){
                    $this->success('已点赞');
                }else{
                    $subject_id = intval($answer['subject_id']);
                    $like_id = $this->dolike($answer_id,$subject_id,$uid);
                    if($like_id){
                        $this->success('点赞成功');
                    }else{
                        $this->error('参数错误！');
                    }
                }
            }else{
                $this->error('参数错误！');
            }

        }else{
            return $this->sendError(3000,'参数错误');
        }
    }
    public function islike($answer_id,$uid){
        $prefix = C('DB_PREFIX');

        $islike = M('task_questions_answers_like')->field("{$prefix}task_questions_answers_like.*")
            ->where("{$prefix}task_questions_answers_like.answer_id='$answer_id' AND {$prefix}task_questions_answers_like.uid='$uid'")
            ->count();

        return $islike;
    }
    public function dolike($answer_id,$subject_id,$uid){
        $prefix = C('DB_PREFIX');
        $data['answers_like_id'] = 0;
        $data['answer_id'] = $answer_id;
        $data['subject_id'] = $subject_id;
        $data['uid'] = $uid;
        $data['status'] = '1';
        $data['datetime'] = time();
        $like_id = M('task_questions_answers_like')->data($data)->add();
        M('task_questions_answers')->where("answer_id='$answer_id'")->setInc("like_hit");
        //addlog('点赞，ID：' . $like_id);

        return $like_id;
    }
    public function answers_question(){
        $data['answer_id'] = I('post.answer_id', '', 'intval');
        $data['subject_id'] = I('post.subject_id', '', 'intval');
        $data['uid'] = $this->USER['uid'];
        $data['remark'] = I('post.remark', '', 'strip_tags');
        $data['status'] = '1';
        $data['type'] = '1';
        $data['datetime'] = time();

        $remark = I('post.remark', '', 'strip_tags');
        if (empty($remark)) {
            $this->error('追问不能为空');
        }

        $remark_id = M('task_questions_answers_remark')->data($data)->add();
        addlog('添加追问，ID：' . $remark_id);
            $this->success('追问成功！');

    }
    public function message(){
        $answer_id = isset($_POST['answer_id']) ? intval($_POST['answer_id']) : false;
        $data['answer_id'] = I('post.answer_id', '', 'intval');
        $data['message'] = I('post.message', '', 'strip_tags');
        $message = I('post.message', '', 'strip_tags');

        if (empty($message)) {
            $this->error('点评不能为空');
        }

        if ($answer_id) {
            M('task_questions_answers')->data($data)->where("answer_id='{$answer_id}'")->save();
            addlog('编辑任务，ID：' . $answer_id);
            $this->success('更新备注成功！');

        } else {
            $this->error('参数错误');
        }

    }

    public function confirm(){
        $answer_id = isset($_GET['answer_id']) ? intval($_GET['answer_id']) : false;
        $data['answer_id'] = I('get.answer_id', '', 'intval');

        $status = I('get.status', '', 'intval');
        if($answer_id){
            if($status == '-1'){
                $data['status'] = '-1';

                M('task_questions_answers')->data($data)->where("answer_id='{$answer_id}'")->save();

                $answer = M('task_questions_answers')->where("answer_id='{$answer_id}'")->find();
                $task_id = $answer['task_id'];
                $task = M('task')->where("task_id='{$task_id}'")->find();
                $message_data = array();
                $message_data['uid'] = $answer['uid'];
                $message_data['type'] = 6;
                $message_data['message'] = '您有任务<a href="'.U('Home/Task/detail',array('task_id'=>$task_id['task_id'])).'">《'.$task['title'].'》</a>回答被作废,请重新提交！';
                $message_data['status'] = 0;
                $message_data['created'] = time();
                M('member_message')->data($message_data)->add();

                $this->success('回答作废成功！');
            } elseif($status == '2'){
                $data['status'] = '1';

                M('task_questions_answers')->data($data)->where("answer_id='{$answer_id}'")->save();
                $this->success('确认状态修改成功！');
            } else{
                $this->error('参数错误');
            }
        } else {
            $this->error('参数错误');
        }

    }


	public function answers()
    {
        $uid = $this->USER['uid'];

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
		$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;

		$this->assign('subject_id', $subject_id);

        $prefix = C('DB_PREFIX');

		$subject = M('task_subjects')->field("{$prefix}task_subjects.*,{$prefix}task.name as task_name")
			->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
			->where("{$prefix}task_subjects.subject_id = ".$subject_id)
			->find();
		$this->assign('subject', $subject);

		//项目标签
		$task_item = M('task')->field("{$prefix}task.*")
			->where("{$prefix}task.task_id = ".$subject['task_id'])
			->find();

		$taglist = M('tags')->field("distinct {$prefix}tags.*")
			->join("{$prefix}tag_package_groups ON {$prefix}tag_package_groups.taggroups_id = {$prefix}tags.groupid")
			->where("{$prefix}tags.status=1 AND {$prefix}tag_package_groups.tagspackage_id in (".$task_item['project_tags'].")")
			->select();

        $this->assign('taglist', $taglist);


		$count_questions = M('task_questions')->field("{$prefix}task_questions.*")
			->where("subject_id='".$subject_id."'")
			->count();
		$this->assign('count_questions', $count_questions);

		$questions = M('task_questions')->field("{$prefix}task_questions.*")
			->order("{$prefix}task_questions.topic asc")
			->where("{$prefix}task_questions.subject_id = ".$subject_id)
			->select();

		foreach($questions as $j=>$qitem){

			$questions[$j]['selectlist'] = M('task_questions_item')->field("{$prefix}task_questions_item.*")
				->order("{$prefix}task_questions_item.item_id asc")
				->where("{$prefix}task_questions_item.question_id = ".$qitem['question_id'] )
				->select();
		}
		$this->assign('questions', $questions);
		$surveydata = $this->get_string_from_list($questions);
		$this->assign('surveydata', $surveydata);

		$task_subjects = M('task_subjects')->field("{$prefix}task_subjects.*")
			->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
			->where("{$prefix}task.task_id = ".$subject['task_id'])
			->order("{$prefix}task_subjects.o asc")
			->select();
		$this->assign('task_subjects', $task_subjects);

		$o = 0;
		foreach($task_subjects as $i=>$item){
			if($item['subject_id'] == $subject_id){
				$o = $i;
			}
		}
		$this->assign('o', $o);

		$previd = $subject_id;
		$nextid = $subject_id;
		if( $o >=1){
			$previd = $task_subjects[$o-1]['subject_id'];
		}
		if( $o < count($task_subjects)-1){
			$nextid = $task_subjects[$o+1]['subject_id'];
		}
		$this->assign('previd', $previd);
		$this->assign('nextid', $nextid);

        $order = " {$prefix}task_questions_answers.answer_id DESC";

        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $count = M('task_questions_answers')->field("{$prefix}task_questions_answers.*,{$prefix}task.title as task_title,{$prefix}task_subjects.title as subject_title,{$prefix}task_subjects.description as subject_description,{$prefix}task_subjects.o as subject_o,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.head")
			->join("{$prefix}task_subjects ON {$prefix}task_questions_answers.subject_id = {$prefix}task_subjects.subject_id")
			->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
            ->join("{$prefix}member ON {$prefix}task_questions_answers.uid = {$prefix}member.uid")
            ->order($order)
            ->where("{$prefix}task_questions_answers.subject_id = ".$subject_id)
            ->count();

        $list = M('task_questions_answers')->field("{$prefix}task_questions_answers.*,{$prefix}task.title as task_title,{$prefix}task_subjects.title as subject_title,{$prefix}task_subjects.description as subject_description,{$prefix}task_subjects.o as subject_o,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.head")
			->join("{$prefix}task_subjects ON {$prefix}task_questions_answers.subject_id = {$prefix}task_subjects.subject_id")
			->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
            ->join("{$prefix}member ON {$prefix}task_questions_answers.uid = {$prefix}member.uid")
            ->order($order)
            ->where("{$prefix}task_questions_answers.subject_id = ".$subject_id)
            ->limit($offset . ',' . $pagesize)
            ->select();

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();

		foreach($list as $i=>$item){
			$order = "";
            $list[$i]['islike'] = $this->islike($item['answer_id'],$uid);
            $list[$i]['question_answers'] = $this->getAnswers($item['answer_id']);

			$questions = M('task_questions')->field("{$prefix}task_questions.*")
				->order("{$prefix}task_questions.topic asc")
				->where("{$prefix}task_questions.subject_id = ".$item['subject_id'])
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
            $remark = M('task_questions_answers_remark')->field("{$prefix}task_questions_answers_remark.*,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.head,{$prefix}member.admin")
                ->join("{$prefix}member ON {$prefix}task_questions_answers_remark.uid = {$prefix}member.uid")
                ->where("{$prefix}task_questions_answers_remark.answer_id = ".$item['answer_id'])
                ->select();

            $list[$i]['remark'] = $remark;

            $comment = M('task_questions_answers_comment')->field("{$prefix}task_questions_answers_comment.*,{$prefix}member.user,{$prefix}member.head")
                ->join("{$prefix}member ON {$prefix}task_questions_answers_comment.uid = {$prefix}member.uid")
                ->where("{$prefix}task_questions_answers_comment.answer_id = ".$item['answer_id'])
                ->select();

            $list[$i]['comment'] = $comment;

            $annotation = M('member_annotation')->field("{$prefix}member_annotation.*")
                ->where("{$prefix}member_annotation.uid = ".$item['uid'] ." AND {$prefix}member_annotation.auid = ".$uid)
                ->select();

            $list[$i]['annotation'] = $annotation;

			$list[$i]['questions'] = $questions;
		}

        $this->assign('list', $list);
        $this->assign('page', $page);

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

        $this->display('answers');
    }

	public function subjectstat()
    {
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
		$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;

        $prefix = C('DB_PREFIX');

		$task_item = M('task')->field("{$prefix}task.*")
			->where("{$prefix}task.task_id = ".$task_id)
			->find();
		$this->assign('task_item', $task_item);


        $list = M('task_subjects')->field("{$prefix}task_subjects.*")
            ->order("{$prefix}task_subjects.subject_id ASC")
            ->where("{$prefix}task_subjects.task_id = $task_id")
            ->select();


		foreach($list as $i=>$item){
			$order = "";

			$questions = M('task_questions')->field("{$prefix}task_questions.*")
				->order("{$prefix}task_questions.topic asc")
				->where("{$prefix}task_questions.subject_id = ".$item['subject_id'])
				->select();

			foreach($questions as $j=>$qitem){
				if($qitem['type'] != 'radio' && $qitem['type'] != 'check'){
					unset($questions[$j]);
					continue;
				}
				$allanswer = M('task_questions_answers_item')->field("count({$prefix}task_questions_answers_item.answer_item_id) as allanswer")
					->where("{$prefix}task_questions_answers_item.question_id = ".$qitem['question_id'] )
					->find();

				$selectlist = array();
				$selectlist = M('task_questions_item')->field("{$prefix}task_questions_item.*")
					->order("{$prefix}task_questions_item.item_id asc")
					->where("{$prefix}task_questions_item.question_id = ".$qitem['question_id'] )
					->select();

				foreach($selectlist as $ai=>$select){
					$answer = M('task_questions_answers_item')->field("count({$prefix}task_questions_answers_item.answer_item_id) as countanswer")
						->where("{$prefix}task_questions_answers_item.itemValue = ".$select['item_value']." AND {$prefix}task_questions_answers_item.question_id = ".$select['question_id'] )
						->find();
					$selectlist[$ai] = $select;
					$selectlist[$ai]['countanswer'] = $answer['countanswer'];
					$selectlist[$ai]['percent'] = round(intval($answer['countanswer'])/intval($allanswer['allanswer'])*100,2);

				}
				$questions[$j]['selectlist'] = $selectlist;
				$questions[$j]['allanswer'] = $allanswer['allanswer'];


			}
			$list[$i]['questions'] = $questions;


			$count_questions = M('task_questions')->field("{$prefix}task_questions.*")
				->where("subject_id='".$item['subject_id']."'")
				->count();
			$list[$i]['count_questions']= $count_questions;//题目数量
			$count_users_all = 0;

			$count_users_all = M('member')->field("{$prefix}member.uid")
				->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
				->where("{$prefix}auth_group_access.group_id in (".$task_item['research_group'].")")
				->count();

			$list[$i]['count_users_all'] = $count_users_all;//参与人数
            $count_users_completed = M('task_questions_answers')->field("{$prefix}task_questions_answers.answer_id")
                ->where("{$prefix}task_questions_answers.subject_id='".$item['subject_id']."'")
                ->count();
            $list[$i]['count_users_completed'] = $count_users_completed;//已完成
			$list[$i]['count_users_pending'] = 28;//进行中
		}
		//dump($list);

		$color = array();
		$color[] = ' ';
		$color[] = ' progress-bar-warning';
		$color[] = ' progress-bar-warningg';
		$color[] = ' progress-bar-danger';
		$color[] = ' progress-bar-pink';
		$color[] = ' progress-bar-inverse';

		$this->assign('color', $color);

        $this->assign('list', $list);
		$this->assign('task_id', $task_id);

        $this->display('subjectstat');
    }

	public function uploadimg(){
		$this->display('uploadimg');
	}
	public function designqfinish(){
		//edit=true&subject_id=1
	}
	public function members(){
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;
        $prefix = C('DB_PREFIX');

        $answer = isset($_GET['answer']) ? $_GET['answer'] : false;
        $question = isset($_GET['question']) ? $_GET['question'] : false;
        $city = isset($_GET['city']) ? $_GET['city'] : false;
        $start = isset($_GET['start']) ? $_GET['start'] : false;
        $end = isset($_GET['end']) ? $_GET['end'] : false;


        $where = [];

        $pull = "left";

        if($answer <> ''){
            $pull = "";
            if($answer == 'unfinished'){
                $task_sql = "{$prefix}member_task.uid != {$prefix}member.uid";
            } else if($answer == 'complete'){
                $task_sql = "{$prefix}member_task.uid = {$prefix}member.uid";
                $where[] = "{$prefix}member_task.status = '2'";
            } else if($answer == 'proceed'){
                $task_sql = "{$prefix}member_task.uid = {$prefix}member.uid";
                $where[] = "{$prefix}member_task.status = '1'";
            }
        } else {
            $task_sql = "{$prefix}member_task.uid = {$prefix}member.uid";
        }


        $task_pull = "left";
        if($question <> ''){
            $pull = "";
            $task_pull = "";
            $where[] = "{$prefix}task_questions_answers_remark.type = '1'";
            if($question == 'unfinished'){
                $task_question = "{$prefix}task_questions_answers_remark.answer_id != {$prefix}task_questions_answers.answer_id";
            } else if($question == 'complete'){
                $task_question = "{$prefix}task_questions_answers_remark.answer_id = {$prefix}task_questions_answers.answer_id";
                $where[] = "{$prefix}task_questions_answers_remark.status = '2'";
            } else if($question == 'proceed'){
                $task_question = " {$prefix}task_questions_answers_remark.answer_id = {$prefix}task_questions_answers.answer_id";
                $where[] = "{$prefix}task_questions_answers_remark.status = '1'";
            }
        } else {
            $task_question = "{$prefix}task_questions_answers_remark.answer_id = {$prefix}task_questions_answers.answer_id";

            // $where[] = "{$prefix}task_questions_answers_remark.type = '1'";
        }

        if($start <> ''){
            $where[] = "{$prefix}member_task.start_time >".strtotime($start);
        }
        if($end <> ''){
            $where[] = "{$prefix}member_task.start_time <".strtotime($end);
        }

        if($city <> ''){
            $where[] = "{$prefix}member.city = '$city'";
        }


        $task_item = M('task')->field("{$prefix}task.*")
            ->where("{$prefix}task.task_id = ".$task_id)
            ->find();

        $where[] = "{$prefix}auth_group_access.group_id in (".$task_item['research_group'].")";

        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }

        $this->assign('task_item', $task_item);

        $list = array();
        $count_users_all = 0;
        $count_users_completed = 0;
        $count_users_pending = 0;
        $count_subjects = 0;


        $count_subjects = M('task_subjects')->field("{$prefix}task_subjects.*")
            ->where("task_id='".$task_item['task_id']."'")
            ->count();

        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count_users_all = M('member')->field("{$prefix}member.uid")
            ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
            ->join("{$prefix}member_task ON {$prefix}member_task.uid = {$prefix}member.uid","$pull")
            ->join("{$prefix}task_questions_answers ON {$prefix}member_task.task_id = {$prefix}task_questions_answers.task_id","$task_pull")
            ->join("{$prefix}task_questions_answers_remark ON $task_question","$task_pull")
            ->where($wherestring)
            ->count("distinct {$prefix}member.uid ");

        $list = M('member')->field("distinct {$prefix}member.uid,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.city,{$prefix}member.phone,{$prefix}member.qq,{$prefix}member.head,{$prefix}member_task.answered,{$prefix}member_task.start_time,{$prefix}member_task.complete_time")
            ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
            ->join("{$prefix}member_task ON $task_sql","$pull")
            ->join("{$prefix}task_questions_answers ON {$prefix}member_task.task_id = {$prefix}task_questions_answers.task_id","$task_pull")
            ->join("{$prefix}task_questions_answers_remark ON $task_question","$task_pull")


            ->where($wherestring)
            ->limit($offset . ',' . $pagesize)
            ->select();

        $page = new \Think\Page($count_users_all, $pagesize);
        $page = $page->show();


        $this->assign('count_users_all', $count_users_all);//参与人数
        $this->assign('count_users_completed', $count_users_completed);//已完成
        $this->assign('count_users_pending', $count_users_pending);//进行中
        $this->assign('count_subjects', $count_subjects);//题目总数


        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->display('taskmembers');

	}
	public function taskcopy(){
		$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;

        $prefix = C('DB_PREFIX');

		$task_item = M('task')->field("{$prefix}task.*")
			->where("{$prefix}task.task_id = ".$task_id)
			->find();

		$data = array();
		$data = $task_item;
		$data['task_id'] = 0;
		$data['name'] = $task_item['name'].' (复制)';
		$data['title'] = $task_item['title'].' (复制)';
		$new_task_id = M('task')->data($data)->add();

		$subjects =  M('task_subjects')->field("{$prefix}task_subjects.*")
				->where("task_id='".$task_item['task_id']."'")
				->select();
		foreach($subjects as $subject){
			$data = array();
			$data = $subject;
			$data['subject_id'] = 0;
			$data['task_id'] = $new_task_id;
			$new_subject_id = M('task_subjects')->data($data)->add();

			$questions =  M('task_questions')->field("{$prefix}task_questions.*")
				->order("{$prefix}task_questions.topic ASC")
				->where("subject_id='".$subject['subject_id']."'")
				->select();
			foreach($questions as $question){
				$data = array();
				$data = $question;
				$data['question_id'] = 0;
				$data['subject_id'] = $new_subject_id;
				$new_question_id = M('task_questions')->data($data)->add();

				$question_items =  M('task_questions_item')->field("{$prefix}task_questions_item.*")
					->order("{$prefix}task_questions_item.item_id ASC")
					->where("{$prefix}task_questions_item.question_id = ".$question['question_id'] )
					->select();
				foreach($question_items as $item){
					$data = array();
					$data = $item;
					$data['item_id'] = 0;
					$data['subject_id'] = $new_subject_id;
					$data['question_id'] = $new_question_id;
					$new_questions_item_id = M('task_questions_item')->data($data)->add();
				}
			}

		}

		$this->success('任务复制成功',U('edit',array('task_id'=>$new_task_id)));
	}
	public function get_string_from_list($list){
		$prefix = C('DB_PREFIX');

		$qstr = '§§§7¤page§1§§undefined§§§2018';

		foreach($list as $item){
			$item['requir'] = $item['requir']=='1'?'true':'false';
			$item['norepeat'] = $item['norepeat']=='1'?'true':'false';
			$item['hasjump'] = $item['hasjump']=='1'?'true':'false';
			$item['needOnly'] = $item['needOnly']=='1'?'true':'false';
			$item['hasList'] = $item['hasList']=='1'?'true':'false';

			switch ($item['type']) {
				case "fileupload":
					$qstr .= '¤'.$item['type'].'§'.$item['topic'].'§'.$item['title'].'〒'.$item['keyword'].'〒'.$item['relation'].'§§'.$item['requir'].'§'.$item['width'].'§'.$item['ext'].'§'.$item['maxsize'].'§'.$item['ins'].'§'.$item['hasjump'].'§'.$item['anytimejumpto'];

					break;
				case "slider":
					$qstr .= '¤'.$item['type'].'§'.$item['topic'].'§'.$item['title'].'〒'.$item['keyword'].'〒'.$item['relation'].'§§'.$item['requir'].'§'.$item['minvalue'].'§'.$item['maxvalue'].'§'.$item['minvaluetext'].'§'.$item['maxvaluetext'].'§'.$item['ins'].'§'.$item['hasjump'].'§'.$item['anytimejumpto'];

					break;
				case "question":
					$qstr .= '¤'.$item['type'].'§'.$item['topic'].'§'.$item['title'].'〒'.$item['keyword'].'〒'.$item['relation'].'§§'.$item['height'].'§'.$item['maxword'].'§'.$item['requir'].'§'.$item['norepeat'].'§'.$item['default'].'§'.$item['ins'].'§'.$item['hasjump'].'§'.$item['anytimejumpto'].'§'.$item['verify'].'§'.$item['needOnly'].'〒'.$item['needsms'].'§'.$item['hasList'].'§'.$item['listId'].'§'.$item['width'].'§'.$item['underline'].'§'.$item['minword'];

					break;
				case "gapfill":
					$qstr .= '¤'.$item['type'].'§'.$item['topic'].'§'.$item['title'].'〒'.$item['keyword'].'〒'.$item['relation'].'§§'.$item['requir'].'§'.$item['gapcount'].'§'.$item['ins'].'§'.$item['hasjump'].'§'.$item['anytimejumpto'];

					break;
				case "sum":
					$qstr .= '¤'.$item['type'].'§'.$item['topic'].'§'.$item['title'].'〒'.$item['keyword'].'〒'.$item['relation'].'§§'.$item['requir'].'§'.$item['total'].'§'.$item['rowtitle'].'§'.$item['rowwidth'].'§§'.$item['ins'].'§'.$item['hasjump'].'§'.$item['anytimejumpto'];

					break;
				case "radio":
				case "check":
				case "radio_down":
				case "matrix":
				case "boolean":
					$isbool = false;
					if($item['type'] == "boolean"){
						$isbool = true;
						$item['type'] = "radio";
					}
					$qstr .= '¤'.$item['type'].'§'.$item['topic'].'§'.$item['title'].'〒'.$item['keyword'].'〒'.$item['relation'].'〒'.$item['mainWidth'].'§'.$item['tag'].'§';

					if ($item['type'] == "matrix") {
						$qstr .= $item['rowtitle'].'〒'.$item['rowtitle2'].'〒'.$item['columntitle'];
					} else {
						$qstr .= $item['numperrow'].'〒'.$item['randomChoice'];
					}
					$qstr .= '§'.$item['hasvalue'].'§'.$item['hasjump'].'§'.$item['anytimejumpto'].'§';

					if ($item['type'] == "check" || ($item['type']== "matrix" && $item['tag'] == "102")) {
						$qstr .= $item['requir'].','.$item['lowLimit'].','.$item['upLimit'].'§';
					} else {
						$qstr .= $item['requir'].'§';
					}

					if ($item['type']  == "matrix") {
						$qstr .= $item['rowwidth'].','.$item['randomRow'].'〒'.$item['rowwidth2'];

						if ($item['tag'] == "202") {
							$qstr .= '〒'.$item['minvalue'].'〒'.$item['maxvalue'];
						}
					}
					$qstr .= '§'. $item['ins'].'§';
					$qstr .=  $item['verify'].','.'§'. $item['referTopic'].'§'. $item['referedTopics'];

					$question_items =  M('task_questions_item')->field("{$prefix}task_questions_item.*")
						->order("{$prefix}task_questions_item.item_id ASC")
						->where("{$prefix}task_questions_item.question_id = ".$item['question_id'] )
						->select();
					if(count($question_items)){
						foreach($question_items as $question_item){

                            $question_item['item_tb'] = $question_item['item_tb']=='1'?'true':'false';
                            $question_item['item_tbr'] = $question_item['item_tbr']=='1'?'true':'false';
                            $question_item['item_imgtext'] = $question_item['item_imgtext']=='1'?'true':'false';
                            $question_item['item_huchi'] = $question_itemquestion_item['item_huchi']=='1'?'true':'false';

							$qstr .= '§'.$question_item['item_title'].'〒'.$question_item['item_radio'].'〒'.$question_item['item_value'].'〒'.$question_item['item_jump'].'〒'.$question_item['item_tb'].'〒'.$question_item['item_tbr'].'〒'.$question_item['item_img'].'〒'.$question_item['item_imgtext'].'〒'.$question_item['item_desc'].'〒'.$question_item['item_label'].'〒'.$question_item['item_huchi'];
						}
					}
					break;
				default:
					break;
			}
		}
		return $qstr;
	}
	public function taskmember(){
		$uid = isset($_GET['uid']) ? intval($_GET['uid']) : 0;
		$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;

        $prefix = C('DB_PREFIX');

        $task_item = M('task')->field("{$prefix}task.*")
            ->where("{$prefix}task.task_id = ".$task_id)
            ->find();
        $this->assign('task_item', $task_item);

        $member =  M('member')->field("{$prefix}member.*")
            ->where("{$prefix}member.uid=$uid")
            ->find();
		$this->assign('member', $member);

        $list = M('task_questions_answers')->field("{$prefix}task_questions_answers.*")
            ->join("{$prefix}task_subjects ON {$prefix}task_questions_answers.subject_id = {$prefix}task_subjects.subject_id")
            ->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
            ->order("{$prefix}task_questions_answers.answer_id DESC")
            ->where("{$prefix}task_questions_answers.task_id = '$task_id' AND {$prefix}task_questions_answers.uid = '$uid' ")
            ->select();

        foreach($list as $i=>$item){
            $order = "";

            $list[$i]['question_answers'] = $this->getAnswers($item['answer_id']);

            $questions = M('task_questions')->field("{$prefix}task_questions.*")
                ->order("{$prefix}task_questions.topic asc")
                ->where("{$prefix}task_questions.subject_id = ".$item['subject_id'])
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
            $list[$i]['questions'] = $questions;
        }
        $this->assign('list', $list);

        //标签
        $tags = M('tags')->field("{$prefix}tags.*")
            ->join("{$prefix}member_tag_map ON {$prefix}member_tag_map.tagid = {$prefix}tags.id")
            ->where("{$prefix}member_tag_map.uid='$uid'")
            ->select();
        $this->assign('tags', $tags);

        $this->display('taskmember');

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
            if($question['type'] == 'radio' || $question['type'] == 'check' ||($question['type'] == 'matrix' && ($question['tag'] !='202'))){
                $question_answers = M('task_questions_answers_item')->field("{$prefix}task_questions_answers_item.*,{$prefix}task_questions_item.item_title as itemvalue")
                    ->join("{$prefix}task_questions_item ON {$prefix}task_questions_item.question_id = {$prefix}task_questions_answers_item.question_id")
                    ->order("{$prefix}task_questions_answers_item.answer_item_id asc")
                    ->where("({$prefix}task_questions_item.item_value={$prefix}task_questions_answers_item.item_value AND {$prefix}task_questions_answers_item.answer_id = '".$answer['answer_id']."' AND {$prefix}task_questions_answers_item.question_id = '".$question['question_id']."')")
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
    public function exportExcel($expTitle,$expCellName,$expTableData){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = $xlsTitle.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);

        vendor("PHPExcel.PHPExcel");
        vendor("PHPExcel.PHPExcel.IOFactory");

        $objPHPExcel = new PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

        //$objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
       // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $expCellName[$i][1]);
        }
          // Miscellaneous glyphs, UTF-8
        for($i=0;$i<$dataNum;$i++){
          for($j=0;$j<$cellNum;$j++){
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), $expTableData[$i][$expCellName[$j][0]]);
          }
        }

        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    /**
     *
     * 导出Excel
     */
    function exportbytask(){//导出Excel
        $prefix = C('DB_PREFIX');
        $task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;
        $task = M('task')->where("task_id='$task_id'")->find();
        if (!$task) {
            $this->error('参数错误！');
        }

        $members = M('member')->field("distinct {$prefix}member.uid,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.phone,{$prefix}member.qq,{$prefix}member.head,{$prefix}member_task.answered")
            ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
            ->join("{$prefix}member_task ON {$prefix}member_task.uid = {$prefix}member.uid",'left')
            ->where("{$prefix}auth_group_access.group_id in (".$task['research_group'].")")
            ->order("{$prefix}member.uid asc")
            ->select();

        $subjects = M('task_subjects')->field("{$prefix}task_subjects.*")
            ->order("{$prefix}task_subjects.o asc")
            ->where("{$prefix}task_subjects.task_id = $task_id")
            ->select();

        $xlsCell  = array(
            array('uid','UID'),
            array('user','用户名')
        );
        foreach($subjects as $subject){
            $xlsCell[]  = array('Q'.$subject['o'],$subject['o'].'. '.$subject['title']);
        }

        $xlsData = array();
        foreach($members as $i => $member){
            $xlsData[$i]['uid'] = $member['uid'];
            $xlsData[$i]['user'] = $member['user'];
            $uid = $member['uid'];

            foreach($subjects as $subject){
                $subject_id = $subject['subject_id'];
                $subject_answer = M('task_questions_answers')->field("{$prefix}task_questions_answers.answer_id")
                    ->where(" {$prefix}task_questions_answers.subject_id = '$subject_id' AND {$prefix}task_questions_answers.uid='$uid'")
                    ->find();

                $answer_str = '';
                if($subject_answer['answer_id']){
                    $question_answers = $this->getAnswers($subject_answer['answer_id']);
                    $answer_str .= '['.$subject_answer['answer_id'].']';
                    foreach($question_answers as $q => $qanswer){
                        $answers = $qanswer['answers'];
                        $answer_str .= 'Q'.$qanswer['topic'].'. ';
                        foreach($answers as $a){
                            $answer_str .= $a['itemvalue'].' ';
                        }
                        $answer_str .= '; ';
                    }
                }
                $xlsData[$i]['Q'.$subject['o']] = $answer_str;
            }
        }

        $xlsName  = "exportbytask_".$task_id;
        $this->exportExcel($xlsName,$xlsCell,$xlsData);

    }
    public function exportbysubject(){//导出Excel
        $prefix = C('DB_PREFIX');

        $ids = implode(',', I('post.ids'));
        if (!$ids) {
            $this->error('参数错误！');
        }

        $subjects = M('task_subjects')->field("{$prefix}task_subjects.*")
            ->order("{$prefix}task_subjects.o asc")
            ->where("{$prefix}task_subjects.subject_id in(" . $ids . ")")
            ->select();

        $xlsCell  = array(
            array('sid','SID'),
            array('aid','AID'),
            array('subject','题目'),
            array('answer','回答'),
            array('user','用户名'),
            array('uid','UID')
        );

        $xlsData = array();


        foreach($subjects as $i => $subject){
            $subject_id = $subject['subject_id'];
            $subject_answers = M('task_questions_answers')->field("{$prefix}task_questions_answers.answer_id,{$prefix}member.uid,{$prefix}member.user")
                ->join("{$prefix}member ON {$prefix}task_questions_answers.uid = {$prefix}member.uid")
                ->where(" {$prefix}task_questions_answers.subject_id = '$subject_id' ")
                ->select();

            foreach($subject_answers as $j => $subject_answer){
                $answer_str = '';
                if($subject_answer['answer_id']){
                    $question_answers = $this->getAnswers($subject_answer['answer_id']);
                    $answer_str .= '|';
                    foreach($question_answers as $q => $qanswer){
                        $answers = $qanswer['answers'];
                        $answer_str .= 'Q'.$qanswer['topic'].'. ';
                        foreach($answers as $a){
                            $answer_str .= $a['itemvalue'].' ';
                        }
                        $answer_str .= '; ';
                    }
                }
                if($j == 0){
                    $xlsData[]= array(
                        'sid'=>$subject['subject_id'],
                        'aid'=>$subject_answer['answer_id'],
                        'subject'=>$subject['o'].'.'.$subject['title'],
                        'answer'=>$answer_str,
                        'user'=>$subject_answer['user'],
                        'uid'=>$subject_answer['uid']
                    );
                }else{
                    $xlsData[]= array(
                        'sid'=>$subject['subject_id'],
                        'aid'=>$subject_answer['answer_id'],
                        'subject'=>'',
                        'answer'=>$answer_str,
                        'user'=>$subject_answer['user'],
                        'uid'=>$subject_answer['uid']
                    );
                }
            }
        }

        $xlsName  = "exportbysubject_".$task_id.'_'.time();
        $this->exportExcel($xlsName,$xlsCell,$xlsData);

    }
    public function export(){
        $prefix = C('DB_PREFIX');

        $tasklist = M('task')->field("{$prefix}task.*")->select();

        $this->assign('tasklist', $tasklist);

        $questions = M('task_questions')->field("{$prefix}task_questions.question_id,{$prefix}task_questions.topic,{$prefix}task_questions.title as question_title,{$prefix}task_questions.subject_id,{$prefix}task_subjects.title as subject_title,{$prefix}task.task_id,{$prefix}task.title as task_title")
            ->join("{$prefix}task_subjects ON {$prefix}task_subjects.subject_id = {$prefix}task_questions.subject_id")
            ->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
            ->order("{$prefix}task.task_id asc,{$prefix}task_subjects.subject_id asc,{$prefix}task_questions.topic asc")
            ->where("{$prefix}task_questions.type = 'fileupload' OR {$prefix}task_subjects.direct_answer=1")
            ->select();

        $this->assign('questions', $questions);

        $diarylist = M('diary')->field("{$prefix}diary.*")->select();
        $this->assign('diarylist', $diarylist);

        $this->display('export');
    }
    public function taskexport(){//导出Excel
        $prefix = C('DB_PREFIX');
        $uids = isset($_POST['keyword']) ? $_POST['keyword'] : '';
        $task_ids = isset($_POST['task_id'])?implode(',',$_POST['task_id']):'';

        $xlsCell  = array(
            array('tid','TID'),
            array('sid','SID'),
            array('subject','题目'),
            array('answer','回答'),
            array('user','用户名'),
            array('uid','UID')
        );
        $xlsData = array();
        if(!$task_ids){
            $this->error('请选择任务');
        }
        $tasklist = M('task')->where("{$prefix}task.task_id in (".$task_ids.")")->select();
        foreach ($tasklist as $task) {
            $task_id = $task['task_id'];
            if($uids){
                $members = M('member')->field("distinct {$prefix}member.uid,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.phone,{$prefix}member.qq,{$prefix}member.head,{$prefix}member_task.answered")
                    ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
                    ->join("{$prefix}member_task ON {$prefix}member_task.uid = {$prefix}member.uid",'left')
                    ->where("{$prefix}auth_group_access.group_id in (".$task['research_group'].") AND {$prefix}member.uid in (".$uids.")")
                    ->order("{$prefix}member.uid asc")
                    ->select();
            }else{
                $members = M('member')->field("distinct {$prefix}member.uid,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.phone,{$prefix}member.qq,{$prefix}member.head,{$prefix}member_task.answered")
                    ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
                    ->join("{$prefix}member_task ON {$prefix}member_task.uid = {$prefix}member.uid",'left')
                    ->where("{$prefix}auth_group_access.group_id in (".$task['research_group'].") ")
                    ->order("{$prefix}member.uid asc")
                    ->select();
            }


            $subjects = M('task_subjects')->field("{$prefix}task_subjects.*")
                ->order("{$prefix}task_subjects.o asc")
                ->where("{$prefix}task_subjects.task_id = $task_id")
                ->select();

            foreach($members as $i => $member){
                $uid = $member['uid'];

                foreach($subjects as $subject){
                    $subject_id = $subject['subject_id'];
                    $subject_answer = M('task_questions_answers')->field("{$prefix}task_questions_answers.answer_id")
                        ->where(" {$prefix}task_questions_answers.subject_id = '$subject_id' AND {$prefix}task_questions_answers.uid='$uid'")
                        ->find();

                    $answer_str = '';
                    if($subject_answer['answer_id']){
                        $question_answers = $this->getAnswers($subject_answer['answer_id']);
                        //$answer_str .= '['.$subject_answer['answer_id'].']';
                        foreach($question_answers as $q => $qanswer){
                            $answers = $qanswer['answers'];
                            $answer_str .= 'Q'.$qanswer['topic'].'. ';
                            foreach($answers as $a){
                                $answer_str .= $a['itemvalue'].' ';
                            }
                            $answer_str .= '; ';
                        }
                        $xlsData[]= array(
                            'tid'=>$subject['task_id'],
                            'sid'=>$subject['subject_id'],
                            'subject'=>$subject['o'].'.'.$subject['title'],
                            'answer'=>$answer_str,
                            'user'=>$member['user'],
                            'uid'=>$member['uid']
                        );
                    }

                }
            }
        }

        $xlsName  = "taskexport_".$task_ids;
        $this->exportExcel($xlsName,$xlsCell,$xlsData);
    }
    public function diaryexport(){//导出Excel
        $prefix = C('DB_PREFIX');
        $uids = isset($_POST['keyword']) ? $_POST['keyword'] : '';
        $diary_ids = isset($_POST['diary_id'])?implode(',',$_POST['diary_id']):'';

        $xlsCell  = array(
            array('uid','UID'),
            array('user','用户名'),
            array('diary','日记任务'),
            array('diary_item','日记')
        );
        if(!$diary_ids){
            $this->error('请选择任务');
        }

        $diarys = M('diary')->where("{$prefix}diary.diary_id in (".$diary_ids.")")->select();

        $xlsData = array();
        foreach ($diarys as $diary) {
            $diary_id = $diary['diary_id'];
            if($uids){
                $members = M('member')->field("distinct {$prefix}member.uid,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.phone,{$prefix}member.qq,{$prefix}member.head")
                    ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
                    ->where("{$prefix}auth_group_access.group_id in (".$diary['research_group'].") AND {$prefix}member.uid in (".$uids.")")
                    ->order("{$prefix}member.uid asc")
                    ->select();
            }else{
                $members = M('member')->field("distinct {$prefix}member.uid,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.phone,{$prefix}member.qq,{$prefix}member.head")
                    ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
                    ->where("{$prefix}auth_group_access.group_id in (".$diary['research_group'].") ")
                    ->order("{$prefix}member.uid asc")
                    ->select();
            }

            foreach($members as $i => $member){
                $uid = $member['uid'];

               $diary_items = M('diary_items')->field("{$prefix}diary_items.*,{$prefix}member.user")
                    ->join("{$prefix}member ON {$prefix}diary_items.uid = {$prefix}member.uid")
                    ->order("{$prefix}diary_items.diary_item_id desc")
                    ->where("{$prefix}diary_items.diary_id='$diary_id' AND {$prefix}diary_items.uid='$uid'")
                    ->select();
                foreach ($diary_items as $diary_item) {
                    $xlsData[]= array(
                        'uid'=>$diary_item['uid'],
                        'user'=>$diary_item['user'],
                        'diary'=>$diary['name'],
                        'diary_item'=>$diary_item['content']
                    );
                }
            }
        }
        $xlsName  = "exportbydiary_".$diary_ids;

        $this->exportExcel($xlsName,$xlsCell,$xlsData);
    }
    public function exportanswers(){
        $uid = $this->USER['uid'];

        $task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;
        $answer_id = isset($_GET['answer_id']) ? intval($_GET['answer_id']) : 0;

        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $annota = isset($_GET['annotation']) ? $_GET['annotation'] : '';
        $haveremark = isset($_GET['haveremark']) ? $_GET['haveremark'] : '';

        $prefix = C('DB_PREFIX');

        $order = " {$prefix}task_questions_answers.task_id ASC, {$prefix}task_questions_answers.subject_id ASC";
        $where = array();
        if ($keyword <> '') {
            //$where[] = "{$prefix}task_subjects.title LIKE '%$keyword%'";
            $keywords = explode(',', $keyword);
            $w = array();
            foreach ($keywords as $val) {
                $w[] = " title = '$val'";
            }
            $tagids = M('tags')->where("(".implode(' OR ',$w).") AND status=1")->getField('id',true);

            if($tagids){
                $uids = M('member_tag_map')->where("tagid in (".implode(',',$tagids).")")->getField('uid',true);
                if($uids){
                    $where[] = " {$prefix}task_questions_answers.uid in (".implode(',',$uids).")";
                }else{
                    $where[] = " {$prefix}task_questions_answers.uid=0";
                }
            }else{
                $where[] = " {$prefix}task_questions_answers.uid=0";
            }
        }
        if ($task_id <> '') {
            $where[] = " {$prefix}task_subjects.task_id = $task_id";
        }
        if ($status <> '') {
            $where[] = " {$prefix}task_questions_answers.status = $status";
        }
        if ($answer_id <> '') {
            $where[] = " {$prefix}task_questions_answers.answer_id = $answer_id";
        }

        if($haveremark == 1){
            $where[] = " {$prefix}task_questions_answers_remark.remark_id > 0";
        }
        if($haveremark == -1){
            $where[] = " {$prefix}task_questions_answers_remark.remark_id is null";
        }
        if ($annota <> '') {
            $members = M('member_annotation')->where("annotation = '$annota' AND auid = ".$uid)->getField('uid',true);
            if($members){
                $where[] = " {$prefix}task_questions_answers.uid in (".implode(',',$members).")";
            }else{
                $where[] = " {$prefix}task_questions_answers.uid=0";
            }
        }

        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }

        $lists = M('task_questions_answers')->field("distinct {$prefix}task_questions_answers.*,{$prefix}task.name as task_name,{$prefix}task_subjects.title as subject_title,{$prefix}task_subjects.o as subject_o,{$prefix}member.user")
            ->join("{$prefix}task_subjects ON {$prefix}task_questions_answers.subject_id = {$prefix}task_subjects.subject_id")
            ->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
            ->join("{$prefix}member ON {$prefix}task_questions_answers.uid = {$prefix}member.uid")
            ->join("LEFT JOIN {$prefix}task_questions_answers_remark  ON {$prefix}task_questions_answers.answer_id = {$prefix}task_questions_answers_remark.answer_id")
            ->order($order)
            ->where($wherestring)
            ->select();

        $xlsCell  = array(
            array('tid','TID'),
            array('sid','SID'),
            array('subject','题目'),
            array('answer','回答'),
            array('user','用户名'),
            array('uid','UID')
        );
        $xlsData = array();
        foreach($lists as $i=>$item){
            if($item['answer_id']){
                $answer_str = '';
                $question_answers = $this->getAnswers($item['answer_id']);
                //$answer_str .= '['.$subject_answer['answer_id'].']';
                foreach($question_answers as $q => $qanswer){
                    $answers = $qanswer['answers'];
                    $answer_str .= 'Q'.$qanswer['topic'].'. ';
                    foreach($answers as $a){
                        $answer_str .= $a['itemvalue'].' ';
                    }
                    $answer_str .= '; ';
                }
                $xlsData[]= array(
                    'tid'=>$item['task_id'],
                    'sid'=>$item['subject_id'],
                    'subject'=>$item['subject_o'].'.'.$item['subject_title'],
                    'answer'=>$answer_str,
                    'user'=>$item['user'],
                    'uid'=>$item['uid']
                );
            }
        }

        $xlsName  = "exportanswers";

        $this->exportExcel($xlsName,$xlsCell,$xlsData);
    }
    public function fileexport(){
        $prefix = C('DB_PREFIX');
        $question_id = isset($_POST['question_id']) ? intval($_POST['question_id']) : 0;
        $answers_items = M('task_questions_answers_item')->field("{$prefix}task_questions_answers_item.subject_id,{$prefix}task_questions_answers_item.answer_id,{$prefix}task_questions_answers_item.type,{$prefix}task_questions_answers_item.itemValue as itemvalue,{$prefix}task_questions_answers_item.itemText as itemtext,{$prefix}member.user")
            ->join("{$prefix}task_questions_answers ON {$prefix}task_questions_answers.answer_id = {$prefix}task_questions_answers_item.answer_id")
            ->join("{$prefix}member ON {$prefix}task_questions_answers.uid = {$prefix}member.uid")

            ->order("{$prefix}task_questions_answers_item.answer_item_id desc")
            ->where("{$prefix}task_questions_answers_item.question_id = '$question_id' AND ({$prefix}task_questions_answers_item.type = 'fileupload' OR {$prefix}task_questions_answers_item.type='question_direct')")
            ->select();
        foreach ($answers_items as $key => $answer) {
            if($answer['type'] == 'question_direct'){
                $images = explode('|',$answer['itemtext']);
                $answers_items[$key]['images'] = $images;
            }
        }
        $this->assign('answers_items', $answers_items);

        $tasklist = M('task')->field("{$prefix}task.*")->select();

        $this->assign('tasklist', $tasklist);

        $questions = M('task_questions')->field("{$prefix}task_questions.question_id,{$prefix}task_questions.topic,{$prefix}task_questions.title as question_title,{$prefix}task_questions.subject_id,{$prefix}task_subjects.title as subject_title,{$prefix}task.task_id,{$prefix}task.title as task_title")
            ->join("{$prefix}task_subjects ON {$prefix}task_subjects.subject_id = {$prefix}task_questions.subject_id")
            ->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_subjects.task_id")
            ->order("{$prefix}task.task_id asc,{$prefix}task_subjects.subject_id asc,{$prefix}task_questions.topic asc")
            ->where("{$prefix}task_questions.type = 'fileupload'  OR {$prefix}task_subjects.direct_answer=1")
            ->select();

        $this->assign('questions', $questions);
        $this->display('export');
    }
    public function getQuestionItems($subject_id){
        $prefix = C('DB_PREFIX');

        $subject =  M('task_subjects')->field("{$prefix}task_subjects.*")
            ->where("{$prefix}task_subjects.subject_id='$subject_id'")
            ->find();

        $task_subjects = M('task_subjects')->field("{$prefix}task_subjects.task_id,{$prefix}task_subjects.subject_id,{$prefix}task_subjects.title,{$prefix}task_subjects.o")
            ->where("{$prefix}task_subjects.o < ".$subject['o'])
            ->order("{$prefix}task_subjects.o asc")
            ->select();

        $results = array();
        foreach($task_subjects as $i=>$task_subject){
            $questions = M('task_questions')->field("{$prefix}task_questions.question_id,{$prefix}task_questions.type,{$prefix}task_questions.topic,{$prefix}task_questions.title")
                ->order("{$prefix}task_questions.topic asc")
                ->where("{$prefix}task_questions.subject_id = ".$task_subject['subject_id'])
                ->select();
            foreach($questions as $k=>$question){
                if($question['type']=='radio' || $question['type']=='check'){
                    $question_items = M('task_questions_item')->field("{$prefix}task_questions_item.item_id,{$prefix}task_questions_item.item_title,{$prefix}task_questions_item.item_value")
                        ->order("{$prefix}task_questions_item.item_id asc")
                        ->where("({$prefix}task_questions_item.question_id = '".$question['question_id']."')")
                        ->select();
                    $questions[$k]['question_items'] = $question_items;
                }
                if($question['type']=='radio' || $question['type']=='check'||$question['type']=='question' || $question['type']=='slider'){

                }else{
                    unset($questions[$k]);
                }
            }

            if(count($questions)){
                $task_subjects[$i]['questions'] = $questions;
            }else{
                unset($task_subjects[$i]);
            }
        }
        $results = $task_subjects;

        return $results;

    }
    public function subjectrelation(){
        $prefix = C('DB_PREFIX');
        $subject_id = isset($_POST['subject_id']) ? intval($_POST['subject_id']) : false;
        $relation = isset($_POST['relation']) ? $_POST['relation'] : array();

        if ($subject_id) {
            $subject =  M('task_subjects')->field("{$prefix}task_subjects.*")
                ->where("{$prefix}task_subjects.subject_id='$subject_id'")
                ->find();
            M('task_subject_relation')->where("{$prefix}task_subject_relation.subject_id = '$subject_id'")->delete();

            foreach($relation as $subject_relation){
                $relation_subject_id = 0;
                $relation_question_id = 0;
                $relation_question_item_id = 0;
                $option = explode('-',$subject_relation['question_id']);
                $question_id = $option[0];
                $question = M('task_questions')->field("{$prefix}task_questions.*")
                    ->where("({$prefix}task_questions.question_id = '".$question_id."')")
                    ->find();
                $relation_subject_id = intval($question['subject_id']);
                $relation_question_id = intval($question['question_id']);

                $question_item_id = $option[1] ? $option[1] : 0;

                if($question_item_id){
                    $question_item = M('task_questions_item')->field("{$prefix}task_questions_item.*")
                        ->where("({$prefix}task_questions_item.item_id = '".$question_item_id."')")
                        ->find();
                    $relation_question_item_id = intval($question_item['item_id']);
                }

                $data = array();
                $data['relation_id'] = 0;
                $data['task_id'] = intval($subject['task_id']);
                $data['subject_id'] = $subject_id;
                $data['relation_subject_id'] = $relation_subject_id;
                $data['relation_question_id'] = $relation_question_id;
                $data['relation_question_item_id'] = $relation_question_item_id;
                $data['relation_compare'] = $subject_relation['compare'];
                $data['relation_value'] = $subject_relation['value'];

                M('task_subject_relation')->data($data)->add();
            }

        }
        $this->success('操作成功！',U('editsubject',array('id'=>$subject_id)));
    }
    public function oftenoptions(){
        $this->display('oftenoptions');
    }

    public function taskgroups()
    {

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';

        $prefix = C('DB_PREFIX');

        $order = '';
        $where = '';

        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = M('task_group')->field("{$prefix}task_group.*")
            ->order($order)
            ->where($where)
            ->count();

        $list = M('task_group')->field("{$prefix}task_group.*")
            ->order($order)
            ->where($where)
            ->limit($offset . ',' . $pagesize)
            ->select();
        foreach($list as $i=>$item){
            $taskgroup_id = $item['taskgroup_id'];
            $list[$i]['count_task'] = M('task')->field("{$prefix}task.*")
                ->where("{$prefix}task.taskgroup_id=$taskgroup_id")
                ->count();
        }

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->display('taskgroups');
    }


    public function delgroup()
    {
        $ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : false;
        if (!$ids) {
            $this->error('请勾选删除任务！');
        }
        if (!is_array($ids)) {
            $this->error('参数错误！');
        }

        $map['taskgroup_id'] = array('in', implode(',', $ids));
        if (M('task_group')->where($map)->delete()) {

            addlog('删除任务组ID：' . implode(',', $ids));
            $this->success('任务组删除成功！');
        } else {
            $this->error('参数错误！');
        }
    }
    public function addgroup()
    {

        $this->display('groupform');
    }
    public function editgroup($taskgroup_id = 0)
    {
        $prefix = C('DB_PREFIX');
        $taskgroup_id = isset($_GET['taskgroup_id']) ? intval($_GET['taskgroup_id']) : 0;
        $current = M('task_group')->where("taskgroup_id='$taskgroup_id'")->find();
        if (!$current) {
            $this->error('参数错误！');
        }

        $this->assign('current', $current);
        $this->display('groupform');
    }

    public function updategroup()
    {
        $taskgroup_id = isset($_POST['taskgroup_id']) ? intval($_POST['taskgroup_id']) : false;
        $data['taskgroup_id'] = I('post.taskgroup_id', '', 'intval');
        $data['title'] = I('post.title', '', 'strip_tags');
        $data['description'] = I('post.description', '', 'htmlspecialchars');
        $data['status'] = I('post.status', '', 'intval');

        if ($taskgroup_id) {
            M('task_group')->data($data)->where("taskgroup_id='{$taskgroup_id}'")->save();
            addlog('编辑任务组，ID：' . $taskgroup_id);
        } else {
            $data['created'] = time();
            M('task_group')->data($data)->add();
            addlog('新增任务组，名称：' . $data['title']);
        }

        $this->success('操作成功！',U('taskgroups'));
    }
    public function groupstatus()
    {
        $ids = implode(',', I('post.ids'));
        $status = I('get.status');
        if (!$ids) {
            $taskgroup_id = isset($_GET['taskgroup_id']) ? intval($_GET['taskgroup_id']) : false;
            if (!$taskgroup_id) {
                $this->error('参数错误！');
            }

            $taskgroup = M('task_group')->where('taskgroup_id=' . $taskgroup_id)->find();
            if (!$taskgroup) {
                $this->error('参数错误！');
            }

            $status = $taskgroup['status'];
            if ($status == 1) {
               $res = M('task_group')->data(array('status' => 0))->where('taskgroup_id=' . $taskgroup_id)->save();
            }
            if ($status != 1 ) {
                $res = M('task_group')->data(array('status' => 1))->where('taskgroup_id=' . $taskgroup_id)->save();
            }

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{
            $res = M('task_group')->data(array('status' => $status))->where('taskgroup_id in(' . $ids.')')->save();

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }

        }
    }
}