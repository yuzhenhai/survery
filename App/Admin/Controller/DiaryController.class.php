<?php
/**
 *
 * Copyright: Bodasoft
 * Author   ：dowei<douwei@bodait.com>
 * Date     ：2018-1-23
 * Version  ：1.0.0
 * Desc     ：日记任务控制器。
 *
 **/

namespace Admin\Controller;
use Think\Upload;

use PHPExcel_IOFactory;
use PHPExcel;

class DiaryController extends ComController
{
    public function index()
    {

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';

        $state = isset($_GET['state']) ? $_GET['state'] : '';

		$prefix = C('DB_PREFIX');

		$order = '';
        $where = "1 = 1 ";
        if ($field <> '') {
            $where .= " AND {$prefix}diary.identifier LIKE '%$field%'";
        }

        if ($keyword <> '') {
            $where .= " AND ({$prefix}diary.name LIKE '%$keyword%' OR {$prefix}diary.title LIKE '%$keyword%')";
        }
        if($state ==''){
            $where .= " AND ({$prefix}diary.state != '5')";
        }else{
            $where .= " AND ({$prefix}diary.state = '$state')";
        }
		$diary = M('diary');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $diary->field("{$prefix}diary.*")
            ->order($order)
            ->where($where)
            ->count();

        $list = $diary->field("{$prefix}diary.*")
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


			$count_users_all = 0;
            if($item['research_group']){
    			$count_users_all = M('member')->field("{$prefix}member.uid")
    				->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
    				->where("{$prefix}auth_group_access.group_id in (".$item['research_group'].")")
    				->count();
            }

            $count_users_completed = M('member_diary')->field("{$prefix}member_diary.id")
                    ->where("{$prefix}member_diary.diary_id = ".$item['diary_id']." AND status = 2")
                    ->count();
            $count_users_pending = M('member_diary')->field("{$prefix}member_diary.id")
                    ->where("{$prefix}member_diary.diary_id = ".$item['diary_id']." AND status = 1")
                    ->count();
			$list[$i]['count_users_all'] = $count_users_all;//参与人数
			$list[$i]['count_users_completed'] = $count_users_completed;//已完成
			$list[$i]['count_users_pending'] = $count_users_pending;//进行中

            $count_remark_all = M('diary_items_remark')->field("{$prefix}diary_items_remark.remark_id")
                    ->join("{$prefix}diary_items ON {$prefix}diary_items.diary_item_id = {$prefix}diary_items_remark.diary_item_id")
                    ->where("{$prefix}diary_items.diary_id = ".$item['diary_id'])
                    ->count();
            $count_remark_pending = M('diary_items_remark')->field("{$prefix}diary_items_remark.remark_id")
                    ->join("{$prefix}diary_items ON {$prefix}diary_items.diary_item_id = {$prefix}diary_items_remark.diary_item_id")
                    ->where("{$prefix}diary_items.diary_id = ".$item['diary_id'] ." AND {$prefix}diary_items_remark.status = 1 AND {$prefix}diary_items_remark.type = 1")
                    ->count();
            $count_remark_done = M('diary_items_remark')->field("{$prefix}diary_items_remark.remark_id")
                    ->join("{$prefix}diary_items ON {$prefix}diary_items.diary_item_id = {$prefix}diary_items_remark.diary_item_id")
                    ->where("{$prefix}diary_items.diary_id = ".$item['diary_id'] ." AND {$prefix}diary_items_remark.status = 2 AND {$prefix}diary_items_remark.type = 1")
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
            $this->error('请勾选删除日记任务！');
        }
        if (!is_array($ids)) {
			$this->error('参数错误！');
        }

        $map['diary_id'] = array('in', implode(',', $ids));
        if (M('diary')->where($map)->delete()) {

            addlog('删除日记任务ID：' . implode(',', $ids));
            $this->success('日记任务删除成功！');
        } else {
            $this->error('参数错误！');
        }
    }

    public function edit($diary_id = 0)
    {
		$prefix = C('DB_PREFIX');
        $diary_id = isset($_GET['diary_id']) ? intval($_GET['diary_id']) : 0;
        $diary = M('diary');
        $currentdiary = $diary->where("diary_id='$diary_id'")->find();
        if (!$currentdiary) {
            $this->error('参数错误！');
        }


		$have_research_group = array();
		$have_researcher = array();
		$have_customer_group = array();
		$have_project_tags = array();

		if($currentdiary){
			$have_research_group = explode(',',$currentdiary['research_group']);
			$have_researcher = explode(',',$currentdiary['researcher']);
			$have_customer_group = explode(',',$currentdiary['customer_group']);
			$have_project_tags = explode(',',$currentdiary['project_tags']);
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


        $this->assign('currentdiary', $currentdiary);
        $this->display('form');
    }

    public function update()
    {
        $diary_id = isset($_POST['diary_id']) ? intval($_POST['diary_id']) : false;
        $data['diary_id'] = I('post.diary_id', '', 'intval');
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
        if($diary_id){
             $diary = M('diary')->where('diary_id=' . $diary_id)->find();
             $data['image'] = $diary['image'];
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
        if ($diary_id) {
            M('diary')->data($data)->where("diary_id='{$diary_id}'")->save();
            addlog('编辑日记任务，ID：' . $diary_id);
        } else {
            $diary_id = M('diary')->data($data)->add();
            addlog('新增日记任务，名称：' . $data['title']);
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


        $this->display('form');
    }

    public function status()
    {
        $ids = implode(',', I('post.ids'));
        $status = I('post.status');
        if (!$ids) {
            $diary_id = isset($_GET['diary_id']) ? intval($_GET['diary_id']) : false;
            if (!$diary_id) {
                $this->error('参数错误！');
            }

            $diary = M('diary')->where('diary_id=' . $diary_id)->find();
            if (!$diary) {
                $this->error('参数错误！');
            }

			$status = $diary['status'];
			if ($status == 1) {
			   $res = M('diary')->data(array('status' => 0))->where('diary_id=' . $diary_id)->save();
			}
			if ($status != 1 ) {
				$res = M('diary')->data(array('status' => 1))->where('diary_id=' . $diary_id)->save();
			}

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{
            $res = M('diary')->data(array('status' => $status))->where('diary_id in(' . $ids.')')->save();

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
        $diary_id = isset($_GET['diary_id']) ? intval($_GET['diary_id']) : 0;
        if (!$state) {
            $this->error('参数错误！');
        }
        if (!$diary_id) {
            $this->error('参数错误！');
        }
        $diary = M('diary')->where("{$prefix}diary.diary_id='$diary_id'")->find();
        if (!$diary) {
            $this->error('参数错误！');
        }

        $res = M('diary')->data(array('state' => $state))->where('diary_id=' . $diary_id)->save();
        if ($res) {
            if($state == 1){

                $list = M('member')->field("distinct {$prefix}member.uid")
                    ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
                    ->where("{$prefix}auth_group_access.group_id in (".$diary['research_group'].")")
                    ->select();
                foreach($list as $item){
                    $message_data = array();
                    $message_data['uid'] = $item['uid'];
                    $message_data['type'] = 1;
                    $message_data['message'] = '您有新日记任务《'.$diary['title'].'》,请及时完成！';
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

    public function items()
    {
        $uid = $this->USER['uid'];

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $diary_id = isset($_GET['diary_id']) ? intval($_GET['diary_id']) : 0;
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
        $annota = isset($_GET['annotation']) ? $_GET['annotation'] : '';
        $haveremark = isset($_GET['haveremark']) ? $_GET['haveremark'] : '';

        $prefix = C('DB_PREFIX');

        $order = " {$prefix}diary_items.diary_item_id DESC";
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
                    $where[] = " {$prefix}diary_items.uid in (".implode(',',$uids).")";
                }else{
                    $where[] = " {$prefix}diary_items.uid=0";
                }
            }else{
                $where[] = " {$prefix}diary_items.uid=0";
            }
        }
        if ($diary_id <> '') {
            $where[] = " {$prefix}diary.diary_id = $diary_id";
        }
        if ($status <> '') {
            $where[] = " {$prefix}diary_items.status = $status";
        }
        if($haveremark == 1){
            $where[] = " {$prefix}diary_items_remark.remark_id > 0";
        }
        if($haveremark == -1){
            $where[] = " {$prefix}diary_items_remark.remark_id is null";
        }
        if ($annota <> '') {
            $members = M('member_annotation')->where("annotation = '$annota' AND auid = ".$uid)->getField('uid',true);
            if($members){
                $uids = implode(',',$members);
                $where[] = " {$prefix}diary_items.uid in ($uids)";
            }
        }
        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }

        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $count = M('diary_items')
            ->join("LEFT JOIN {$prefix}diary_items_remark  ON {$prefix}diary_items.diary_item_id = {$prefix}diary_items_remark.diary_item_id")
            ->join("{$prefix}diary ON {$prefix}diary.diary_id = {$prefix}diary_items.diary_id")
            ->join("{$prefix}member ON {$prefix}diary_items.uid = {$prefix}member.uid")
            ->order($order)
            ->where($wherestring)
            ->count();

        $lists = M('diary_items')->field("distinct {$prefix}diary_items.*,{$prefix}diary.name as diary_name,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.head,{$prefix}diary.project_tags")
            ->join("LEFT JOIN {$prefix}diary_items_remark  ON {$prefix}diary_items.diary_item_id = {$prefix}diary_items_remark.diary_item_id")
            ->join("{$prefix}diary ON {$prefix}diary.diary_id = {$prefix}diary_items.diary_id")
            ->join("{$prefix}member ON {$prefix}diary_items.uid = {$prefix}member.uid")
            ->order($order)
            ->where($wherestring)
            ->limit($offset . ',' . $pagesize)
            ->select();

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $list = array();

        foreach($lists as $i=>$item){
            $list[$i] = $item;
            $remark = M('diary_items_remark')->field("{$prefix}diary_items_remark.*,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.head,{$prefix}member.admin")
                ->join("{$prefix}member ON {$prefix}diary_items_remark.uid = {$prefix}member.uid")
                ->where("{$prefix}diary_items_remark.diary_item_id = ".$item['diary_item_id'])
                ->select();

            $list[$i]['remark'] = $remark;

            $comment = M('diary_items_comment')->field("{$prefix}diary_items_comment.*,{$prefix}member.user,{$prefix}member.head")
                ->join("{$prefix}member ON {$prefix}diary_items_comment.uid = {$prefix}member.uid")
                ->where("{$prefix}diary_items_comment.diary_item_id = ".$item['diary_item_id'])
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

        $diarylist = M('diary')->field("{$prefix}diary.*")->select();

        $this->assign('diarylist', $diarylist);

        $annotations = M('member_annotation')->field("distinct annotation")
            ->where("auid = ".$uid)
            ->select();
        $this->assign('annotations', $annotations);

        $this->display('items');
    }
    public function delcomment(){
        $comment_id = isset($_REQUEST['comment_id']) ? $_REQUEST['comment_id'] : false;
        if ($comment_id) {
            M('diary_items_comment')->where("comment_id=$comment_id")->delete();
            $this->success('删除评论成功！');
        }else{
            $this->error('参数错误！');
        }
    }
    public function comment(){
        $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : false;

        $data['diary_id'] = I('post.diary_id', '', 'intval');
        $data['diary_item_id'] = I('post.diary_item_id', '', 'intval');
        $data['uid'] = $this->USER['uid'];
        $data['comment'] = I('post.comment', '', 'strip_tags');
        $data['status'] = '1';
        $data['datetime'] = time();
        $del = I('post.del', '', 'strip_tags');

        if($del){
            M('diary_items_comment')->where("comment_id=$comment_id")->delete();
                $this->success('删除评论成功！');
        }else{

            $diary_id = I('post.diary_id', '', 'intval');
            $diary_item_id = I('post.diary_item_id', '', 'intval');
            if (empty($diary_id)) {
                $this->error('参数错误！');
            }
            if (empty($diary_item_id)) {
                $this->error('参数错误！');
            }

            $comment = I('post.comment', '', 'strip_tags');
            if (empty($comment)) {
                $this->error('评论不能为空');
            }

            if(!$comment_id){
                $remark_id = M('diary_items_comment')->data($data)->add();
                addlog('添加评论，ID：' . $remark_id);
                $this->success('评论成功！');
            } else {
                M('diary_items_comment')->data(array('comment' => $comment))->where("comment_id=$comment_id")->save();
                addlog('编辑评论信息，会员UID：' . $uid);
                $this->success('修改评论成功！');

            }
        }

    }
    public function confirm(){
        $diary_item_id = isset($_GET['diary_item_id']) ? intval($_GET['diary_item_id']) : false;
        $data['diary_item_id'] = I('get.diary_item_id', '', 'intval');

        $status = I('get.status', '', 'intval');
        if($diary_item_id){
            if($status == '-1'){
                $data['status'] = '-1';

                M('diary_items')->data($data)->where("diary_item_id='{$diary_item_id}'")->save();

                /*

                $diary_item = M('diary_items')->where("diary_item_id='{$diary_item_id}'")->find();
                $diary_id = $diary_item['diary_id'];
                $diary = M('diary')->where("diary_id='{$diary_id}'")->find();
                $message_data = array();
                $message_data['uid'] = $answer['uid'];
                $message_data['type'] = 6;
                $message_data['message'] = '您有日记任务<a href="'.U('Home/diary/detail',array('diary_id'=>$diary_id['diary_id'])).'">《'.$diary['title'].'》</a>回答被作废,请重新提交！';
                $message_data['status'] = 0;
                $message_data['created'] = time();
                M('member_message')->data($message_data)->add();
                */

                $this->success('回答作废成功！');
            } elseif($status == '2'){
                $data['status'] = '1';

                M('diary_items')->data($data)->where("diary_item_id='{$diary_item_id}'")->save();
                $this->success('确认状态修改成功！');
            } else{
                $this->error('参数错误');
            }
        } else {
            $this->error('参数错误');
        }

    }
    public function addlike(){
        $uid = $this->USER['uid'];

        $prefix = C('DB_PREFIX');

        $diary_item_id = isset($_REQUEST['diary_item_id']) ? $_REQUEST['diary_item_id'] : '';
        if($diary_item_id<>''){
            $diary_item = M('diary_items')->where("diary_item_id='$diary_item_id'")->find();
            if($diary_item['diary_id']){
                $islike = $this->islike($diary_item_id,$uid);
                if($islike){
                    $this->success('已点赞');
                }else{
                    $diary_id = intval($diary_item['diary_id']);
                    $like_id = $this->dolike($diary_id,$diary_item_id,$uid);
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
    public function islike($diary_item_id,$uid){
        $prefix = C('DB_PREFIX');

        $islike = M('diary_items_like')->field("{$prefix}diary_items_like.*")
            ->where("{$prefix}diary_items_like.diary_item_id='$diary_item_id' AND {$prefix}diary_items_like.uid='$uid'")
            ->count();

        return $islike;
    }
    public function dolike($diary_id,$diary_item_id,$uid){
        $data['diary_like_id'] = 0;
        $data['diary_id'] = $diary_id;
        $data['diary_item_id'] = $diary_item_id;
        $data['uid'] = $uid;
        $data['status'] = '1';
        $data['datetime'] = time();
        $like_id = M('diary_items_like')->data($data)->add();
        M('diary_items')->where("diary_item_id='$diary_item_id'")->setInc("like_hit");

        //addlog('点赞，ID：' . $like_id);
        return $like_id;
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
    public function message(){
        $diary_item_id = isset($_POST['diary_item_id']) ? intval($_POST['diary_item_id']) : false;
        $data['diary_item_id'] = I('post.diary_item_id', '', 'intval');
        $data['message'] = I('post.message', '', 'strip_tags');
        $message = I('post.message', '', 'strip_tags');

        if (empty($message)) {
            $this->error('点评不能为空');
        }

        if ($diary_item_id) {
            M('diary_items')->data($data)->where("diary_item_id='{$diary_item_id}'")->save();
            addlog('编辑日记任务，ID：' . $diary_item_id);
            $this->success('更新备注成功！');

        } else {
            $this->error('参数错误');
        }

    }
    public function doremark(){
        $data['diary_id'] = I('post.diary_id', '', 'intval');
        $data['diary_item_id'] = I('post.diary_item_id', '', 'intval');
        $data['uid'] = $this->USER['uid'];
        $data['remark'] = I('post.remark', '', 'strip_tags');
        $data['status'] = '1';
        $data['type'] = '1';
        $data['datetime'] = time();

        $remark = I('post.remark', '', 'strip_tags');
        if (empty($remark)) {
            $this->error('追问不能为空');
        }

        $remark_id = M('diary_items_remark')->data($data)->add();
        addlog('添加追问，ID：' . $remark_id);
        $this->success('追问成功！');
    }
    public function uploadimg(){
		$this->display('uploadimg');
	}
	public function designqfinish(){
		//edit=true&diary_item_id=1
	}
	public function members(){
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $diary_id = isset($_GET['diary_id']) ? intval($_GET['diary_id']) : 0;
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
                $diary_sql = "{$prefix}member_diary.uid != {$prefix}member.uid";
            } else if($answer == 'complete'){
                $diary_sql = "{$prefix}member_diary.uid = {$prefix}member.uid";
                $where[] = "{$prefix}member_diary.status = '2'";
            } else if($answer == 'proceed'){
                $diary_sql = "{$prefix}member_diary.uid = {$prefix}member.uid";
                $where[] = "{$prefix}member_diary.status = '1'";
            }
        } else {
            $diary_sql = "{$prefix}member_diary.uid = {$prefix}member.uid";
        }


        $diary_pull = "left";
        if($question <> ''){
            $pull = "";
            $diary_pull = "";
            $where[] = "{$prefix}diary_items_remark.type = '1'";
            if($question == 'unfinished'){
                $diary_question = "{$prefix}diary_items_remark.diary_item_id != {$prefix}diary_items.diary_item_id";
            } else if($question == 'complete'){
                $diary_question = "{$prefix}diary_items_remark.diary_item_id = {$prefix}diary_items.diary_item_id";
                $where[] = "{$prefix}diary_items_remark.status = '2'";
            } else if($question == 'proceed'){
                $diary_question = " {$prefix}diary_items_remark.diary_item_id = {$prefix}diary_items.diary_item_id";
                $where[] = "{$prefix}diary_items_remark.status = '1'";
            }
        } else {
            $diary_question = "{$prefix}diary_items_remark.diary_item_id = {$prefix}diary_items.diary_item_id";

            // $where[] = "{$prefix}diary_items_remark.type = '1'";
        }

        if($start <> ''){
            $where[] = "{$prefix}member_diary.start_time >".strtotime($start);
        }
        if($end <> ''){
            $where[] = "{$prefix}member_diary.start_time <".strtotime($end);
        }

        if($city <> ''){
            $where[] = "{$prefix}member.city = '$city'";
        }


        $diary_item = M('diary')->field("{$prefix}diary.*")
            ->where("{$prefix}diary.diary_id = ".$diary_id)
            ->find();

        $where[] = "{$prefix}auth_group_access.group_id in (".$diary_item['research_group'].")";

        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }

        $this->assign('diary_item', $diary_item);

        $list = array();
        $count_users_all = 0;
        $count_users_completed = 0;
        $count_users_pending = 0;
        $count_subjects = 0;


        $pagesize = 20;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count_users_all = M('member')->field("{$prefix}member.uid")
            ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
            ->join("{$prefix}member_diary ON {$prefix}member_diary.uid = {$prefix}member.uid","$pull")
            ->join("{$prefix}diary_items ON {$prefix}member_diary.diary_id = {$prefix}diary_items.diary_id","$diary_pull")
            ->join("{$prefix}diary_items_remark ON $diary_question","$diary_pull")
            ->where($wherestring)
            ->count("distinct {$prefix}member.uid ");

        $list = M('member')->field("distinct {$prefix}member.uid,{$prefix}member.user,{$prefix}member.realname,{$prefix}member.city,{$prefix}member.phone,{$prefix}member.qq,{$prefix}member.head,{$prefix}member_diary.start_time,{$prefix}member_diary.complete_time")
            ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
            ->join("{$prefix}member_diary ON $diary_sql","$pull")
            ->join("{$prefix}diary_items ON {$prefix}member_diary.diary_id = {$prefix}diary_items.diary_id","$diary_pull")
            ->join("{$prefix}diary_items_remark ON $diary_question","$diary_pull")


            ->where($wherestring)
            ->limit($offset . ',' . $pagesize)
            ->select();

        $page = new \Think\Page($count_users_all, $pagesize);
        $page = $page->show();


        $this->assign('count_users_all', $count_users_all);//参与人数
        $this->assign('count_users_completed', $count_users_completed);//已完成
        $this->assign('count_users_pending', $count_users_pending);//进行中


        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->display('members');

	}

	public function member(){
		$uid = isset($_GET['uid']) ? intval($_GET['uid']) : 0;
		$diary_id = isset($_GET['diary_id']) ? intval($_GET['diary_id']) : 0;

        $prefix = C('DB_PREFIX');

        $diary_item = M('diary')->field("{$prefix}diary.*")
            ->where("{$prefix}diary.diary_id = ".$diary_id)
            ->find();
        $this->assign('diary_item', $diary_item);

        $member =  M('member')->field("{$prefix}member.*")
            ->where("{$prefix}member.uid=$uid")
            ->find();
		$this->assign('member', $member);

        $list = M('diary_items')->field("{$prefix}diary_items.*")
            ->join("{$prefix}diary ON {$prefix}diary.diary_id = {$prefix}diary_items.diary_id")
            ->order("{$prefix}diary_items.diary_item_id DESC")
            ->where("{$prefix}diary_items.diary_id = '$diary_id' AND {$prefix}diary_items.uid = '$uid' ")
            ->select();

        $this->assign('list', $list);

        //标签
        $tags = M('tags')->field("{$prefix}tags.*")
            ->join("{$prefix}member_tag_map ON {$prefix}member_tag_map.tagid = {$prefix}tags.id")
            ->where("{$prefix}member_tag_map.uid='$uid'")
            ->select();
        $this->assign('tags', $tags);

        $this->display('member');

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
    public function exportbydiary(){//导出Excel
        $prefix = C('DB_PREFIX');
        $diary_id = isset($_GET['diary_id']) ? intval($_GET['diary_id']) : 0;
        $diary = M('diary')->where("diary_id='$diary_id'")->find();
        if (!$diary) {
            $this->error('参数错误！');
        }

        $xlsCell  = array(
            array('uid','UID'),
            array('user','用户名'),
            array('diary','日记任务'),
            array('diary_item','日记')
        );

        $diary_items = M('diary_items')->field("{$prefix}diary_items.*,{$prefix}member.user")

            ->join("{$prefix}member ON {$prefix}diary_items.uid = {$prefix}member.uid")
            ->order("{$prefix}diary_items.diary_item_id desc")
            ->where("{$prefix}diary_items.diary_id='$diary_id'")
            ->select();

        $xlsData = array();
        foreach($diary_items as $i => $item){
            $xlsData[$i]['uid'] = $item['uid'];
            $xlsData[$i]['user'] = $item['user'];
            $xlsData[$i]['diary'] = $diary['name'];
            $xlsData[$i]['diary_item'] = $item['content'];
        }

        $xlsName  = "exportbydiary_".$diary_id;
        $this->exportExcel($xlsName,$xlsCell,$xlsData);

    }
    public function exportitems(){
        $uid = $this->USER['uid'];

        $diary_id = isset($_GET['diary_id']) ? intval($_GET['diary_id']) : 0;
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
        $annota = isset($_GET['annotation']) ? $_GET['annotation'] : '';
        $haveremark = isset($_GET['haveremark']) ? $_GET['haveremark'] : '';

        $prefix = C('DB_PREFIX');

        $order = " {$prefix}diary_items.diary_id ASC";
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
                    $where[] = " {$prefix}diary_items.uid in (".implode(',',$uids).")";
                }else{
                    $where[] = " {$prefix}diary_items.uid=0";
                }
            }else{
                $where[] = " {$prefix}diary_items.uid=0";
            }
        }
        if ($diary_id <> '') {
            $where[] = " {$prefix}diary.diary_id = $diary_id";
        }
        if ($status <> '') {
            $where[] = " {$prefix}diary_items.status = $status";
        }
        if($haveremark == 1){
            $where[] = " {$prefix}diary_items_remark.remark_id > 0";
        }
        if($haveremark == -1){
            $where[] = " {$prefix}diary_items_remark.remark_id is null";
        }
        if ($annota <> '') {
            $members = M('member_annotation')->where("annotation = '$annota' AND auid = ".$uid)->getField('uid',true);
            if($members){
                $uids = implode(',',$members);
                $where[] = " {$prefix}diary_items.uid in ($uids)";
            }
        }
        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }

        $lists = M('diary_items')->field("distinct {$prefix}diary_items.*,{$prefix}diary.name as diary_name,{$prefix}member.user")
            ->join("LEFT JOIN {$prefix}diary_items_remark  ON {$prefix}diary_items.diary_item_id = {$prefix}diary_items_remark.diary_item_id")
            ->join("{$prefix}diary ON {$prefix}diary.diary_id = {$prefix}diary_items.diary_id")
            ->join("{$prefix}member ON {$prefix}diary_items.uid = {$prefix}member.uid")
            ->order($order)
            ->where($wherestring)
            ->select();


        $xlsCell  = array(
            array('diary_id','ID'),
            array('diary_name','日记任务'),
            array('diary_item','日记'),
            array('uid','UID'),
            array('user','用户名')
        );
        $xlsData = array();
        foreach($lists as $i=>$diary_item){
            $xlsData[]= array(
                'diary_id'=>$diary_item['diary_id'],
                'diary_name'=>$diary_item['diary_name'],
                'diary_item'=>$diary_item['content'],
                'uid'=>$diary_item['uid'],
                'user'=>$diary_item['user'],
            );
        }

        $xlsName  = "exportitems";

        $this->exportExcel($xlsName,$xlsCell,$xlsData);
    }
}