<?php
/**
 *
 * Copyright: Bodasoft
 * Author   ：dowei<douwei@bodait.com>
 * Date     ：2018-1-23
 * Version  ：1.0.0
 * Desc     ：自定义变量
 *
 **/

namespace Admin\Controller;

class TagController extends ComController
{

    public function tags()
    {
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';

        $prefix = C('DB_PREFIX');
        // $where = "{$prefix}taggroups.status = 1 ";
        // if ($order == 'asc') {
        //     $order = "{$prefix}member.t asc";
        // } elseif (($order == 'desc')) {
        //     $order = "{$prefix}member.t desc";
        // } else {
        //     $order = "{$prefix}member.uid asc";
        // }
        $order = "{$prefix}tags.o asc";
        $where = array();
        if ($keyword <> '') {
            if ($field == 'title') {
                $where[] = "{$prefix}tags.title LIKE '%$keyword%'";
            }
            if ($field == 'id') {
                $where[] = "{$prefix}tags.id = $keyword";
            }
        }
        if ($id <> '') {
			$where[] = " {$prefix}tags.groupid = $id";
        }
        if ($status <> '') {
            $where[] = " {$prefix}tags.status = $status";
        }
		$wherestring = '';
		if(count($where)){
			$wherestring = implode(' AND ',$where);
		}

        $tags = M('tags');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $count = $tags->field("{$prefix}tags.*,{$prefix}taggroups.title as name")
            ->order($order)
            ->join("{$prefix}taggroups ON {$prefix}taggroups.id = {$prefix}tags.groupid")

            ->where($wherestring)
            ->count();

        $list = $tags->field("{$prefix}tags.*,{$prefix}taggroups.title as name")
            ->order($order)
            ->join("{$prefix}taggroups ON {$prefix}taggroups.id = {$prefix}tags.groupid")
            ->where($wherestring)
            ->limit($offset . ',' . $pagesize)
            ->select();

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);

        $taggroups = M('taggroups')->field('id,title')->select();
        $this->assign('taggroups', $taggroups);

        $this->display('tags');
    }

   public function addtags()
    {
        $groupid = isset($_GET['groupid']) ? intval($_GET['groupid']) : 0;
        $this->assign('groupid', $groupid);

        $taggroups = M('taggroups')->field('id,title')->select();
        $this->assign('taggroups', $taggroups);

        $this->display('tagsform');
    }

    public function edittags()
    {

        $id = isset($_GET['id']) ? intval($_GET['id']) : false;



        if ($id) {
            $prefix = C('DB_PREFIX');
            $user = M('tags');
            $tags = $user->field("{$prefix}tags.*")->where("{$prefix}tags.id=$id")->find();

        } else {
            $this->error('参数错误！');
        }
        $taggroups = M('taggroups')->field('id,title')->select();
        $this->assign('taggroups', $taggroups);

        $this->assign('tags', $tags);
        $this->display('tagsform');
    }

    public function updatetags(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : false;
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $data['title'] = isset($_POST['title']) ? trim($_POST['title']) : '';
        $data['groupid'] = isset($_POST['groupid']) ? intval($_POST['groupid']) : 0;
        $data['status'] = isset($_POST['status']) ? intval($_POST['status']) : 0;
        $data['o'] = isset($_POST['o']) ? trim($_POST['o']) : '';

        if ($data['title'] == '') {
            $this->error('标题不能为空！');
        }

        if (!$id) {
            if (M('taggroups')->where("title='$title'")->count()) {
                $this->error('标题已被占用！');
            }
            $id = M('tags')->data($data)->add();

            addlog('编辑标签组信息，ID：' . $id);
        } else {
            addlog('编辑标签组信息，ID：' . $id);
            M('tags')->data($data)->where("id=$id")->save();

        }
        $this->success('操作成功！', U('tags',array('id'=>$data['groupid'])));
    }

    public function deltag(){
        $ids = implode(',', I('post.ids'));
        $id = I('get.id');
        if ($id <> '') {
            if (M('tags')->where("id='{$id}'")->delete()) {
                addlog('删除自定义变量，ID：' . $id);
                $this->success('删除成功！');
            } else {
                $this->error('参数错误！');
            }
        } else {
            if($ids <> ''){
                if (M('tags')->where('id in(' . $ids.')')->delete()) {
                    addlog('删除自定义变量，ID：' . $id);
                    $this->success('删除成功！');
                } else {
                    $this->error('参数错误！');
                }
            } else {
                $this->error('参数错误！');
            }
        }
    }

    public function add()
    {

        $this->display('taggroupsform');
    }

    public function edittaggroups()
    {

        $id = isset($_GET['id']) ? intval($_GET['id']) : false;
        if ($id) {
            $prefix = C('DB_PREFIX');
            $user = M('taggroups');
            $taggroups = $user->field("{$prefix}taggroups.*")->where("{$prefix}taggroups.id=$id")->find();

        } else {
            $this->error('参数错误！');
        }

        $this->assign('taggroups', $taggroups);
        $this->display('taggroupsform');
    }

    public function del()
    {

        $ids = implode(',', I('post.ids'));
        $id = I('get.id');
        if ($id <> '') {
            if (M('taggroups')->where("id='{$id}'")->delete()) {
                addlog('删除自定义变量，ID：' . $id);
                $this->success('删除成功！');
            } else {
                $this->error('参数错误！');
            }
        } else {
            if($ids <> ''){
                if (M('taggroups')->where('id in(' . $ids.')')->delete()) {
                    addlog('删除自定义变量，ID：' . $id);
                    $this->success('删除成功！');
                } else {
                    $this->error('参数错误！');
                }
            } else {
                $this->error('参数错误！');
            }
        }
    }

    public function update()
    {
        $id = isset($_POST['id']) ? intval($_POST['id']) : false;
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $data['title'] = isset($_POST['title']) ? trim($_POST['title']) : '';

        $data['status'] = isset($_POST['status']) ? intval($_POST['status']) : 0;
        $data['o'] = isset($_POST['o']) ? trim($_POST['o']) : '';
        $data['description'] = isset($_POST['description']) ? htmlentities($_POST['description']) : '';

        if ($data['title'] == '') {
            $this->error('标题不能为空！');
        }



        if (!$id) {
            if (M('taggroups')->where("title='$title'")->count()) {
                $this->error('标题已被占用！');
            }
            $id = M('taggroups')->data($data)->add();

            addlog('编辑标签信息，ID：' . $id);
        } else {
            addlog('编辑标签信息，ID：' . $id);
            M('taggroups')->data($data)->where("id=$id")->save();

        }
        $this->success('操作成功！', U('taggroups'));
    }

    public function taggroups(){

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';

        $prefix = C('DB_PREFIX');
        // $where = "{$prefix}taggroups.status = 1 ";
        // if ($order == 'asc') {
        //     $order = "{$prefix}member.t asc";
        // } elseif (($order == 'desc')) {
        //     $order = "{$prefix}member.t desc";
        // } else {
        //     $order = "{$prefix}member.uid asc";
        // }
        $order = "{$prefix}taggroups.o asc";
        $where = '';
        if ($keyword <> '') {
            if ($field == 'title') {
                $where = "{$prefix}taggroups.title LIKE '%$keyword%'";
            }
            if ($field == 'id') {
                if(is_numeric($keyword)){
                    $where = "{$prefix}taggroups.id = $keyword";
                } else {
                    $this->error('输入ID不正确！');
                }
            }
        }

        if ($status <> '') {
            $where[] = " {$prefix}taggroups.status = $status";
        }

        $taggroups = M('taggroups');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $count = $taggroups->field("{$prefix}taggroups.*")
            ->order($order)
            ->where($where)
            ->count();

        $list = $taggroups->field("{$prefix}taggroups.*")
            ->order($order)
            ->where($where)
            ->limit($offset . ',' . $pagesize)
            ->select();

		foreach($list as $i=>$item){
			$tags= M('tags')->field("{$prefix}tags.*")
            ->where("{$prefix}tags.groupid=".$item['id'])
            ->select();
			$list[$i]['tags']= $tags;
			$list[$i]['counttags'] = count($tags);
		}

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);


        $this->display('taggroups');
    }

    public function status(){
        $ids = implode(',', I('post.ids'));
        if (!$ids) {
            $id = isset($_GET['id']) ? intval($_GET['id']) : false;
            if (!$id) {
                $this->error('参数错误！');
            }

            $taggroup = M('taggroups')->where('id=' . $id)->find();
            if (!$taggroup) {
                $this->error('参数错误！');
            }

            $status = $taggroup['status'];
            if ($status == 1) {
               $res = M('taggroups')->data(array('status' => 0))->where('id=' . $id)->save();
            }
            if ($status != 1 ) {
                $res = M('taggroups')->data(array('status' => 1))->where('id=' . $id)->save();
            }

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }

        }else{
            $res = M('taggroups')->data(array('status' => 0))->where('id in(' . $ids.')')->save();

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->success('更新失败！');
            }

        }
    }
    public function tagstatus(){
        $ids = implode(',', I('post.ids'));
        if (!$ids) {
            $id = isset($_GET['id']) ? intval($_GET['id']) : false;
            if (!$id) {
                $this->error('参数错误！');
            }

            $tag = M('tags')->where('id=' . $id)->find();
            if (!$tag) {
                $this->error('参数错误！');
            }

            $status = $tag['status'];
            if ($status == 1) {
               $res = M('tags')->data(array('status' => 0))->where('id=' . $id)->save();
            }
            if ($status != 1 ) {
                $res = M('tags')->data(array('status' => 1))->where('id=' . $id)->save();
            }

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
            if (!$ids) {
                $this->error('参数错误！');
            }

        }else{
            $res = M('tags')->data(array('status' => 0))->where('id in(' . $ids.')')->save();

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->success('更新失败！');
            }

        }
    }

    public function tagspackage(){
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';

        $prefix = C('DB_PREFIX');
        // $where = "{$prefix}taggroups.status = 1 ";
        // if ($order == 'asc') {
        //     $order = "{$prefix}member.t asc";
        // } elseif (($order == 'desc')) {
        //     $order = "{$prefix}member.t desc";
        // } else {
        //     $order = "{$prefix}member.uid asc";
        // }

        $order = "{$prefix}tagspackage.o asc";
        $where = '';
        if ($keyword <> '') {
            if ($field == 'name') {
                $where = "{$prefix}tagspackage.name LIKE '%$keyword%'";
            }
            if ($field == 'id') {
                if(is_numeric($keyword)){
                    $where = "{$prefix}tagspackage.id = $keyword";
                } else {
                    $this->error('输入ID不正确！');
                }
            }
        }
        if ($status <> '') {
            $where[] = " {$prefix}tagspackage.status = $status";
        }

        $tagspackage = M('tagspackage');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $count = $tagspackage->field("{$prefix}tagspackage.*")
            ->order($order)
            ->where($where)
            ->count();

        $list = $tagspackage->field("{$prefix}tagspackage.*")
            ->order($order)
            ->where($where)
            ->limit($offset . ',' . $pagesize)
            ->select();


		foreach($list as $i=>$item){
			$tags= M('taggroups')->field("{$prefix}taggroups.*")
            ->join("{$prefix}tag_package_groups ON {$prefix}tag_package_groups.taggroups_id = {$prefix}taggroups.id")
            ->where("{$prefix}tag_package_groups.tagspackage_id=".$item['id'])
            ->select();
			$list[$i]['taggroups']= $tags;
			//$list[$i]['counttaggroups'] = count($tags);
		}

        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->display('tagspackage');
    }

    public function addpackage(){

        $prefix = C('DB_PREFIX');


        $list = array();
        $groups = M('taggroups');

        $taggroups = $groups->field("{$prefix}taggroups.id as taggroups_id,{$prefix}taggroups.title")
            ->select();
        $this->assign('taggroups', $taggroups);

        $this->assign('list', $list);

        $this->display('tagspackageform');
    }

    public function editpackage(){

        $prefix = C('DB_PREFIX');

        $id = isset($_GET['id']) ? intval($_GET['id']) : false;
        if ($id) {
            $user = M('tagspackage');
            $tagspackage = $user->field("{$prefix}tagspackage.*")->where("{$prefix}tagspackage.id=$id")->find();

        } else {
            $this->error('参数错误！');
        }
        $tag_package_groups = M('tag_package_groups');

        $list = $tag_package_groups->field("{$prefix}tag_package_groups.*")
            // ->join("{$prefix}taggroups ON {$prefix}taggroups.id = {$prefix}tag_package_groups.taggroups_id")
            ->where("{$prefix}tag_package_groups.tagspackage_id=$id")
            ->select();
        $groups = M('taggroups');


        $taggroups = $groups->field("{$prefix}taggroups.id as taggroups_id,{$prefix}taggroups.title")
            ->select();
        $this->assign('taggroups', $taggroups);
        $this->assign('list', $list);
        $this->assign('tagspackage', $tagspackage);
        $this->display('tagspackageform');
    }

    public function updatepackage()
    {
// var_dump($_POST['tag']);exit;
        $id = isset($_POST['id']) ? intval($_POST['id']) : false;
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $data['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';

        $data['status'] = isset($_POST['status']) ? intval($_POST['status']) : 0;
        $data['o'] = isset($_POST['o']) ? trim($_POST['o']) : '';
        $data['date_added'] = date('Y-m-d H:i:s',time());

        $tags = isset($_POST['tag']) ? $_POST['tag'] : '';;


        if ($data['name'] == '') {
            $this->error('标题不能为空！');
        }

        if (!$id) {
            if (M('tagspackage')->where("name='$name'")->count()) {
                $this->error('标题已被占用！');
            }
            $id = M('tagspackage')->data($data)->add();

            addlog('编辑标签组信息，ID：' . $id);
        } else {
            addlog('编辑标签组信息，ID：' . $id);
            M('tagspackage')->data($data)->where("id=$id")->save();
            M('tag_package_groups')->where("tagspackage_id=$id")->delete();
        }
        if(!empty($tags)){
            $prefix = C('DB_PREFIX');
            $package['tagspackage_id'] = $id;
            $package_groups = M('tag_package_groups');

            foreach ($tags as $value) {
                $package['taggroups_id'] = $value['taggroups_id'];
                $package['admin'] = $value['admin'];
                $package['research'] = $value['research'];
                $package['client'] = $value['client'];
                M('tag_package_groups')->data($package)->add();
                // $tagspackage = $package_groups->field("{$prefix}tag_package_groups.*")->where("{$prefix}tag_package_groups.tagspackage_id={$id} AND {$prefix}tag_package_groups.taggroups_id = $$value['taggroups_id']")->find();

                // if(empty($tagspackage)){
                //     M('tag_package_groups')->data($package)->add();
                // }
            }
        }

        $this->success('操作成功！', U('tagspackage'));
    }

    public function delpackage(){


        $ids = implode(',', I('post.ids'));
        $id = I('get.id');
        if ($id <> '') {
            if (M('tagspackage')->where("id='{$id}'")->delete()) {
                addlog('删除自定义变量，ID：' . $id);
                $this->success('删除成功！');
            } else {
                $this->error('参数错误！');
            }
        } else {
            if($ids <> ''){
                if (M('tagspackage')->where('id in(' . $ids.')')->delete()) {
                    addlog('删除自定义变量，ID：' . $id);
                    $this->success('删除成功！');
                } else {
                    $this->error('参数错误！');
                }
            } else {
                $this->error('参数错误！');
            }
        }

    }

    public function tagspackagestatus(){
        $ids = implode(',', I('post.ids'));
        if (!$ids) {
            $id = isset($_GET['id']) ? intval($_GET['id']) : false;
            if (!$id) {
                $this->error('参数错误！');
            }

            $tagspackage = M('tagspackage')->where('id=' . $id)->find();
            if (!$tagspackage) {
                $this->error('参数错误！');
            }

            $status = $tagspackage['status'];
            if ($status == 1) {
               $res = M('tagspackage')->data(array('status' => 0))->where('id=' . $id)->save();
            }
            if ($status != 1 ) {
                $res = M('tagspackage')->data(array('status' => 1))->where('id=' . $id)->save();
            }

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->error('更新失败！');
            }
            if (!$ids) {
                $this->error('参数错误！');
            }

        }else{
            $res = M('tagspackage')->data(array('status' => 0))->where('id in(' . $ids.')')->save();

            if ($res) {
                $this->success('更新状态成功！');
            } else {
                $this->success('参数错误！');
            }

        }
    }


    public function autocomplete($ajax=""){
        $data = array();
        $id = I('get.taggroups_id');
        $ajax = I('get.ajax');
        if (!empty($ajax) && !empty($id)) {
            if (isset($id)) {
                $taggroups_id = $id;
            } else {
                $taggroups_id = '';
            }


            $prefix = C('DB_PREFIX');

            $where = "{$prefix}taggroups.id = '$taggroups_id'";

            $user = M('taggroups');


            $lists = $user->field("{$prefix}taggroups.*")->where($where)->find();


            // $results = $this->model_customer_customer->getCustomers($title);
                $json = $lists['id'].','.$lists['title'];
            echo $json;exit();
        }


    }

}