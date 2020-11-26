<?php
/**
 *
 * Copyright: Bodasoft
 * Author   ：dowei<douwei@bodait.com>
 * Date     ：2018-1-23
 * Version  ：1.0.0
 * Desc     ：网站设置
 *
 **/

namespace Admin\Controller;

use Think\Upload;

class SettingController extends ComController
{
    public function setting()
    {
        $this->display();
    }

    public function update()
    {

        $data = $_POST;
        $data['website'] = isset($_POST['website']) ? strip_tags($_POST['website']) : '';
        $data['sitename'] = isset($_POST['sitename']) ? strip_tags($_POST['sitename']) : '';
        $data['title'] = isset($_POST['title']) ? strip_tags($_POST['title']) : '';
        $data['keywords'] = isset($_POST['keywords']) ? strip_tags($_POST['keywords']) : '';
        $data['description'] = isset($_POST['description']) ? strip_tags($_POST['description']) : '';
        $data['footer'] =  I('post.footer', '', 'htmlspecialchars');

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
            if($info) {
                foreach ($info as $item) {
                    if($item['key'] == 'logo'){
                        $logo = M('setting')->where("k='logo'")->getField('v');
                        if($logo){
                            unlink($upload->rootPath.$item['savepath'].$logo);
                        }
                        $data['logo'] = $item['savename'];
                    }
                    if($item['key'] == 'favicon'){
                        $favicon = M('setting')->where("k='favicon'")->getField('v');
                        if($favicon){
                            unlink($upload->rootPath.$item['savepath'].$favicon);
                        }
                        $data['favicon'] = $item['savename'];
                    }
                }

            }
        }
        foreach ($data as $k => $v) {
            if (M('setting')->where("k='{$k}'")->count()) {
                M('setting')->data(array('v' => $v))->where("k='{$k}'")->save();
            }else{
                $setting_data = array();
                $setting_data['v'] = $v;
                $setting_data['k'] = $k;
                $setting_data['type'] = 0;
                $setting_data['name'] = '';
                M('setting')->data($setting_data)->add();
            }
        }
        //清除旧的缓存数据
        $cache = \Think\Cache::getInstance();
        $cache->clear();
        addlog('修改交流圈');
        $this->success('网站配置成功！');
    }

    public function website()
    {

        $this->display();
    }
    public function update_website()
    {
		$data = $_POST;

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
				$error = $upload->getError();
				//$this->error($error);
			}else{// 上传成功

				foreach ($info as $item) {
                    if($item['key'] == 'bgimg'){
                        $bgimg = M('setting')->where("k='bgimg'")->getField('v');
                        if($bgimg){
                            unlink($upload->rootPath.$item['savepath'].$bgimg);
                        }
                        $data['bgimg'] = $item['savename'];
                    }
                    if($item['key'] == 'loginbg'){
                        $loginbg = M('setting')->where("k='loginbg'")->getField('v');
                        if($loginbg){
                            unlink($upload->rootPath.$item['savepath'].$loginbg);
                        }
                        $data['loginbg'] = $item['savename'];
                    }
				}
			}
        }
        $data['bgcolor'] = isset($_POST['bgcolor']) ? strip_tags($_POST['bgcolor']) : '';


        $data['menucolor'] = isset($_POST['menucolor']) ? strip_tags($_POST['menucolor']) : '';
        $data['menufont'] = isset($_POST['menufont']) ? strip_tags($_POST['menufont']) : '';
        $data['menuactivecolor'] = isset($_POST['menuactivecolor']) ? strip_tags($_POST['menuactivecolor']) : '';

        $data['titlecolor'] = isset($_POST['titlecolor']) ? strip_tags($_POST['titlecolor']) : '';
        $data['titlefont'] = isset($_POST['titlefont']) ? strip_tags($_POST['titlefont']) : '';

        $data['linkcolor'] = isset($_POST['linkcolor']) ? strip_tags($_POST['linkcolor']) : '';

        $data['color'] = isset($_POST['color']) ? strip_tags($_POST['color']) : '';
        $data['font'] = isset($_POST['font']) ? strip_tags($_POST['font']) : '';
        $data['fontsize'] = isset($_POST['fontsize']) ? strip_tags($_POST['fontsize']) : '';

        $data['taskfont'] = isset($_POST['taskfont']) ? strip_tags($_POST['taskfont']) : '';
        $data['taskfontsize'] = isset($_POST['taskfontsize']) ? strip_tags($_POST['taskfontsize']) : '';

        $data['taskstartcolor'] = isset($_POST['taskstartcolor']) ? strip_tags($_POST['taskstartcolor']) : '';
        $data['taskstartbgcolor'] = isset($_POST['taskstartbgcolor']) ? strip_tags($_POST['taskstartbgcolor']) : '';

        $data['taskprogresscolor'] = isset($_POST['taskprogresscolor']) ? strip_tags($_POST['taskprogresscolor']) : '';
        $data['taskprogressbgcolor'] = isset($_POST['taskprogressbgcolor']) ? strip_tags($_POST['taskprogressbgcolor']) : '';

        $data['taskcompletecolor'] = isset($_POST['taskcompletecolor']) ? strip_tags($_POST['taskcompletecolor']) : '';
        $data['taskcompletebgcolor'] = isset($_POST['taskcompletebgcolor']) ? strip_tags($_POST['taskcompletebgcolor']) : '';

        $data['tagcolor'] = isset($_POST['tagcolor']) ? strip_tags($_POST['tagcolor']) : '';
        $data['tagbordercolor'] = isset($_POST['tagbordercolor']) ? strip_tags($_POST['tagbordercolor']) : '';
        $data['tagbgcolor'] = isset($_POST['tagbgcolor']) ? strip_tags($_POST['tagbgcolor']) : '';
        $data['tagfont'] = isset($_POST['tagfont']) ? strip_tags($_POST['tagfont']) : '';
        $data['tagfontsize'] = isset($_POST['tagfontsize']) ? strip_tags($_POST['tagfontsize']) : '';

        $Model = M('setting');
        foreach ($data as $k => $v) {
            if (M('setting')->where("k='{$k}'")->count()) {
                $Model->data(array('v' => $v))->where("k='{$k}'")->save();
            }else{
                $setting_data = array();
                $setting_data['v'] = $v;
                $setting_data['k'] = $k;
                $setting_data['type'] = 0;
                $setting_data['name'] = '';
                M('setting')->data($setting_data)->add();
            }

        }
        //清除旧的缓存数据
        $cache = \Think\Cache::getInstance();
        $cache->clear();
        addlog('修改界面设置。');
        $this->success('界面设置成功！');
    }

    public function adminlog(){
        $model = new \Think\Model();
        $mysql = $model->query("select VERSION() as mysql");
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $t = time() - 3600 * 24 * 60;
        $log = M('log');
        $log->where("t < $t")->delete();//删除60天前的日志
        $pagesize = 25;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量
        $count = $log->count();
        $list = $log->order('id desc')->limit($offset . ',' . $pagesize)->select();
        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->assign('mysql', $mysql[0]['mysql']);
        $this->assign('nav', array('', '', ''));//导航
        $this->display('adminlog');
    }

    public function userlog(){
        $model = new \Think\Model();
        $mysql = $model->query("select VERSION() as mysql");

        $pa = isset($_GET['pa']) ? intval($_GET['pa']) : '1';
        $t = time() - 3600 * 24 * 60;
        $log = M('user_log');
        $log->where("t < $t")->delete();//删除60天前的日志
        $upagesize = 25;#每页数量
        $uoffset = $upagesize * ($pa - 1);//计算记录偏移量
        $count = $log->count();
        $ulist = $log->order('id desc')->limit($uoffset . ',' . $upagesize)->select();
        $upage = new \Think\Page($count, $upagesize);
        $upage = $upage->show();
        $this->assign('ulist', $ulist);
        $this->assign('upage', $upage);

        $this->assign('mysql', $mysql[0]['mysql']);
        $this->assign('nav', array('', '', ''));//导航
        $this->display('userlog');
    }


    public function statistics(){
        $prefix = C('DB_PREFIX');
        // $mon_th = isset($_GET['month']) ? intval($_GET['month']) : false;;
        // $da_y = isset($_GET['day']) ? intval($_GET['day']) : false;;
        
        $yesterday = isset($_GET['yesterday']) ? trim($_GET['yesterday']) : false;
        $past = isset($_GET['past']) ? trim($_GET['past']) : false;

        $begin = isset($_GET['begin']) ? trim($_GET['begin']) : false;
        $end = isset($_GET['end']) ? trim($_GET['end']) : false;
        $begintime = strtotime(date('Y-m-d',time()));
        $endtime = strtotime(date('Y-m-d',strtotime("+1 day")));
        $landscape = 24;
        if($yesterday){
            $begintime = strtotime(date('Y-m-d',strtotime("-1 day")));
            $endtime = strtotime(date('Y-m-d',time()));
        }

        if($past){
            $begintime = strtotime(date('Y-m-d',strtotime("-7 day")));
            $endtime = strtotime(date('Y-m-d',strtotime("-1 day")));
        }

        if($begin){
            $begintime = strtotime($begin);
        }

        if($end){
            $endtime = strtotime($end);
        }

        $heng = array();
        $hourlogin = array();
        $hourdatis = array();
        $houruser = array();
        $hourintegral = array();
        if(!$begin&&!$past){
   
            for($i=1;$i<=24;$i++){
                $hourlogin[$i] = 0;
                $hourdatis[$i] = 0;
                $houruser[$i] = 0;
                $hourintegral[$i] = 0;
                $heng[] = $i;
            }    
        } else {
            $ccc = $this->prDates($begintime,$endtime);
            foreach ($ccc as $value) {
                $key = date('m-d',$value);
                $hourlogin[$key] = 0;
                $hourdatis[$key] = 0;
                $houruser[$key] = 0;
                $hourintegral[$key] = 0; 
                $heng[] = $key;
            }  
            $landscape = $this->count_days($begintime,$endtime);

        }

        $list = M("user_log")->field("{$prefix}user_log.*")
                ->where("{$prefix}user_log.t>= $begintime AND {$prefix}user_log.t<= $endtime AND {$prefix}user_log.type<> '0'")
                ->select();
     
        foreach ($list as $key => $value) {
            // $t = date('m',$value['t']);
            // $y = date('Y',$value['t']);
            $d = date('m-d',$value['t']);
            $h = date('H',$value['t']);

            
            if($value['type'] == '1'){
                if($past||$begin){
                    $ccc = $this->prDates($begintime,$endtime);
                    foreach ($ccc as $value) {
                         $key = date('m-d',$value);
                        if($d == $key){
                            $hourlogin[$key] = $hourlogin[$key]+1;
     
                        }
                    }                     
                } else {
                    for($i=1;$i<=$landscape;$i++){
                        if($h == $i){
                            $hourlogin[$i] = $hourlogin[$i]+1;
                        }
                    }
                }
            }
           if($value['type'] == '2'){
                if($past||$begin){
                    $ccc = $this->prDates($begintime,$endtime);
                    foreach ($ccc as $value) {
                         $key = date('m-d',$value);
                        if($d == $key){
                            $houruser[$key] = $houruser[$key]+1;

                        }
                    }  

                } else {
                    for($i=1;$i<=$landscape;$i++){
                        if($h == $i){
                            $houruser[$i] = $houruser[$i]+1;
                        }
                    }
                }
            }
            if($value['type'] == '3'){
                if($past||$begin){
                    $ccc = $this->prDates($begintime,$endtime);
                    foreach ($ccc as $value) {
                         $key = date('m-d',$value);
                        if($d == $key){
                            $hourdatis[$key] = $hourdatis[$key]+1;

                        }
                    }  
                } else {
                    for($i=1;$i<=$landscape;$i++){
                        if($h == $i){
                            $hourdatis[$i] = $hourdatis[$i]+1;
                        }
                    }
                }
            }
            if($value['type'] == '4'){
                if($past||$begin){
                   $ccc = $this->prDates($begintime,$endtime);
                    foreach ($ccc as $value) {
                         $key = date('m-d',$value);
                        if($d == $key){
                            $hourintegral[$key] = $hourintegral[$key]+1;

                        }
                    } 
                } else {
                    for($i=1;$i<=$landscape;$i++){
                        if($h == $i){
                            $hourintegral[$i] = $hourintegral[$i]+1;
                        }
                    }
                }
            }
            
        }
        
        $this->assign('end', $end);
        $this->assign('begin', $begin);
        

        $this->assign('heng', $heng);
        // $this->assign('finish', $finish);

        // $this->assign('day', $day);
        // $this->assign('month', $month);

        // $this->assign('mon', $mon);
        // $this->assign('da', $da);

        $this->assign('hourlogin', $hourlogin);
        $this->assign('hourdatis', $hourdatis);
        $this->assign('houruser', $houruser);
        $this->assign('hourintegral', $hourintegral);

        $this->assign('landscape', $landscape);


        // $this->assign('dayslogin', $dayslogin);
        // $this->assign('daysdatis', $daysdatis);
        // $this->assign('daysuser', $daysuser);
        // $this->assign('daysintegral', $daysintegral);

        // $this->assign('days', $days);
        // $this->assign('integral', $integral);
        // $this->assign('eidtuser', $eidtuser);
        // $this->assign('userlog', $userlog);
        // $this->assign('datis', $datis);

        $this->display('statistics');
    }


    public function hours(){
        $prefix = C('DB_PREFIX');
        $yesterday = isset($_GET['yesterday']) ? trim($_GET['yesterday']) : false;
        $past = isset($_GET['past']) ? trim($_GET['past']) : false;

        $begin = isset($_GET['begin']) ? trim($_GET['begin']) : false;
        $end = isset($_GET['end']) ? trim($_GET['end']) : false;
        $begintime = strtotime(date('Y-m-d',time()));
        $endtime = strtotime(date('Y-m-d',strtotime("+1 day")));
        $landscape = 24;
        if($yesterday){
            $begintime = strtotime(date('Y-m-d',strtotime("-1 day")));
            $endtime = strtotime(date('Y-m-d',time()));
        }

        if($past){
            $begintime = strtotime(date('Y-m-d',strtotime("-7 day")));
            $endtime = strtotime(date('Y-m-d',strtotime("-1 day")));
        }

        if($begin){
            $begintime = strtotime($begin);
        }

        if($end){
            $endtime = strtotime($end);
        }




        $list = M("user_log")->field("{$prefix}user_log.*")
                ->where("{$prefix}user_log.t>= $begintime AND {$prefix}user_log.t<= $endtime AND {$prefix}user_log.type<> '0'")
                ->select();
        $dayslogin = array();
        $daysdatis = array();
        $daysuser = array();
        $daysintegral = array();
        $days = cal_days_in_month(CAL_GREGORIAN, date('m',time()), date('Y',time()));

        $heng = array();
   
        for($i=1;$i<=24;$i++){
            $dayslogin[$i] = 0;
            $daysdatis[$i] = 0;
            $daysuser[$i] = 0;
            $daysintegral[$i] = 0;
            $heng[] = $i;
        }    
        
        $year = date('Y',time());
        $day = date('d',time());
        $month = date('m',time());
        $hour = date('H',time());


        $ccc = $this->prDates($begintime,$endtime);

        foreach ($list as $value) {


            $d = date('m-d',$value['t']);
            $h = date('H',$value['t']);

                if($value['type'] == '1'){
                    foreach ($ccc as $value) {
                         $key = date('m-d',$value);
                        if($d == $key){
                            for($i=1;$i<=24;$i++){
                                if($h == $i){
                                    $dayslogin[$i] = $dayslogin[$i]+1;
                                }
                            }
                        }

                    }
                    // for($i=1;$i<=24;$i++){
                    //     if($h == $i){
                    //         $dayslogin[$i] = $dayslogin[$i]+1;
                    //     }
                    // }
                }

               if($value['type'] == '2'){         
                    foreach ($ccc as $value) {
                         $key = date('m-d',$value);
                        if($d == $key){
                            for($i=1;$i<=24;$i++){
                                if($h == $i){
                                    $daysuser[$i] = $daysuser[$i]+1;
                                }
                            }
                        }
                    }
                    // for($i=1;$i<=$days;$i++){
                    //     if($d == $i){
                    //         $daysuser[$i] = $daysuser[$i]+1;
                    //     }
                    // }
                }
                if($value['type'] == '3'){
                    foreach ($ccc as $value) {
                         $key = date('m-d',$value);
                        if($d == $key){
                            for($i=1;$i<=24;$i++){
                                if($h == $i){
                                    $daysdatis[$i] = $daysdatis[$i]+1;
                                }
                            }
                        }
                    }
                    // for($i=1;$i<=$days;$i++){
                    //     if($d == $i){
                    //         $daysdatis[$i] = $daysdatis[$i]+1;
                    //     }
                    // }
                }
                if($value['type'] == '4'){
                    foreach ($ccc as $value) {
                         $key = date('m-d',$value);
                        if($d == $key){
                            for($i=1;$i<=24;$i++){
                                if($h == $i){
                                    $daysintegral[$i] = $daysintegral[$i]+1;
                                }
                            }
                        }
                    }
                    // for($i=1;$i<=$days;$i++){
                    //     if($d == $i){
                    //         $daysintegral[$i] = $daysintegral[$i]+1;
                    //     }
                    // }
                }
        }

        $mon = array();
        for($i = 1; $i <= 12; $i++){
            $mon[$i] = $i;
        }
       $da = array();
        for($i = 1; $i <= $days; $i++){
            $da[$i] = $i;
        }
        $ho = array();
        for($i = 1; $i <= 24; $i++){
            $ho[$i] = $i;
        }
        // var_dump($dayslogin);
        $this->assign('days', $days);
        $this->assign('day', $day);
        $this->assign('month', $month);

        $this->assign('mon', $mon);
        $this->assign('da', $da);
        $this->assign('ho', $ho);

        $this->assign('dayslogin', $dayslogin);
        $this->assign('daysdatis', $daysdatis);
        $this->assign('daysuser', $daysuser);
        $this->assign('daysintegral', $daysintegral);

        $this->display('hours');
    }

    public function count_days($a,$b){
        $a_dt = getdate($a);
        $b_dt = getdate($b);
        $a_new = mktime(12, 0, 0, $a_dt['mon'], $a_dt['mday'], $a_dt['year']);
        $b_new = mktime(12, 0, 0, $b_dt['mon'], $b_dt['mday'], $b_dt['year']);
        return round(abs($a_new-$b_new)/86400);
    }
    public function prDates($start,$end){
        // $dt_start = strtotime($start);
        // $dt_end = strtotime($end);
        $dt_array = array();
        while ($start<=$end){
             $dt_array[] = $start;//date('Y-m-d',$dt_start)."\n";
             $start = strtotime('+1 day',$start);
        }
        // var_dump(expression)
        return $dt_array;
    }   

}