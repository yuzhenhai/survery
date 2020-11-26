<?php
/**
 *
 * Copyright: Bodasoft
 * Author   ：dowei<douwei@bodait.com>
 * Date     ：2018-1-23
 * Version  ：1.0.0
 * Desc     ：用户组制器。
 *
 **/

namespace Admin\Controller;

class GroupController extends ComController
{
    public function index()
    {
        $group = M('auth_group')->select();
        $this->assign('list', $group);
        $this->assign('nav', array('user', 'grouplist', 'grouplist'));//导航
        $this->display();
    }
    public function admingroup()
    {

        $prefix = C('DB_PREFIX');
        $groups = M('auth_group')->where('type = 1')->select();
        foreach($groups AS $key => $group){
            $member = M()->table($prefix.'auth_group_access AS g')->join($prefix.'member AS m ON g.uid=m.uid')->where('g.group_id = "'.$group['id'].'"')->getField('user',true);
            $groups[$key]['count'] = count($member);
            $groups[$key]['member'] = implode(',',array_values($member));
        }
        $this->assign('list', $groups);
        $this->assign('nav', array('user', 'grouplist', 'grouplist'));//导航
        $this->display();
    }

    public function membergroup()
    {
        $group = M('auth_group')->where('type = 2')->select();
        $this->assign('list', $group);
        $this->assign('nav', array('user', 'grouplist', 'grouplist'));//导航
        $this->display('');
    }

    public function del()
    {
        $ids = I('post.ids');
        // $ids = isset($_POST['ids']) ? $_POST['ids'] : false;
        if (is_array($ids)) {
            foreach ($ids as $k => $v) {
                $ids[$k] = intval($v);
            }
            $ids = implode(',', $ids);
            $map['id'] = array('in', $ids);
            if (M('auth_group')->where($map)->delete()) {
                addlog('删除用户组ID：' . $ids);
                $this->success('用户组删除成功！');
            } else {
                $this->error('请选择一个组！');
            }
        } else {
            $this->error('请选择一个组！');
        }
    }

    public function updateadmin()
    {

        $data['title'] = isset($_POST['title']) ? trim($_POST['title']) : false;
        $id = isset($_POST['id']) ? intval($_POST['id']) : false;
        if ($data['title']) {
            $status = isset($_POST['status']) ? $_POST['status'] : '';
            if ($status == 'on') {
                $data['status'] = 1;
            } else {
                $data['status'] = 0;
            }
            //如果是超级管理员一直都是启用状态
            if ($id == 1) {
                $data['status'] = 1;
            }

            $data['remark'] = isset($_POST['remark']) ? trim($_POST['remark']) : '';
            $data['pid'] = isset($_POST['pid']) ? trim($_POST['pid']) : '';
            $data['type'] = 1;
            $rules = isset($_POST['rules']) ? $_POST['rules'] : 0;
            if (is_array($rules)) {
                foreach ($rules as $k => $v) {
                    $rules[$k] = intval($v);
                }
                $rules = implode(',', $rules);
            }
            $data['rules'] = $rules;
            if ($id) {
                $group = M('auth_group')->where('id=' . $id)->data($data)->save();
                if ($group) {
                    addlog('编辑用户组，ID：' . $id . '，组名：' . $data['title']);
                    $this->success('用户组修改成功！');
                    exit(0);
                } else {
                    $this->success('未修改内容');
                }
            } else {
                M('auth_group')->data($data)->add();
                addlog('新增用户组，ID：' . $id . '，组名：' . $data['title']);
                $this->success('新增用户组成功！');
                exit(0);
            }
        } else {
            $this->success('用户组名称不能为空！');
        }
    }

    public function editadmin()
    {

        $id = isset($_GET['id']) ? intval($_GET['id']) : false;
        if (!$id) {
            $this->error('参数错误！');
        }

        $group = M('auth_group')->where('id=' . $id)->find();
        if (!$group) {
            $this->error('参数错误！');
        }
        //获取所有启用的规则
        $rule = M('auth_rule')->field('id,pid,title')->where('status=1')->order('o asc')->select();
        $group['rules'] = explode(',', $group['rules']);

        $p_group = M('auth_group')->field('id,title')->where('type=1 AND pid=0')->select();
        $this->assign('p_group', $p_group);
        $rule = $this->getMenu($rule);
        $this->assign('rule', $rule);
        $this->assign('group', $group);
        $this->assign('nav', array('user', 'grouplist', 'addgroup'));//导航
        $this->display('adminform');
    }

    public function addadmin()
    {

        //获取所有启用的规则

        $p_group = M('auth_group')->field('id,title')->where('type=1 AND pid=0')->select();
        $this->assign('p_group', $p_group);
        $rule = M('auth_rule')->field('id,pid,title')->where('status=1')->order('o asc')->select();
        $rule = $this->getMenu($rule);
        $this->assign('rule', $rule);
        $this->display('adminform');
    }

    public function updatemember()
    {

        $data['title'] = isset($_POST['title']) ? trim($_POST['title']) : false;
        $id = isset($_POST['id']) ? intval($_POST['id']) : false;
        if ($data['title']) {
            $status = isset($_POST['status']) ? $_POST['status'] : '';
            if ($status == 'on') {
                $data['status'] = 1;
            } else {
                $data['status'] = 0;
            }
            //如果是超级管理员一直都是启用状态
            if ($id == 1) {
                $data['status'] = 1;
            }

            $data['remark'] = isset($_POST['remark']) ? trim($_POST['remark']) : '';
            $data['pid'] = isset($_POST['pid']) ? trim($_POST['pid']) : '';
            $data['type'] = 2;
            $rules = isset($_POST['rules']) ? $_POST['rules'] : 0;
            if (is_array($rules)) {
                foreach ($rules as $k => $v) {
                    $rules[$k] = intval($v);
                }
                $rules = implode(',', $rules);
            }
            $data['rules'] = $rules;
            if ($id) {
                $group = M('auth_group')->where('id=' . $id)->data($data)->save();
                // if ($group) {
                    addlog('编辑用户组，ID：' . $id . '，组名：' . $data['title']);
                    $this->success('用户组修改成功！',U("membergroup"));
                    exit(0);
                // } else {
                //     $this->success('未修改内容');
                // }
            } else {
                M('auth_group')->data($data)->add();
                addlog('新增用户组，ID：' . $id . '，组名：' . $data['title']);
                $this->success('新增用户组成功！',U("membergroup"));
                exit(0);
            }
        } else {
            $this->success('用户组名称不能为空！');
        }
    }


    public function editmember()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : false;
        if (!$id) {
            $this->error('参数错误！');
        }

        $group = M('auth_group')->where('id=' . $id)->find();
        if (!$group) {
            $this->error('参数错误！');
        }
        //获取所有启用的规则
        $rule = M('auth_rule')->field('id,pid,title')->where('status=1')->order('o asc')->select();
        $group['rules'] = explode(',', $group['rules']);

        $p_group = M('auth_group')->field('id,title')->where('type=2')->select();
        $this->assign('p_group', $p_group);
        $rule = $this->getMenu($rule);
        $this->assign('rule', $rule);
        $this->assign('group', $group);
        $this->assign('nav', array('user', 'grouplist', 'addgroup'));//导航
        $this->display('memberform');
    }

    public function addmember()
    {

        //获取所有启用的规则

        $p_group = M('auth_group')->field('id,title')->where('type=2')->select();
        $this->assign('p_group', $p_group);
        $rule = M('auth_rule')->field('id,pid,title')->where('status=1')->order('o asc')->select();
        $rule = $this->getMenu($rule);
        $this->assign('rule', $rule);
        $this->display('memberform');
    }

    public function addmembers()
    {

        $id = isset($_GET['id']) ? intval($_GET['id']) : false;

        if (!$id) {
            $this->error('参数错误！');
        }

        $group = M('auth_group')->where('id=' . $id)->find();
        if (!$group) {
            $this->error('参数错误！');
        }
        $this->assign('group', $group);
        $this->display('addmembers');
    }


    public function importmembers()
    {
        $uidstr = isset($_POST['uid']) ? $_POST['uid'] : 0;
        $group_id = isset($_POST['group_id']) ? intval($_POST['group_id']) : 0;

        $uids = explode(',', $uidstr);

        if (!$group_id) {
            $this->error('参数错误！');
        }

        foreach($uids AS $uid){
            if(M('member')->where("uid = $uid and admin = 0")->select()){
                if(!M('auth_group_access')->where("uid = $uid AND group_id = $group_id")->select()){
                    $groupdata[] = array('uid' => $uid,'group_id' => $group_id);
                }
            }
        }
        if(!empty($groupdata)){
            M('auth_group_access')->addAll($groupdata);
        }
        $this->success('操作成功！');
    }

    public function delmembers()
    {

        $id = isset($_GET['id']) ? intval($_GET['id']) : false;

        if (!$id) {
            $this->error('参数错误！');
        }

        $group = M('auth_group')->where('id=' . $id)->find();
        if (!$group) {
            $this->error('参数错误！');
        }
        $this->assign('group', $group);
        $this->display('delmembers');
    }


    public function removemembers()
    {
        $uidstr = isset($_POST['uid']) ? $_POST['uid'] : 0;
        $group_id = isset($_POST['group_id']) ? intval($_POST['group_id']) : 0;

        $uids = explode(',', $uidstr);

        if (!$group_id) {
            $this->error('请选择用户组');
        }

        
        foreach($uids AS $uid){
            if(!empty($uid)){
                if(M('member')->where("uid = $uid and admin = 0")->select()){
                    if(M('auth_group_access')->where("uid = $uid AND group_id = $group_id")->select()){
                        $members[] = $uid;
                    }
                }
            }        

        }

        if(!empty($members)){
            $uidstr = implode(',', $members);
            $where = 'group_id = '.$group_id .' AND uid in ('.$uidstr .')';

            if(M('auth_group_access')->where($where)->delete()){
                $this->success('操作成功！');
            }else{
                $this->error('操作失败！');
            }
        }else{
            $this->success('操作成功！');
        }

    }


    public function status()
    {

       // $id = I('id');
        $ids = implode(',', I('post.ids'));


        if(empty($ids)){//更新一个
            $id = I('id');
            if (!$id) {
                $this->error('参数错误！');
            }

            $group = M('auth_group')->where('id=' . $id)->find();
            if (!$group) {
                $this->error('参数错误！');
            }

            if ($group['pid'] == 0) {
                $this->error('此用户组不可变更状态！');
            }
            $status = $group['status'];
            if ($status == 1) {
               $res = M('auth_group')->data(array('status' => 0))->where('id=' . $id)->save();
            }
            if ($status != 1 ) {
                $res = M('auth_group')->data(array('status' => 1))->where('id=' . $id)->save();
            }
            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
        }else{//批量更新
            $status = I('status');
            if (!$ids) {
                $this->error('参数错误！');
            }
            /*if ($id == 1) {
                $this->error('此用户组不可变更状态！');
            }*/
            $res = M('auth_group')->data(array('status' => $status))->where('id in(' . $ids.')')->save();

            if ($res) {
                $this->success('更新成功！');
            } else {
                $this->error('所选组已经是停用状态');
            }
        }
    }

    public function groupcopy() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $prefix = C('DB_PREFIX');

        $group = M('auth_group')->where("type = 2 AND id = " .$id)->find();

        $data = array();
        // $data = $group;
        $data['id'] = 0;
        $data['title'] = $group['title'].' (复制)';
        $data['pid'] = intval($group['pid']);
        $data['type'] = intval($group['type']);
        $data['status'] = intval($group['status']);
        $data['rules'] = $group['rules'];
        $data['remark'] = $group['remark'];


        $group_id = M('auth_group')->data($data)->add();

        addlog('复制用户组，ID：' . $id . '，组名：' . $data['title']);

        $this->success('任务复制成功',U('editmember',array('id'=>$group_id)));
    }
    public function tasks()
    {

        $auth_group_id = isset($_GET['group_id']) ? $_GET['group_id'] : '';

        $prefix = C('DB_PREFIX');

        $group = M('auth_group')->where("id = " .$auth_group_id)->find();
        $this->assign('group', $group);

        $list = M('task_research_group')->field("{$prefix}task_research_group.*,{$prefix}task.name,{$prefix}task.title,{$prefix}task.identifier")
            ->join("{$prefix}task ON {$prefix}task.task_id = {$prefix}task_research_group.task_id")
            ->order("{$prefix}task_research_group.o ASC")
            ->where("{$prefix}task_research_group.auth_group_id='$auth_group_id'")
            ->select();

        $sort = array();
        foreach($list as $i=>$item){
            $sort[] = $item['research_group_id'];
        }

        $this->assign('list', $list);
        $this->assign('sort', implode(",",$sort));

        $this->display('tasks');
    }
    public function sorttasks(){
        $order = $_POST['order'];
        $neworder = trim($_POST['neworder']);
        if (!empty($neworder)) {
            $sort_arr=explode(",",$neworder);
            $i = 1;
            foreach($sort_arr as $val){
                M('task_research_group')->data(array('o'=>$i))->where("research_group_id='{$val}'")->save();
                $i++;
            }
        }

    }
}