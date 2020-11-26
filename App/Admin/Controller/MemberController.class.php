<?php
/**
 *
 * Copyright: Bodasoft
 * Author   ：dowei<douwei@bodait.com>
 * Date     ：2018-1-23
 * Version  ：1.0.0
 * Desc     ：用户控制器
 *
 **/

namespace Admin\Controller;

use Think\Upload;
use Think\Image;

use PHPExcel_IOFactory;
use PHPExcel;

class MemberController extends ComController
{
    public function index()
    {

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
        $close = isset($_GET['close']) ? $_GET['close'] : '0';
        $where = array();

        $prefix = C('DB_PREFIX');
        if ($order == 'asc') {
            $order = "{$prefix}member.t asc";
        } elseif (($order == 'desc')) {
            $order = "{$prefix}member.t desc";
        } else {
            $order = "{$prefix}member.uid asc";
        }
        if ($keyword <> '') {
            if ($field == 'user') {
                $where[] = "{$prefix}member.user LIKE '%$keyword%'";
            }
            if ($field == 'phone') {
                $where[] = "{$prefix}member.phone LIKE '%$keyword%'";
            }
            if ($field == 'qq') {
                $where[] = "{$prefix}member.qq LIKE '%$keyword%'";
            }
            if ($field == 'email') {
                $where[] = "{$prefix}member.email LIKE '%$keyword%'";
            }
            if ($field == 'realname') {
                $where[] = "{$prefix}member.realname LIKE '%$keyword%'";
            }
            if ($field == 'uid') {
                $where[] = "{$prefix}member.uid in (".$keyword.")";
            }

        }
        if($close !=1){
            $where[] = "{$prefix}member.close =0";
        }

        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }

        $user = M('member');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $count = $user->field("{$prefix}member.*,{$prefix}auth_group.id as gid,{$prefix}auth_group.title,(select {$prefix}log.t from {$prefix}log where {$prefix}log.uid = {$prefix}member.uid order by {$prefix}log.id desc limit 1) as activitytime")
            ->order($order)
            ->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
            ->join("{$prefix}auth_group ON {$prefix}auth_group.id = {$prefix}auth_group_access.group_id")

            ->where($wherestring)
            ->count();

        $list = $user->field("{$prefix}member.*,{$prefix}auth_group.id as gid,{$prefix}auth_group.title,(select {$prefix}log.t from {$prefix}log where {$prefix}log.uid = {$prefix}member.uid order by {$prefix}log.id desc limit 1) as activitytime")
            ->order($order)
            ->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
            ->join("{$prefix}auth_group ON {$prefix}auth_group.id = {$prefix}auth_group_access.group_id")

            ->where($wherestring)
            ->limit($offset . ',' . $pagesize)
            ->select();


        //$user->getLastSql();
        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();


        $this->assign('list', $list);
        $this->assign('page', $page);
        $group = M('auth_group')->field('id,title')->select();
        $this->assign('group', $group);
        $this->display();
    }
    public function admin()
    {
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
        $close = isset($_GET['close']) ? $_GET['close'] : '0';
        $prefix = C('DB_PREFIX');
        $where = "{$prefix}member.admin = 1 ";
        if ($order == 'asc') {
            $order = "{$prefix}member.t asc";
        } elseif (($order == 'desc')) {
            $order = "{$prefix}member.t desc";
        } else {
            $order = "{$prefix}member.uid asc";
        }
        if ($keyword <> '') {
            if ($field == 'user') {
                $where .= " AND {$prefix}member.user LIKE '%$keyword%'";
            }
            if ($field == 'phone') {
                $where .= " AND {$prefix}member.phone LIKE '%$keyword%'";
            }
            if ($field == 'qq') {
                $where .= " AND {$prefix}member.qq LIKE '%$keyword%'";
            }
            if ($field == 'email') {
                $where .= " AND {$prefix}member.email LIKE '%$keyword%'";
            }
            if ($field == 'realname') {
                $where .= " AND {$prefix}member.realname LIKE '%$keyword%'";
            }
            if ($field == 'uid') {
                $where .= " AND {$prefix}member.uid in (".$keyword.")";
            }

        }
        if($close !=1){
            $where .= " AND {$prefix}member.close =0";
        }
        $groups = implode(',', I('request.groups'));
        if(!empty($groups)){
            $where .= " AND {$prefix}auth_group_access.group_id in (".$groups.")";
        }

        $user = M('member');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $count = $user->field("{$prefix}member.*,{$prefix}auth_group.id as gid,{$prefix}auth_group.title,(select {$prefix}log.t from {$prefix}log where {$prefix}log.uid = {$prefix}member.uid order by {$prefix}log.id desc limit 1) as activitytime")
            ->order($order)
            ->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
            ->join("{$prefix}auth_group ON {$prefix}auth_group.id = {$prefix}auth_group_access.group_id")
            ->where($where)
            ->count();

        $list = $user->field("{$prefix}member.*,{$prefix}auth_group.id as gid,{$prefix}auth_group.title,(select {$prefix}log.t from {$prefix}log where {$prefix}log.uid = {$prefix}member.uid order by {$prefix}log.id desc limit 1) as activitytime")
            ->order($order)
            ->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
            ->join("{$prefix}auth_group ON {$prefix}auth_group.id = {$prefix}auth_group_access.group_id")
            ->where($where)
            ->limit($offset . ',' . $pagesize)
            ->select();
        //echo $user->getLastSql();
        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);
        $groups = M('auth_group')->field('id,title')->where('type = 1 AND status = 1')->select();
        $this->assign('groups', $groups);
        $this->display('admin');
    }

    public function member()
    {

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $order = isset($_GET['order']) ? $_GET['order'] : '';
        $where = array();
        $prefix = C('DB_PREFIX');
        $where[] = "{$prefix}member.admin = 0 ";


        if($order == 'idcard'){
                $where[] = "{$prefix}member.idcard != ''";
        }

        if($order == 'unidcard'){
                $where[] = "{$prefix}member.idcard = ''";
        }

        if($order == 'approval'){
                $where[] = "{$prefix}member.approval = '1'";
        }

        if($order == 'unapproval'){
                $where[] = "{$prefix}member.approval = '0'";
        }
        if($order == 'close'){
                $where[] = "{$prefix}member.close = '1'";
        } else {
            $where[] = "{$prefix}member.close = 0";
        }

        if ($keyword <> '') {
            if ($field == 'user') {
                $where[] = "{$prefix}member.user LIKE '%$keyword%'";
            }
            if ($field == 'phone') {
                $where[] = "{$prefix}member.phone LIKE '%$keyword%'";
            }
            if ($field == 'qq') {
                $where[] = "{$prefix}member.qq LIKE '%$keyword%'";
            }
            if ($field == 'email') {
                $where[] = "{$prefix}member.email LIKE '%$keyword%'";
            }
            if ($field == 'realname') {
                $where[] = "{$prefix}member.realname LIKE '%$keyword%'";
            }
            if ($field == 'uid') {
                $where[] = "{$prefix}member.uid in (".$keyword.")";
            }
        }


        $groups = implode(',', I('request.groups'));
        if(!empty($groups)){
            $where[] = "{$prefix}auth_group_access.group_id in (".$groups.")";
        }

        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }
        $user = M('member');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $count = $user->field("distinct {$prefix}member.*,(select {$prefix}log.t from {$prefix}log where {$prefix}log.uid = {$prefix}member.uid order by {$prefix}log.id desc limit 1) as activitytime")
            // ->order($order)
            // ->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
            // ->join("{$prefix}auth_group ON {$prefix}auth_group.id = {$prefix}auth_group_access.group_id")
            ->where($wherestring)
            ->count();
        $list = $user->field("distinct {$prefix}member.*,(select {$prefix}log.t from {$prefix}log where {$prefix}log.uid = {$prefix}member.uid order by {$prefix}log.id desc limit 1) as activitytime")
            // ->order($order)
            // ->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
            // ->join("{$prefix}auth_group ON {$prefix}auth_group.id = {$prefix}auth_group_access.group_id",'left')
            ->where($wherestring)
            ->limit($offset . ',' . $pagesize)
            ->select();
        // echo $user->getLastSql();

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);
        $groups = M('auth_group')->field('id,title')->where('type = 2 AND status = 1')->select();
        $this->assign('groups', $groups);
        $this->display('member');
    }


    public function del()
    {

        $uids = isset($_REQUEST['uids']) ? $_REQUEST['uids'] : false;
        //uid为1的禁止删除
        if ($uids == 1 or !$uids) {
            $this->error('参数错误！');
        }
        if (is_array($uids)) {
            foreach ($uids as $k => $v) {
                if ($v == 1) {//初始管理员(uid为1)禁止删除
                    unset($uids[$k]);
                }
                $uids[$k] = intval($v);
            }
            if (!$uids) {
                $this->error('参数错误！');
                $uids = implode(',', $uids);
            }
        }

        $map['uid'] = array('in', $uids);
        if (M('member')->data(array('close' => '1'))->where($map)->save()) {
            // M('auth_group_access')->where($map)->delete();
            addlog('删除会员UID：' . $uids);
            $this->success('用户删除成功！');
        } else {
            $this->error('参数错误！');
        }
    }

    public function edit()
    {

        $uid = isset($_GET['uid']) ? intval($_GET['uid']) : false;
        if ($uid) {
            //$member = M('member')->where("uid='$uid'")->find();
            $prefix = C('DB_PREFIX');
            $user = M('member');
            $member = $user->field("{$prefix}member.*,{$prefix}auth_group_access.group_id")->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")->where("{$prefix}member.uid=$uid")->find();

        } else {
            $this->error('参数错误！');
        }

        $usergroup = M('auth_group')->field('id,title')->where('type=1')->select();
        $this->assign('usergroup', $usergroup);
        $this->assign('member', $member);
        $this->display('adminform');
    }

    public function add()
    {

        $usergroup = M('auth_group')->field('id,title')->where('type=1')->select();
        $this->assign('usergroup', $usergroup);
        $this->display('adminform');
    }


    public function update($ajax = '')
    {
        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : false;
        $user = isset($_POST['user']) ? htmlspecialchars($_POST['user'], ENT_QUOTES) : '';
        $group_id = isset($_POST['group_id']) ? intval($_POST['group_id']) : 0;
        if (!$group_id) {
            $this->error('请选择用户组！');
        }
        $password = isset($_POST['password']) ? trim($_POST['password']) : false;
        if ($password) {
            $data['password'] = password($password);
        }

        $data['sex'] = isset($_POST['sex']) ? intval($_POST['sex']) : 0;
        $data['realname'] = isset($_POST['realname']) ? trim($_POST['realname']) : '';
        $data['birthday'] = isset($_POST['birthday']) ? strtotime($_POST['birthday']) : 0;
        $data['phone'] = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $data['email'] = isset($_POST['email']) ? trim($_POST['email']) : '';
        $data['status'] = isset($_POST['status']) ? intval($_POST['status']) : 0;
        $data['admin'] = 1;

        $data['remark'] =  isset($_POST['remark']) ? trim($_POST['remark']) : '';


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
                'savePath' => 'upload/avatars/',
                'subName'  =>  '',
            ));
            $info = $upload->upload($_FILES);
            if($info) {
                $member = M('member')->where("uid='$uid'")->find();

                foreach ($info as $item) {

                    if($item['key'] == 'image'){
                        $imagefile = $upload->rootPath.$item['savepath'].$item['savename'];
                        $image = new Image();
                        $image->open($imagefile);
                        $image->thumb(300, 300)->save($upload->rootPath.$item['savepath'].'m_'.$item['savename']);
                        $image->thumb(100, 100)->save($upload->rootPath.$item['savepath'].'s_'.$item['savename']);
                        if($member['head']){
                            unlink($upload->rootPath.$item['savepath'].$member['head']);
                            unlink($upload->rootPath.$item['savepath'].'m_'.$member['head']);
                            unlink($upload->rootPath.$item['savepath'].'s_'.$member['head']);
                        }
                        $data['head'] = $item['savename'];
                    }
                }
            }
        }
        $data['t'] = time();
        
        if (!$uid) {
            if ($user == '') {
                $this->error('用户名称不能为空！');
            }
            if (!$password) {
                $this->error('用户密码不能为空！');
            }
            if (M('member')->where("user='$user'")->count()) {
                $this->error('用户名已被占用！');
            }
            $data['user'] = $user;
            $data['approval'] = 0;

            $uid = M('member')->data($data)->add();
            M('auth_group_access')->data(array('group_id' => $group_id, 'uid' => $uid))->add();
            addlog('新管理员，管理员UID：' . $uid);
        } else {
            M('auth_group_access')->data(array('group_id' => $group_id))->where("uid=$uid")->save();
            addlog('编辑管理员信息，管理员UID：' . $uid);
            M('member')->data($data)->where("uid=$uid")->save();

        }
        $this->success('操作成功！');
    }
    public function addmember()
    {
        $usergroups = M('auth_group')->field('id,title')->where("id!=1 AND type=2 AND status = 1")->select();
        $this->assign('usergroups', $usergroups);
        $this->display('memberform');
    }


    public function editmember()
    {

        $uid = isset($_GET['uid']) ? intval($_GET['uid']) : false;
        if ($uid) {
            //$member = M('member')->where("uid='$uid'")->find();
            $prefix = C('DB_PREFIX');
            $user = M('member');

            $member = M('member')->where('uid=' . $uid)->find();
            $member_groups = M('auth_group_access')->where('uid='. $uid)->getField('group_id',true);
            $member['groups'] = $member_groups;
           // $member = $user->field("{$prefix}member.*,{$prefix}auth_group_access.group_id")->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")->where("{$prefix}member.uid=$uid")->find();

        } else {
            $this->error('参数错误！');
        }
        // var_dump($member);
        $prefix = C('DB_PREFIX');

        $tag_map = M('member_tag_map')->field("{$prefix}member_tag_map.*,{$prefix}tags.title")
                    ->join("{$prefix}tags ON {$prefix}tags.id = {$prefix}member_tag_map.tagid")
                    ->where("{$prefix}member_tag_map.uid=" . $uid . " AND {$prefix}member_tag_map.type = ''")
                    ->select();

        $usergroups = M('auth_group')->field('id,title')->where("id!=1 AND type=2 AND status = 1")->select();
        $this->assign('tag_map', $tag_map);
        $this->assign('usergroups', $usergroups);
        $this->assign('member', $member);
        $this->display('memberform');
    }

    public function updatemember($ajax = '')
    {
        if ($ajax == 'yes') {
            $uid = I('get.uid', 0, 'intval');
            $gid = I('get.gid', 0, 'intval');
            M('auth_group_access')->data(array('group_id' => $gid))->where("uid='$uid'")->save();
            die('1');
        }

        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : false;
        $user = isset($_POST['user']) ? htmlspecialchars($_POST['user'], ENT_QUOTES) : '';
        $group_ids = isset($_POST['group_id']) ? $_POST['group_id'] : 0;
        if (!$group_ids) {
            $this->error('请选择用户组！');
        }
        if(strlen($_POST['idcard'])>0){
            if(strlen($_POST['idcard']) != 18){
                $this->error('请输入正确的身份证！');
            }
        }
        $password = isset($_POST['password']) ? trim($_POST['password']) : false;
        if ($password) {
            $data['password'] = password($password);
        }
        $data['sex'] = isset($_POST['sex']) ? intval($_POST['sex']) : 0;
        $data['realname'] = isset($_POST['realname']) ? trim($_POST['realname']) : '';

        $data['birthday'] = isset($_POST['birthday']) ? strtotime($_POST['birthday']) : 0;
        $data['phone'] = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $data['email'] = isset($_POST['email']) ? trim($_POST['email']) : '';
        $data['status'] = isset($_POST['status']) ? intval($_POST['status']) : 0;
        $data['idcard'] = isset($_POST['idcard']) ? trim($_POST['idcard']) : '';
        $data['position'] = isset($_POST['position']) ? trim($_POST['position']) : '';
        $data['city'] = isset($_POST['position']) ? trim($_POST['position']) : '';
        $data['edu'] = isset($_POST['edu']) ? trim($_POST['edu']) : '';
        $data['income'] = isset($_POST['income']) ? trim($_POST['income']) : '';
        $data['fincome'] = isset($_POST['fincome']) ? trim($_POST['fincome']) : '';
        $data['interest'] = isset($_POST['interest']) ? trim($_POST['interest']) : '';
        $data['tags'] = isset($_POST['tags']) ? trim($_POST['tags']) : '';
        $data['age'] = isset($_POST['age']) ? trim($_POST['age']) : '';
        $data['qq'] = isset($_POST['qq']) ? trim($_POST['qq']) : '';
        $data['remark'] = isset($_POST['remark']) ? htmlentities($_POST['remark']) : '';

        $data['approval'] = 0;

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
                'savePath' => 'upload/avatars/',
                'subName'  =>  '',
            ));
            $info = $upload->upload($_FILES);
            if($info){
                $member = M('member')->where("uid=$uid")->find();
                foreach ($info as $item) {
                    if($item['key'] == 'image'){
                        $imagefile = $upload->rootPath.$item['savepath'].$item['savename'];
                        $image = new Image();
                        $image->open($imagefile);
                        $image->thumb(300, 300)->save($upload->rootPath.$item['savepath'].'m_'.$item['savename']);
                        $image->thumb(100, 100)->save($upload->rootPath.$item['savepath'].'s_'.$item['savename']);
                        if($member['head']){
                            unlink($upload->rootPath.$item['savepath'].$member['head']);
                            unlink($upload->rootPath.$item['savepath'].'m_'.$member['head']);
                            unlink($upload->rootPath.$item['savepath'].'s_'.$member['head']);
                        }
                        $data['head'] = $item['savename'];
                    }
                }
            }
        }
        $data['admin'] = 0;

        $data['t'] = time();
        if (!$uid) {
            if ($user == '') {
                $this->error('用户名称不能为空！');
            }
            if (!$password) {
                $this->error('用户密码不能为空！');
            }
            if (M('member')->where("user='$user'")->count()) {
                $this->error('用户名已被占用！');
            }
            $data['user'] = $user;
            $uid = M('member')->data($data)->add();
            foreach($group_ids AS $group_id){
                $groupdata[] = array('uid' => $uid,'group_id' => $group_id);
            }
            M('auth_group_access')->addAll($groupdata);

            // M('auth_group_access')->data(array('group_id' => $group_id, 'uid' => $uid))->add();
            addlog('新增会员，会员UID：' . $uid);
        } else {

            M('auth_group_access')->where("uid=$uid")->delete();
            foreach($group_ids AS $group_id){
                $groupdata[] = array('uid' => $uid,'group_id' => $group_id);
            }
            M('auth_group_access')->addAll($groupdata);
            addlog('编辑会员信息，会员UID：' . $uid);
            M('member')->data($data)->where("uid=$uid")->save();

        }
        $this->success('操作成功！');
    }

    public function status()
    {
        // $id = I('id');

        $ids = implode(',', I('post.uids'));
        $status = I('get.status');
        $approval = I('get.approval');

        if (!$ids) {
           $uid = I('uid');
            if (!$uid) {
                $this->error('参数错误！');
            }

            $member = M('member')->where('uid=' . $uid)->find();
            if (!$member) {
                $this->error('参数错误！');
            }

            if ($member['uid'] == 1) {
                $this->error('此用户不可停用！');
            }
            $status = $member['status'];
            $approval = $member['approval'];

            if(isset($_GET['status'])){
                if ($status == 1) {
                   $res = M('member')->data(array('status' => 0))->where('uid=' . $uid)->save();
                }
                if ($status != 1 ) {
                    $res = M('member')->data(array('status' => 1))->where('uid=' . $uid)->save();
                }
            }
            if(isset($_GET['approval'])){
                if ($approval == 1) {
                   $res = M('member')->data(array('approval' => 0))->where('uid=' . $uid)->save();
                }
                if ($approval != 1 ) {
                    $res = M('member')->data(array('approval' => 1))->where('uid=' . $uid)->save();
                }
            }


            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{
            if(isset($_GET['status'])){

                $res = M('member')->data(array('status' => $status))->where('uid in(' . $ids.')')->save();
            }
            if(isset($_GET['approval'])){

                $res = M('member')->data(array('approval' => $approval))->where('uid in(' . $ids.')')->save();
            }
            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }

        }
    }


    public function import(){
		$prefix = C('DB_PREFIX');
		$taggroups_import = M('taggroups_import')->select();
		foreach($taggroups_import as $i=>$item){
			$tags= M('tags_import')
            ->where("{$prefix}tags_import.groupid=".$item['id'])
            ->select();
			$taggroups_import[$i]['tags']= $tags;
			$taggroups_import[$i]['counttags'] = count($tags);
		}

		$this->assign('taggroups_import', $taggroups_import);

        $taggroups = M('taggroups')->field('id,title')->select();
        $this->assign('taggroups', $taggroups);

        $usergroups = M('auth_group')->field('id,title')->where("id!=1 AND type=2 AND status = 1")->select();
        $this->assign('usergroups', $usergroups);

        //代理人
        $where = " {$prefix}auth_group.id= 29";

        $agentmembers = M('member')->field("{$prefix}member.uid,{$prefix}member.user")
            ->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
            ->join("{$prefix}auth_group ON {$prefix}auth_group.id = {$prefix}auth_group_access.group_id")
            ->where($where)
            ->select();
        $this->assign('agentmembers', $agentmembers);

		$this->display('import');
    }
	public function impUser(){
        if (!empty($_FILES)) {
            $group_ids = isset($_POST['group_id']) ? $_POST['group_id'] : array(8);//默认调研对象组
            $agent_uid = isset($_POST['agent_uid']) ? intval($_POST['agent_uid']) : 0;//默认调研对象组
			$mimes = array();
			$exts = array(
				'xlsx',
				'xls'
			);
			$upload = new Upload(array(
				'mimes' => $mimes,
				'exts' => $exts,
				'rootPath' => 'Public/',
				'savePath' => 'upload/',
				'subName'  =>  '',
			));
			$info = $upload->upload($_FILES);
			if(!$info) {// 上传错误提示错误信息
				$error = $upload->getError();
				$this->error($error);
			}else{// 上传成功

				vendor("PHPExcel.PHPExcel");
				vendor("PHPExcel.PHPExcel.IOFactory");
                vendor("PHPExcel.PHPExcel.Shared");
                Vendor("PHPExcel.PHPExcel.Shared.Date");
				$file_types = explode(".",$info['import']['savename']);
				$file_type = $file_types [count($file_types) - 1];

				$file_name = $upload->rootPath.$info['import']['savepath'].$info['import']['savename'];


				if(strtolower ( $file_type )=='xls') {
					Vendor("PHPExcel.PHPExcel.Reader.Excel5");
					$objReader = \PHPExcel_IOFactory::createReader('Excel5');
				}elseif(strtolower ( $file_type )=='xlsx') {
					Vendor("PHPExcel.PHPExcel.Reader.Excel2007");
					$objReader = \PHPExcel_IOFactory::createReader('Excel2007');
				}

				$objPHPExcel = $objReader->load($file_name);
				$objWorksheet = $objPHPExcel->getActiveSheet();

				$highestRow = $objWorksheet->getHighestRow();
				$highestColumn = $objWorksheet->getHighestColumn();
				$highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

				for ($row = 1; $row <= $highestRow; $row++) {
					for ($col = 0; $col < $highestColumnIndex; $col++) {
						$excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
					}
				}

				$tagsgroups = array();
				$tags = array();
				foreach($excelData as $j=>$value){
					for ($col = 19; $col < $highestColumnIndex; $col++) {
						if($j == 1){
							//$tags[$value[$col]] = array();
							$arr = explode('.',$value[$col]);
							if(count($arr)>1){
								$tags[$col] = $arr[1];
							}else{
								$tags[$col] = $value[$col];
							}
						}else{
							if(!empty($value[$col])){
								$tagsgroups[$tags[$col]][] = $value[$col];
							}
						}
					}
				}
				foreach($tagsgroups as $key=>$tagsgroup){
					$title = $key;

					$data = array();
					$data['title'] = $title;
					$data['status'] = 1;
					$data['o'] =  '';
					$data['description'] = '';
					if (M('taggroups_import')->where("title='$title'")->count()) {
						$group = M('taggroups_import')->where("title='$title'")->find();
						$groupid = $group['id'];
					}else{
						$groupid = M('taggroups_import')->data($data)->add();
					}

					foreach($tagsgroup as $tag){

						$title = $tag;
						$data = array();
						$data['title'] = $tag;
						$data['groupid'] = $groupid;
						$data['status'] = 1;
						$data['o'] = '';
						if (M('tags_import')->where("title='$title' AND groupid='$groupid'")->count()) {
							$tag_import = M('tags_import')->where("title='$title' AND groupid='$groupid'")->find();
							$tag_id = $tag_import['id'];
						}else{
							$tag_id = M('tags_import')->data($data)->add();
						}
					}
				}

				$users = array();
                $error_user = '';
				foreach($excelData as $i=>$value){

					if($i < 2){
						continue;
					}
					$Excal['user'] = (string)$value[0];//用户名
					$Excal['realname'] = (string)$value[1];//真实姓名
					$Excal['city'] = (string)$value[2];//城市
					$Excal['sex'] = (string)$value[3]=='男'?1:((string)$value[3]=='女'?2:0);//性别
                    $Excal['birthday'] = $value[4];//出生日期
					$Excal['age'] = (int)$value[5];//年龄
                    $Excal['hunyin'] = (string)$value[6];//婚姻状况
                    $Excal['edu'] = (string)$value[7];//教育程度
					$Excal['phone'] = (string)$value[8];//手机
                    $Excal['email'] = (string)$value[9];//邮箱
                    $Excal['qq'] = (string)$value[10];//QQ号
                    $Excal['profession_rank'] = (string)$value[11];//职级
                    $Excal['fincome'] = (int)$value[12];//家庭月收入
                    $Excal['company'] = (string)$value[13];//公司
                    $Excal['position'] = (string)$value[14];//职位
                    $Excal['profession'] = (string)$value[15];//职业
                    $Excal['remark'] = (string)$value[16];
                    $Excal['alipay'] = (string)$value[17];//支付宝账号
                    $Excal['idcard'] = substr((string)$value[18],1,-1);//身份证号码


                    // $Excal['online_time'] = $value[20];//在线时长
                    // $Excal['visit_days'] = $value[21];//访问天数
                    // $Excal['integral'] = $value[22];//金钱 积分
                    // $Excal['update_date'] = $value[23];//更新日期
                    // $Excal['t'] = \PHPExcel_Shared_Date::ExcelToPHP($value[24]);//注册日期
                    // $Excal['last_visit'] = \PHPExcel_Shared_Date::ExcelToPHP($value[25]);//最后访问
                    // $Excal['last_post'] = \PHPExcel_Shared_Date::ExcelToPHP($value[26]);//最后发帖
                    // $Excal['posts'] = $value[27];//发帖数

					/*
					$Excal->product_origin = $value[12];//情况打分
					$Excal->cpercent = $value[15];//充值手机
					$Excal->show = $value[16];//充值银行
					$Excal->image = $value[17];//充值卡号
					$Excal->image_alias = $value[18];//充值分行
					*/
					$Excal['admin'] = 0;
					$Excal['head'] = '';

					$Excal['ip'] = '';
					$Excal['last_city'] = '';

					$Excal['income'] = '';
					$Excal['password'] = '';
					$Excal['last_login'] = '';
					$Excal['status'] = 0;

                    $Excal['audited'] = 1;
                    $Excal['agent_uid'] = $agent_uid;
                    $Excal['approval'] = 0;


					$member = M('member')->where("user='".$Excal['user']."'")->find();
					if($member){
                        $error_user .= $Excal['user'].'<br/>';
					}else{
						$Excal['uid']=0;
						//add
						$uid = M('member')->data($Excal)->add();

                        M('auth_group_access')->where("uid=$uid")->delete();
                        foreach($group_ids AS $group_id){
                            M('auth_group_access')->data(array('uid' => $uid,'group_id' => $group_id))->add();
                        }

    					for ($col = 19; $col < $highestColumnIndex; $col++) {
    						$title = $tags[$col];
    						$group = M('taggroups_import')->where("title='$title'")->find();
    						$groupid = $group['id'];
    						if($groupid){
    							$tag_title = $value[$col];
    							$tag_import = M('tags_import')->where("title='$tag_title' AND groupid='$groupid'")->find();
    							$tag_id = $tag_import['id'];
    							if($tag_id){
    								$member_tag_map = M('member_tag_map')->where("uid='".$uid."' AND tagid='$tag_id'")->find();
    								if($member_tag_map){
    								}else{
    									$data = array();
    									$data['uid'] = $uid;
    									$data['groupid'] = $groupid;
    									$data['tagid'] = $tag_id;
    									$data['type'] = '_import';

    									//add
    									M('member_tag_map')->data($data)->add();
    								}
    							}
    						}
    					}
                    }
				}
                if($error_user == ''){
                    unlink($file_name);
                    $this->success('导入成功！');
                }else{
                    $this->success('导入成功！<br/>未导入用户：<br/>'.$error_user,'',20);
                }
			}
        }else{
			$this->error("请选择上传的文件");
		}
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
    function expUser(){//导出Excel
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
        $close = isset($_GET['close']) ? $_GET['close'] : '0';
        $where = array();
        $prefix = C('DB_PREFIX');
        $where[] = "{$prefix}member.admin = 0 ";
        if ($order == 'asc') {
            $order = "{$prefix}member.uid asc";
        } elseif (($order == 'desc')) {
            $order = "{$prefix}member.uid desc";
        } else {
            $order = "{$prefix}member.uid asc";
        }
        if ($keyword <> '') {
            if ($field == 'user') {
                $where[] = "{$prefix}member.user LIKE '%$keyword%'";
            }
            if ($field == 'phone') {
                $where[] = "{$prefix}member.phone LIKE '%$keyword%'";
            }
            if ($field == 'qq') {
                $where[] = "{$prefix}member.qq LIKE '%$keyword%'";
            }
            if ($field == 'email') {
                $where[] = "{$prefix}member.email LIKE '%$keyword%'";
            }
            if ($field == 'realname') {
                $where[] = "{$prefix}member.realname LIKE '%$keyword%'";
            }
            if ($field == 'uid') {
                $where[] = "{$prefix}member.uid in (".$keyword.")";
            }
        }
        if($close != 1){
            $where[] = "{$prefix}member.close = 0";
        }

        $groups = implode(',', I('request.groups'));
        if(!empty($groups)){
            $where[] = "{$prefix}auth_group_access.group_id in (".$groups.")";
        }

        $wherestring = '';
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }
        $xlsData = M('member')->field("distinct {$prefix}member.*,(select {$prefix}log.t from {$prefix}log where {$prefix}log.uid = {$prefix}member.uid order by {$prefix}log.id desc limit 1) as activitytime")
            ->order($order)
            ->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
            ->join("{$prefix}auth_group ON {$prefix}auth_group.id = {$prefix}auth_group_access.group_id")
            ->where($wherestring)
            ->select();

        $xlsName  = "User";
        $xlsCell  = array(
            array('uid','UID'),
            array('user','用户名'),
            array('realname','真实姓名'),
            array('city','城市'),
            array('sex','性别'),
            array('birthday','出生日期'),
            array('age','年龄'),
            array('hunyin','婚姻状况'),
            array('edu','教育程度'),
            array('phone','手机'),
            array('email','邮箱'),
            array('qq','QQ号'),
            array('idcard','身份证号码'),
            array('profession_rank','职级'),
            array('fincome','家庭月收入'),
            array('company','公司'),
            array('position','职位'),
            array('profession','职业'),
            array('remark','备注'),
            array('alipay','支付宝账号'),
            array('online_time','在线时长'),
            array('visit_days','访问天数'),
            array('integral','积分'),
            array('update_date','更新日期'),
            array('t','注册日期'),
            array('last_visit','最后访问'),
            array('last_post','最后发帖'),
            array('posts','发帖数'),
            array('approval ','审核状态')
        );
        foreach ($xlsData as $k => $v)
        {

            //1、在线时长 2、访问天数 3、

            $xlsData[$k]['sex']=$v['sex']==1?'男':'女';
            $xlsData[$k]['t']=date('Y/m/d h:i:s',$v['t']);
            $xlsData[$k]['last_visit']=date('Y/m/d h:i:s',$v['last_visit']);
            $xlsData[$k]['last_post']=date('Y/m/d h:i:s',$v['last_post']);
            $xlsData[$k]['update_date']=date('Y/m/d h:i:s',$v['update_date']);


        }
        $this->exportExcel($xlsName,$xlsCell,$xlsData);

    }

	public function edittaggroups(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : false;
        if ($id) {
            $prefix = C('DB_PREFIX');
            $taggroups_import = M('taggroups_import')->field("{$prefix}taggroups_import.*")->where("{$prefix}taggroups_import.id=$id")->find();

        } else {
            $this->error('参数错误！');
        }
        $taggroups = M('taggroups')->field('id,title')->select();
        $this->assign('taggroups', $taggroups);

        $this->assign('taggroups_import', $taggroups_import);
        $this->display('taggroupsform');
	}
	public function updatetaggroups(){
		$ids = I('post.ids');
        $tities = I('post.tities');
        $taggroups_ids = I('post.taggroups_ids');
		for($i=0;$i<count($ids);$i++){
			$id = $ids[$i];
			$title = $tities[$i];
			$taggroups_id = $taggroups_ids[$i];
			if($taggroups_id == 0){
				$add = 1;
			}
			$this->updatetaggroup($id,$title,$taggroups_id,$add);
		}

		$this->success('标签合并或新建成功！', U('import'));

	}
	public function updatetaggroup($id,$title,$taggroups_id,$add){
		$old_groupid = $id;

		if($add == 1){
			$data = array();
			$data['title'] = $title;
			$data['status'] = 1;
			$data['o'] =  '';
			$data['description'] = '';
			$new_groupid = M('taggroups')->data($data)->add();

			$tags = M('tags_import')->where("groupid='$old_groupid'")->select();
			foreach($tags as $tag){
				$old_tagid = $tag['id'];
				$data = array();
				$data['title'] = $tag['title'];
				$data['groupid'] = $new_groupid;
				$data['status'] = $tag['status'];
				$data['o'] = $tag['o'];
				$new_tagid = M('tags')->data($data)->add();


				$map = array();
				$map['groupid'] = $new_groupid;
				$map['tagid'] = $new_tagid;
				$map['type'] = '';

				M('member_tag_map')->data($map)->where("type='_import' AND groupid='$old_groupid' AND tagid='$old_tagid'")->save();
			}
		}
		if($taggroups_id){
			$new_groupid = $taggroups_id;
			$tags = M('tags_import')->where("groupid='$old_groupid'")->select();
			foreach($tags as $tag){
				$old_tagid = $tag['id'];
				$tag_title =  $tag['title'];
				$data = array();
				$data['title'] = $tag['title'];
				$data['groupid'] = $new_groupid;
				$data['status'] = $tag['status'];
				$data['o'] = $tag['o'];

				$tag_find = M('tags')->where("title='$tag_title' AND groupid='$new_groupid'")->find();
				if($tag_find['id']){
					$new_tagid = intval($tag_find['id']);
				}else{
					$new_tagid = M('tags')->data($data)->add();
				}

				$map = array();
				$map['groupid'] = $new_groupid;
				$map['tagid'] = $new_tagid;
				$map['type'] = '';
				M('member_tag_map')->data($map)->where("type='_import' AND groupid='$old_groupid' AND tagid='$old_tagid'")->save();
			}
		}

		M('taggroups_import')->where("id='$old_groupid'")->delete();
		M('tags_import')->where("groupid='$old_groupid'")->delete();
	}

	public function celebrity()
    {

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
        $where = array();

        $prefix = C('DB_PREFIX');
        //时间判断
        // $where[] = "date({$prefix}member_celebrity.begin) <=DATE(NOW())";
        // $where[] = "date({$prefix}member_celebrity.end) >=DATE(NOW())";

        if ($order == 'asc') {
            $order = "{$prefix}member.t asc";
        } elseif (($order == 'desc')) {
            $order = "{$prefix}member.t desc";
        } else {
            $order = "{$prefix}member_celebrity.id asc";
        }
        if ($keyword <> '') {
            if ($field == 'realname') {

                $where[] = "{$prefix}member.realname LIKE '%$keyword%'";
            }
            if ($field == 'comment') {
                $where[] = "{$prefix}member_celebrity.comment LIKE '%$keyword%'";
            }

        }
        $groups = implode(',', I('post.groups'));
        if(!empty($groups)){
            $where[] = "{$prefix}auth_group_access.group_id in (".$groups.")";
        }
        if(count($where)){
            $wherestring = implode(' AND ',$where);
        }

        $user = M('member_celebrity');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $count = $user->field("{$prefix}member_celebrity.*,{$prefix}member.realname,{$prefix}member.t,")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member_celebrity.uid = {$prefix}member.uid")
            ->where($where)
            ->count();

        $list = $user->field("{$prefix}member_celebrity.*,{$prefix}member.realname,{$prefix}member.t")
            ->order($order)
            ->join("{$prefix}member ON {$prefix}member_celebrity.uid = {$prefix}member.uid")
            ->where($where)
            ->limit($offset . ',' . $pagesize)
            ->select();

        //$user->getLastSql();
        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);
        $group = M('auth_group')->field('id,title')->select();
        $this->assign('group', $group);
        $this->display('celebrity');
    }
    public function cstatus()
    {
        // $id = I('id');

        $ids = implode(',', I('post.ids'));
        $status = I('get.status');
        if (!$ids) {
           $id = I('id');
            if (!$id) {
                $this->error('参数错误！');
            }

            $member_celebrity = M('member_celebrity')->where('id=' . $id)->find();
            if (!$member_celebrity) {
                $this->error('参数错误！');
            }

            $status = $member_celebrity['status'];
            if ($status == 1) {
               $res = M('member_celebrity')->data(array('status' => 0))->where('id=' . $id)->save();
            }
            if ($status != 1 ) {
                $res = M('member_celebrity')->data(array('status' => 1))->where('id=' . $id)->save();
            }
            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{
            $res = M('member_celebrity')->data(array('status' => $status))->where('id in(' . $ids.')')->save();

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->success('更新状态成功！');
            }

        }
    }

    public function cdel()
    {

        $ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : false;
        //uid为1的禁止删除
        if ($ids == 1 or !$ids) {
            $this->error('参数错误！');
        }
        if (is_array($ids)) {
            foreach ($ids as $k => $v) {
                if ($v == 1) {//初始管理员(uid为1)禁止删除
                    unset($ids[$k]);
                }
                $ids[$k] = intval($v);
            }
            if (!$ids) {
                $this->error('参数错误！');
                $ids = implode(',', $ids);
            }
        }

        $map['id'] = array('in', $ids);

        if (M('member_celebrity')->where($map)->delete()) {
            addlog('删除社会之星ID：' . $ids);
            $this->success('社会之星删除成功！');
        } else {
            $this->error('参数错误！');
        }
    }
    public function addcelebrity()
    {
        $prefix = C('DB_PREFIX');

        $usergroup = M('member')->field('uid,realname')->where("{$prefix}member.admin=0")->select();
        $this->assign('usergroup', $usergroup);
        $this->display('celebrityform');
    }


    public function editcelebrity()
    {

        $id = isset($_GET['id']) ? intval($_GET['id']) : false;
        $celebrity = '';
        if ($id) {
            //$member = M('member')->where("uid='$uid'")->find();
            $prefix = C('DB_PREFIX');
            $user = M('member_celebrity');
            $celebrity = $user->field("{$prefix}member_celebrity.*")
                            ->where("{$prefix}member_celebrity.id=$id")
                            ->find();

        } else {
            $this->error('参数错误！');
        }



        // var_dump($member);

        $usergroup = M('member')->field('uid,realname')->where("{$prefix}member.admin=0")->select();
        $this->assign('usergroup', $usergroup);

        $this->assign('celebrity', $celebrity);
        $this->display('celebrityform');
    }

    public function updatecelebrity()
    {
        $id = isset($_POST['id']) ? intval($_POST['id']) : false;

        $uid = isset($_POST['uid']) ? intval($_POST['uid']) : false;
        $begin = isset($_POST['begin']) ? trim($_POST['begin']) : false;
        $end = isset($_POST['end']) ? trim($_POST['end']) : false;
        $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
        if (!$uid) {
            $this->error('请选择用户！');
        }
        if (!$begin) {
            $this->error('请选择开始时间！');
        }
        $data['uid'] = isset($_POST['uid']) ? trim($_POST['uid']) : '';
        $data['begin'] = isset($_POST['begin']) ? trim($_POST['begin']) : false;
        $data['end'] = isset($_POST['end']) ? trim($_POST['end']) : false;
        $data['status'] = isset($_POST['status']) ? trim($_POST['status']) : '';
        $data['comment'] = isset($_POST['comment']) ? trim($_POST['comment']) : '';

        if(!$comment){
            $this->error('评语不能为空');
        }
        if (!$id) {
            $data['created'] = time();

            if (M('member_celebrity')->where("uid='$uid'")->count()) {
                $this->error('用户已被选为社区之星！');
            }
            $uid = M('member_celebrity')->data($data)->add();
            addlog('新增社区之星，会员UID：' . $uid);
        } else {

            if (M('member_celebrity')->where("uid='$uid' AND id!=$id")->count()) {
                $this->error('用户已被选为社区之星！');
            }
            M('member_celebrity')->data($data)->where("id=$id")->save();
            addlog('编辑社区之星，会员UID：' . $uid);
        }
        $this->success('操作成功！',U('celebrity'));
    }


    public function member_approve(){



        $prefix = C('DB_PREFIX');

        $users = M('member_approve')->field("{$prefix}member_approve.*,{$prefix}member.realname")
            ->join("{$prefix}member ON {$prefix}member_approve.uid = {$prefix}member.uid")
            ->select();
        $card0 = 'Public/themes/images/card-0.png';
        $card1 = 'Public/themes/images/card-1.png';

        $this->assign('card0', $card0);
        $this->assign('card1', $card1);
        $this->assign('users', $users);
        $this->display('approve');
    }
    public function approcal(){


        $ids = implode(',', I('post.ids'));
        $approcal = I('get.approcal');
        if (!$ids) {
           $id = I('id');
            if (!$id) {
                $this->error('参数错误！');
            }

            $member_approcal = M('member_approve')->where('id=' . $id)->find();
            if (!$member_approcal) {
                $this->error('参数错误！');
            }

            $approcal = $member_approcal['approcal'];
            if ($approcal == 1) {
               $res = M('member_approve')->data(array('approcal' => 0))->where('id=' . $id)->save();
            }
            if ($approcal != 1 ) {
                $res = M('member_approve')->data(array('approcal' => 1))->where('id=' . $id)->save();
            }
            if ($res) {
                $this->success('更新审核状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{
            $res = M('member_approve')->data(array('approcal' => $approcal))->where('id in(' . $ids.')')->save();

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->success('更新状态成功！');
            }

        }
    }


    public function register(){


        $prefix = C('DB_PREFIX');

        $register = M('member_register')->field("{$prefix}member_register.*")
                    ->find();

        $this->assign('register', $register);

        $this->display('register');
    }

    public function registerup(){
            $id = isset($_POST['id']) ? intval($_POST['id']) : false;
            $title = isset($_POST['title']) ? trim($_POST['title']) : false;
            $description = isset($_POST['description']) ? htmlentities($_POST['description']) : false;

            if(!$title && !$description){
                $this->error("标题和协议内容不得为空！");
            }
            $data['title'] = isset($_POST['title']) ? trim($_POST['title']) : '';
            $data['description'] = isset($_POST['description']) ? trim($_POST['description']) : '';
            if (!$id) {
                $data['date'] = time();
                $id = M('member_register')->data($data)->add();
                addlog('新增注册用户协议，协议ID：' . $id);
            } else {
                M('member_register')->data($data)->where("id=$id")->save();
                addlog('编辑注册用户协议，协议ID：' . $id);
            }
            $this->success("操作成功！",U("register"));
    }

}
