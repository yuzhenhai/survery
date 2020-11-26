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
use Think\Upload;
use Think\Image;

use PHPExcel_IOFactory;
use PHPExcel;

class TalksController extends ComController
{
    public function index()
    {

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

		$prefix = C('DB_PREFIX');
        if ($order == 'asc') {
            $order = "{$prefix}talks_word.date asc";
        } elseif (($order == 'desc')) {
            $order = "{$prefix}talks_word.date desc";
        } else {
            $order = "{$prefix}talks_word.date asc";
        }
        $where = array();
        if ($keyword <> '') {
            if ($field == 'find') {
                $where[] = "{$prefix}talks_word.find LIKE '%$keyword%'";
            }
            if ($field == 'typename') {
                $where[] = "{$prefix}talks_word_type.typename = '$keyword'";
            }
            if ($field == 'replacement') {
                $where[] = "{$prefix}talks_word.replacement = '%$keyword%'";
            }

        }

		$wherestring = '';
		if(count($where)){
			$wherestring = implode(' AND ',$where);
		}

		$talks = M('talks_word');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $talks->field("{$prefix}talks_word.*,{$prefix}talks_word_type.typename,{$prefix}auth_group.title")
            ->order($order)
            ->join("{$prefix}auth_group ON {$prefix}talks_word.creator = {$prefix}auth_group.id",'left')
            ->join("{$prefix}talks_word_type ON {$prefix}talks_word.type = {$prefix}talks_word_type.id",'left')
            ->where($wherestring)
            ->count();

        $list = $talks->field("{$prefix}talks_word.*,{$prefix}talks_word_type.typename,{$prefix}auth_group.title")
            ->order($order)
            ->join("{$prefix}auth_group ON {$prefix}talks_word.creator = {$prefix}auth_group.id",'left')
            ->join("{$prefix}talks_word_type ON {$prefix}talks_word.type = {$prefix}talks_word_type.id",'left')
            ->where($wherestring)
            ->limit($offset . ',' . $pagesize)
            ->select();

		$page = new \Think\Page($count, $pagesize);
        $page = $page->show();

        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->display();
    }
    public function add()
    {
        $prefix = C('DB_PREFIX');

        $talks_type = M('talks_word_type');

        $list = $talks_type->field("{$prefix}talks_word_type.*")
            ->select();
        $usergroup = M('auth_group')->field('id,title')->select();
        $this->assign('usergroup', $usergroup);

        $this->assign('list', $list);
       
        $this->display('form');
    }

    public function del()
    {

        $ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : false;
        if (!$ids) {
            $this->error('请勾选删除任务！');
        }
        if (!is_array($ids)) {
			$this->error('参数错误！');
        }
        $usergroup = M('auth_group')->field('id,title')->select();
        $this->assign('usergroup', $usergroup);
        
        $map['id'] = array('in', implode(',', $ids));
        if (M('talks_word')->where($map)->delete()) {

            addlog('删除任务ID：' . implode(',', $ids));
            $this->success('任务删除成功！');
        } else {
            $this->error('参数错误！');
        }
    }

    public function edit($id = 0)
    {
		$prefix = C('DB_PREFIX');

        $id = intval($id);
        $talks_list = M('talks_word');
        $talks = $talks_list->where("id='$id'")->find();
        if (!$talks) {
            $this->error('参数错误！');
        }

        $talks_type = M('talks_word_type');
        $usergroup = M('auth_group')->field('id,title')->select();
        $this->assign('usergroup', $usergroup);
        $list = $talks_type->field("{$prefix}talks_word_type.*")
            ->select();
        $this->assign('list', $list);
        $this->assign('talk', $talks);
        $this->display('form');
    }

    public function update()
    {
        $id = isset($_POST['id']) ? intval($_POST['id']) : false;
        $data['id'] = I('post.id', '', 'intval');
		$data['find'] = I('post.find', '', 'strip_tags');
		$data['type'] = I('post.type', '', 'strip_tags');
        $data['replacement'] = I('post.replacement', '', 'strip_tags');
        $data['date'] = date('Y-m-d H:i:s');
        $action = I('post.action', '', 'strip_tags');
        if($action != 'REPLACE'){
            $data['replacement'] = '';
        } else {
            $data['replacement'] = I('post.replacement', '', 'intval');
        }
                    $data['creator'] = I('post.creator', '', 'intval');

        $find = I('post.find', '', 'strip_tags');

        if (empty($find)) {
            $this->error('请不良词语不能为空！');
        }

        if ($id) {
            M('talks_word')->data($data)->where("id='{$id}'")->save();
            addlog('编辑任务，ID：' . $id);
        } else {
            M('talks_word')->data($data)->add();
            addlog('新增任务，名称：' . $data['find']);
        }

        $this->success('操作成功！',U('index'));
    }

    public function talkstype(){

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';

        $prefix = C('DB_PREFIX');

        if ($keyword <> '') {
            if ($field == 'id') {
                $where = "{$prefix}talks_word_type.id = '$keyword'";
            }
            if ($field == 'typename') {
                $where = "{$prefix}talks_word_type.typename LIKE '%$keyword%'";
            }


        }

        $talks = M('talks_word_type');
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $talks->field("{$prefix}talks_word_type.*")
            ->where($where)
            ->count();

        $list = $talks->field("{$prefix}talks_word_type.*")
            ->where($where)
            ->limit($offset . ',' . $pagesize)
            ->select();



        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->display('talkstype');


    }
    public function addtype(){


        $this->display('talkstypeform');
    }

    public function edittype($id = 0)
    {
        $prefix = C('DB_PREFIX');

        $id = intval($id);
       

        $talks_type = M('talks_word_type');
        $list = $talks_type->where("id='$id'")->find();

        if (!$list) {
            $this->error('参数错误！');
        }

        $this->assign('list', $list);
        $this->display('talkstypeform');
    }

    public function updatetype()
    {
        $id = isset($_POST['id']) ? intval($_POST['id']) : false;
        $data['id'] = I('post.id', '', 'intval');
        $data['typename'] = I('post.typename', '', 'strip_tags');

        $typename = I('post.typename', '', 'strip_tags');

        if (empty($typename)) {
            $this->error('请分类名称不能为空！');
        }

        if ($id) {
            M('talks_word_type')->data($data)->where("id='{$id}'")->save();
            addlog('编辑任务，ID：' . $id);
        } else {
            M('talks_word_type')->data($data)->add();
            addlog('新增任务，名称：' . $data['typename']);
        }

        $this->success('操作成功！',U('talkstype'));
    }


        public function deltype()
    {

        $ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : false;
       
        if (!$ids) {
            $this->error('请勾选删除任务！');
        }
        if (!is_array($ids)) {
            $this->error('参数错误！');
        }

        $map['id'] = array('in', implode(',', $ids));
        if (M('talks_word_type')->where($map)->delete()) {

            addlog('删除任务ID：' . implode(',', $ids));
            $this->success('任务删除成功！');
        } else {
            $this->error('参数错误！');
        }
    }    

    public function setting()
    {

        $this->display('setting');
    }

    public function saveSetting()
    {
        $data = $_POST;

        $data['post_interval'] = isset($_POST['post_interval']) ? intval(strip_tags($_POST['post_interval']))  : 0;
        $data['post_talk_number'] = isset($_POST['post_talk_number']) ? intval(strip_tags($_POST['post_talk_number']))  : 0;

        
        $Model = M('setting');
        foreach ($data as $k => $v) {
            $setting = $Model->where("k='$k'")->find();
            $ret['k'] = $k;
            $ret['v'] = $v;
            $ret['type'] = '0';
            $ret['name'] = '';

            if(isset($setting)){
                $Model->data(array('v' => $v))->where("k='{$k}'")->save();
            }else{
                $Model->data($ret)->add();
            }
        }
        //清除旧的缓存数据
        $cache = \Think\Cache::getInstance();
        $cache->clear();
        addlog('修改交流圈配置。');
        $this->success('交流圈配置修改成功！');
    }



    public function Import(){

        if (!empty($_FILES)) {
            // $group_ids = isset($_POST['group_id']) ? $_POST['group_id'] : array(8);//默认调研对象组
            // $agent_uid = isset($_POST['agent_uid']) ? intval($_POST['agent_uid']) : 0;//默认调研对象组
            $mimes = array();
            $exts = array(
                'xlsx',
                'xls'
            );
            $upload = new Upload(array(
                'mimes' => $mimes,
                'exts' => $exts,
                'rootPath' => './Public/',
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
                $file_types = explode(".",$info['image']['savename']);
                $file_type = $file_types [count($file_types) - 1];

                $file_name = $upload->rootPath.$info['image']['savepath'].$info['image']['savename'];


                if(strtolower ( $file_type )=='xls') {
                    Vendor("PHPExcel.PHPExcel.Reader.Excel5");
                    $objReader = \PHPExcel_IOFactory::createReader('Excel5');
                }elseif(strtolower ( $file_type )=='xlsx') {
                    Vendor("PHPExcel.PHPExcel.Reader.Excel2007");
                    $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
                }
                

                $objPHPExcel = $objReader->load($file_name);
                // $objPHPExcel = new PHPExcel();
                // var_dump($objPHPExcel);exit;
                $objWorksheet = $objPHPExcel->getActiveSheet();

                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

                for ($row = 1; $row <= $highestRow; $row++) {
                    for ($col = 0; $col < $highestColumnIndex; $col++) {
                        $excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    }
                }

                $talks_word = array();
                // $error = '';
                foreach($excelData as $j=> $value){
                    // if($j==1){
                    //     if($value[0] != 'creator'){
                    //         $error = "不正确的导入数据"！
                    //     }
                    // }
                    if($j>1){
                        $talks_word[] = $value;
                    }
                }
                foreach($talks_word as $key=>$word){
                    $data = array();
                    $data['creator'] = (int)$word[0];//用户组
                    $data['type'] = (int)$word[1];//类型
                    $data['find'] =  (string)$word[2];//不良词语
                    $data['action'] = (string)$word[3];//过滤动作
                    $data['replacement'] = (string)$word[4];//替换词语
                    $data['date'] = date('Y-m-d H:i:s',time());//时间


                    $groups = M("auth_group")->where("id = $word[0]")->find();
                    $type = M("talks_word_type")->where("id = $word[1]")->find();
                    if(!$groups || !$type){
                        $error = $word;
                    }else{
                        $talkswordid = M('talks_word')->data($data)->add();
                    }

                    // var_dump($data);
                    // if (M('taggroups_import')->where("title='$title'")->count()) {
                        // $group = M('talks_word')->where("title='$title'")->find();
                    //     $groupid = $group['id'];
                    // }else{
                    // }
                }
                if($error){
                    $this->success('部分导入失败，请注意查看');
                }else{
                    $this->success('导入成功！');
                }

            }
        }else{
            $this->error("请选择上传的文件");
        }
    }
}