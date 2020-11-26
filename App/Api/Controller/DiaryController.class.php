<?php
namespace Api\Controller;
use Think\Exception;
use Think\Upload;
class DiaryController extends ComController
{
	public function diarys(){
		$this->filterLogin();
		//当前用户可以查看的所有日记任务
		$uid = $this->userId;
		$prefix = C('DB_PREFIX');


        if($this->user['admin'] == 1){
            $list = M('diary')->field("{$prefix}diary.*")
                ->where("{$prefix}diary.state>0 AND {$prefix}diary.state<5 ")
                ->order("{$prefix}diary.diary_id DESC")
                ->select();
        }else{
            $list = M('diary')->field("{$prefix}diary.*,{$prefix}member_diary.status as member_diary_status")
                ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.group_id in ({$prefix}diary.research_group)")
                ->join("{$prefix}member ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
                ->join("LEFT JOIN {$prefix}member_diary ON {$prefix}member_diary.uid = {$prefix}member.uid AND {$prefix}member_diary.diary_id = {$prefix}diary.diary_id")
                ->where("{$prefix}member.uid='$uid' AND {$prefix}diary.state>0 AND {$prefix}diary.state<5")
                ->order("cast(member_diary_status as SIGNED INTEGER) ASC")
                ->select();
        }
        foreach($list as $i=>$item){
            $list[$i]['lefttime'] = $this->timeLeft($item['end']);  // 比较日期差
            $list[$i]['image'] = C('URL').C('UPLOAD_IMAGES_PATH').$item['image'];
            $list[$i]['count_users_all'] = $this->getTotalUsers($item['diary_id']);//参与人数
            //$list[$i]['count_comments'] = 0;//评论
        }

		$this->sendSuccess($list);
	}

	public function diary(){
		$this->filterLogin();
		//根据ID返回任务的详细信息，任务的欢迎信息，当前用户是否开始了当前日记任务
		$uid = $this->userId;
		$prefix = C('DB_PREFIX');

        $diary_id = isset($_REQUEST['diary_id']) ? intval($_REQUEST['diary_id']) : 0;
        if($diary_id){
            if($this->user['admin'] == 1){
                $diary = $this->getdiaryInfo($diary_id,-1);
            }else{
                $diary = $this->getdiaryInfo($diary_id,$uid);
            }
            if($diary){
                $this->sendSuccess($diary);
            }else{
                return $this->sendError(3000,'参数错误');
            }
        }else{
            return $this->sendError(3000,'参数错误');
        }
	}

    public function start(){
    	$this->filterLogin();
    	//根据任务ID开始日记任务，在member_diary 增加一条记录
        $uid = $this->userId;
        $diary_id = isset($_REQUEST['diary_id']) ? intval($_REQUEST['diary_id']) : 0;

        $prefix = C('DB_PREFIX');
        if($diary_id){
            $diary = M('diary')->where("diary_id='$diary_id'")->find();
            if($diary){
                //是否有权限做任务
                $member =  M('member')->field("{$prefix}member.*")
                    ->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
                    ->where("{$prefix}member.uid='$uid' AND {$prefix}auth_group_access.group_id in (".$diary['research_group'].")")
                    ->find();

                if($member['uid']){
                    //是否已开启任务
                    $count_member_diary = M('member_diary')->field("{$prefix}member_diary.id")
                        ->where("{$prefix}member_diary.diary_id ='".$diary_id."' AND {$prefix}member_diary.uid='".$uid."'")
                        ->count();
                    if($count_member_diary){
                        //已开启
                    }else{

                        $member_diary_data['id'] = 0;
                        $member_diary_data['uid'] = $uid;
                        $member_diary_data['diary_id'] = (int)$diary_id;
                        $member_diary_data['diary_items'] = 0;
                        $member_diary_data['status'] = 1;
                        $member_diary_data['start_time'] = time();
                        $member_diary_data['created'] = date('Y-m-d H:i:s');

                        M('member_diary')->data($member_diary_data)->add();
                        addlog('任务开启，任务名称：' . $diary['name'],false,3);
                    }
                    return $this->sendSuccess('日记任务开启成功');

                }else{
                    return $this->sendError(3001,'您没有权限做该任务');
                }
            }else{
                return $this->sendError(3000,'参数错误');
            }
        }else{
            return $this->sendError(3000,'参数错误');
        }
    }
    public function diarysubmit(){
        $this->filterLogin();
        //做日記
        $uid = $this->userId;

        $diary_id = isset($_REQUEST['diary_id']) ? intval($_REQUEST['diary_id']) : 0;

        $prefix = C('DB_PREFIX');
        if($diary_id > 0){

            $diary = M('diary')->where("diary_id='$diary_id'")->find();
            if($diary){
                //是否有权限做任务
                $member =  M('member')->field("{$prefix}member.*")
                    ->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")
                    ->where("{$prefix}member.uid='$uid' AND {$prefix}auth_group_access.group_id in (".$diary['research_group'].")")
                    ->find();

                if($member['uid']){
                    //是否已开启任务
                    $count_member_diary = M('member_diary')->field("{$prefix}member_diary.id")
                        ->where("{$prefix}member_diary.diary_id ='".$diary_id."' AND {$prefix}member_diary.uid='".$uid."'")
                        ->count();
                    if($count_member_diary){
                        //已开启
                        $content = isset($_REQUEST['content']) ? $_REQUEST['content'] : '';
                        $latitude = isset($_REQUEST['latitude']) ? $_REQUEST['latitude'] : 0;
                        $longitude = isset($_REQUEST['longitude']) ? $_REQUEST['longitude'] : 0;
                        $qrcode = isset($_REQUEST['qrcode']) ? $_REQUEST['qrcode'] : '';
                        $hasFile = isset($_REQUEST['hasFile']) ? $_REQUEST['hasFile'] : 0;
                        $message = isset($_REQUEST['message']) ? $_REQUEST['message'] : '';

                        $diary_item_data['diary_item_id'] = 0;
                        $diary_item_data['diary_id'] = $diary_id;
                        $diary_item_data['uid'] = $uid;
                        $diary_item_data['like'] = 0;
                        $diary_item_data['content'] = $content;
                        $diary_item_data['latitude'] = $latitude;
                        $diary_item_data['longitude'] = $longitude;
                        $diary_item_data['qrcode'] = $qrcode;
                        $diary_item_data['hasFile'] = $hasFile;
                        $diary_item_data['message'] = $message;
                        $diary_item_data['status'] = 0;
                        $diary_item_data['created'] = date('Y-m-d H:i:s');
                        $diary_item_id = M('diary_items')->data($diary_item_data)->add();

                        $diary = M('diary')->where("diary_id='$diary_id'")->find();
                        addlog('日记任务提交，任务名称：' . $diary['name'],false,3);

                        M('member_diary')->where("diary_id='$diary_id' AND uid='$uid'")->setInc('diary_items',1);

                        return $this->sendSuccess('日记提交成功');
                    }else{
                        return $this->sendError(3004,'参数错误,任务还未开启');
                    }

                }else{
                    return $this->sendError(3001,'您没有权限做该任务');
                }
            }else{
                return $this->sendError(3000,'参数错误');
            }

        }else{
            return $this->sendError(3006,'提交错误');
        }
    }
    public function diaries(){
    	$this->filterLogin();
    	//根据id 返回日记任务中当前用户的所有日记
        $uid = $this->userId;
        $prefix = C('DB_PREFIX');

        $diary_id = isset($_REQUEST['diary_id']) ? $_REQUEST['diary_id'] : '';

        $order = "";
        $where = '';

        if($diary_id <> ''){
            if($this->user['admin'] == 1){
                $list = M('diary_items')->field("{$prefix}diary_items.*")
                    ->where("{$prefix}diary_items.diary_id='$diary_id' ")
                    ->order("{$prefix}diary_items.diary_item_id DESC")
                    ->select();
            }else{
                $list = M('diary_items')->field("{$prefix}diary_items.*")
                    ->where("{$prefix}diary_items.diary_id='$diary_id' AND {$prefix}diary_items.uid='$uid' ")
                    ->order("{$prefix}diary_items.diary_item_id DESC")
                    ->select();
                foreach ($list as $i => $item) {
                    $list[$i]['islike'] = $this->islike($item['diary_item_id'],$uid);
                }
            }
            $this->sendSuccess($list);
        }else{
            return $this->sendError(3000,'参数错误');
        }

    }

    public function addlike(){
        $this->filterLogin();

    	//仅管理员，对某条日记点赞
        $uid = $this->userId;
        $prefix = C('DB_PREFIX');
        if($this->user['admin'] == 1){
            $diary_item_id = isset($_REQUEST['diary_item_id']) ? $_REQUEST['diary_item_id'] : '';
            if($diary_item_id<>''){
                $diary_item = M('diary_items')->where("diary_item_id='$diary_item_id'")->find();
                if($diary_item['diary_id']){
                    $islike = $this->islike($diary_item_id,$uid);
                    if($islike){
                        $this->sendSuccess('已点赞');
                    }else{
                        $diary_id = intval($diary_item['diary_id']);
                        $like_id = $this->dolike($diary_id,$diary_item_id,$uid);
                        if($like_id){
                            $this->sendSuccess('点赞成功');
                        }else{
                            return $this->sendError(3000,'参数错误');
                        }
                    }
                }else{
                    return $this->sendError(3000,'参数错误');
                }

            }else{
                return $this->sendError(3000,'参数错误');
            }
        }else{
            return $this->sendError(3000,'没有权限');
        }
    }

    public function addcomment(){
        $this->filterLogin();
    	//管理员对某个日记发表追问
        $uid = $this->userId;
        if($this->user['admin'] == 1){
            $diary_item_id = isset($_REQUEST['diary_item_id']) ? $_REQUEST['diary_item_id'] : '';
            if($diary_item_id<>''){
                $diary_item = M('diary_items')->where("diary_item_id='$diary_item_id'")->find();
                if($diary_item['diary_id']){
                    $diary_id = intval($diary_item['diary_id']);
                    $comment = isset($_REQUEST['comment']) ? $_REQUEST['comment'] : '';
                    if (empty($comment)) {
                        return $this->sendError(3000,'评论不能为空');
                    }
                    $comment_id = $this->docomment($diary_id,$diary_item_id,$uid,$comment);
                    if($comment_id){
                        $this->sendSuccess('评论成功');
                    }else{
                        return $this->sendError(3000,'参数错误');
                    }
                }else{
                    return $this->sendError(3000,'参数错误');
                }

            }else{
                return $this->sendError(3000,'参数错误');
            }
        }else{
            return $this->sendError(3000,'没有权限');
        }
    }
    public function addremark(){
        $this->filterLogin();
        //管理员对某个日记发表追问
        $uid = $this->userId;
        if($this->user['admin'] == 1){
            $diary_item_id = isset($_REQUEST['diary_item_id']) ? $_REQUEST['diary_item_id'] : '';
            if($diary_item_id<>''){
                $diary_item = M('diary_items')->where("diary_item_id='$diary_item_id'")->find();
                if($diary_item['diary_id']){
                    $diary_id = intval($diary_item['diary_id']);
                    $remark = isset($_REQUEST['remark']) ? $_REQUEST['remark'] : '';
                    if (empty($remark)) {
                        return $this->sendError(3000,'追问不能为空');
                    }
                    $canremark = $this->canremark($diary_item_id,$uid);
                    if(!$canremark){
                        $this->sendError(3000,'有未回答追问');
                    }else{
                        $remark_id = $this->doremark($diary_id,$diary_item_id,$uid,$remark);
                        if($remark_id){
                            $this->sendSuccess('追问成功');
                        }else{
                            return $this->sendError(3000,'参数错误');
                        }
                    }
                }else{
                    return $this->sendError(3000,'参数错误');
                }

            }else{
                return $this->sendError(3000,'参数错误');
            }
        }else{
            return $this->sendError(3000,'没有权限');
        }
    }
    public function reply(){
        $this->filterLogin();
    	//用户回复管理员的追问
        $uid = $this->userId;
        $prefix = C('DB_PREFIX');

        $remark_id = isset($_REQUEST['remark_id']) ? $_REQUEST['remark_id'] : '';
        $reply = isset($_REQUEST['reply']) ? $_REQUEST['reply'] : '';
        if (empty($remark_id)) {
            return $this->sendError(3000,'参数错误');
        }
        if (empty($reply)) {
            return $this->sendError(3000,'参数错误,回答追问不能为空');
        }

        $remark_item = M('diary_items_remark')->field("{$prefix}diary_items_remark.*")
            ->where("{$prefix}diary_items_remark.remark_id='$remark_id'")
            ->find();

        if($remark_item['status']==1){
            $data['diary_id'] = $remark_item['diary_id'];
            $data['diary_item_id'] = $remark_item['diary_item_id'];
            $data['uid'] = $uid;
            $data['remark'] = $reply;
            $data['status'] = '2';
            $data['type'] = '2';
            $data['datetime'] = time();
            $reply_id = M('diary_items_remark')->data($data)->add();

            $reply_data['reply_id'] = $reply_id;
            $reply_data['ruid'] = $uid;
            $reply_data['reply'] = $reply;
            $reply_data['status'] = '2';
            $reply_data['reply_datetime'] = time();
            M('diary_items_remark')->data($reply_data)->where("remark_id='$remark_id'")->save();
            addlog('日记任务回复追问，ID：' . $remark_id);
            $this->sendSuccess('追问回复成功！');
        }else{
            $this->sendSuccess('追问已回复！');
        }


    }
    public function getdiaryInfo($diary_id,$uid){
        $prefix = C('DB_PREFIX');

        $diary = M('diary')->where("diary_id='$diary_id'")->find();
        if($diary){

            $diary['count_users_all'] = $this->getTotalUsers($diary_id);//参与人数

            $diary['leftday'] = $this->timeLeft($diary['end']);

            $diary['image'] = C('URL').C('UPLOAD_IMAGES_PATH').$diary['image'];


            if($uid == -1){
                $count_diary_items = 0;
            }else{
                $member_diary = M('member_diary')->where("diary_id='$diary_id' AND uid='$uid'")->find();
                $count_diary_items = (int)$member_diary['diary_items'];
            }
            $diary['count_diary_items'] = $count_diary_items; //已完成
        }

        return $diary;
    }
    public function timeLeft($end){
        static $chunks = array(array(86400,'天'),array(3600 ,'小时'),array(60,'分'));

        $diff = intval(strtotime($end)-time());
        if($diff){
            $count =0;
            $since = array();
            for($i=0;$i<count($chunks);$i++) {
                if($diff>=$chunks[$i][0]) {
                    $num   =  floor($diff/$chunks[$i][0]);
                    if($num > 0){
                        array_push($since,sprintf('%d'.$chunks[$i][1],$num));
                        $diff =  (int)($diff-$chunks[$i][0]*$num);
                    }
                    $count++;
                }
            }
            //return $since;
            return array_shift($since).array_shift($since);
        }else {
            return 0;
        }
    }
    public function islike($diary_item_id,$uid){
        $prefix = C('DB_PREFIX');

        $islike = M('diary_items_like')->field("{$prefix}diary_items_like.*")
            ->where("{$prefix}diary_items_like.diary_item_id='$diary_item_id' AND {$prefix}diary_items_like.uid='$uid'")
            ->count();

        return $islike;
    }
    public function getCountLike($diary_item_id){
        $prefix = C('DB_PREFIX');

        $total = M('diary_items_like')->field("{$prefix}diary_items_like.*")
            ->where("{$prefix}diary_items_like.diary_item_id='$diary_item_id'")
            ->count();

        return $total;
    }
    public function getTotalUsers($diary_id){
        $prefix = C('DB_PREFIX');
        $diary = M('diary')->where("diary_id='$diary_id'")->find();

        $count_users_all = 0;
        $research_group= explode(',',$diary['research_group']);
        if(count($research_group) == 1){
            $count_users_all = M('member')->field("{$prefix}member.uid")
                ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
                ->where("{$prefix}auth_group_access.group_id ='".$diary['research_group']."'")
                ->count();
        }
        if(count($research_group) > 1){
            $count_users_all = M('member')->field("{$prefix}member.uid")
                ->join("{$prefix}auth_group_access ON {$prefix}auth_group_access.uid = {$prefix}member.uid")
                ->where("{$prefix}auth_group_access.group_id in (".$diary['research_group'].")")
                ->count();
        }
        return $count_users_all;//参与人数
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
    public function docomment($diary_id,$diary_item_id,$uid,$comment){

        $data['diary_id'] = $diary_id;
        $data['diary_item_id'] = $diary_item_id;
        $data['uid'] = $uid;
        $data['comment'] = $comment;
        $data['status'] = '1';
        $data['datetime'] = time();

        $comment_id = M('diary_items_comment')->data($data)->add();
        addlog('添加评论，ID：' . $comment_id);
        return $comment_id;

    }

    public function canremark($diary_item_id,$uid){
        $prefix = C('DB_PREFIX');

        $remark_count = M('diary_items_remark')
            ->where("{$prefix}diary_items_remark.status = 1 AND {$prefix}diary_items_remark.diary_item_id='$diary_item_id' AND {$prefix}diary_items_remark.uid='$uid'")
            ->count();
        return $remark_count?false:true;
    }

    public function doremark($diary_id,$diary_item_id,$uid,$remark){
        $prefix = C('DB_PREFIX');

        $data['diary_id'] = $diary_id;
        $data['diary_item_id'] = $diary_item_id;
        $data['uid'] = $uid;
        $data['remark'] = $remark;
        $data['status'] = '1';
        $data['type'] = '1';
        $data['datetime'] = time();

        $remark_id = M('diary_items_remark')->data($data)->add();

        $diary_item = M('diary_items')->field("{$prefix}diary_items.*,{$prefix}diary.name as diary_name")
            ->join("{$prefix}diary ON {$prefix}diary.diary_id = {$prefix}diary_items.diary_id")
            ->where("{$prefix}diary_items.diary_item_id='$diary_item_id'")
            ->find();

        $message_data = array();
        $message_data['uid'] = $diary_item['uid'];
        $message_data['type'] = 2;
        $message_data['message'] = '您的日记任务《'.$diary_item['diary_name'].'》有新追问,请及时回复！';
        $message_data['status'] = 0;
        $message_data['created'] = time();
        M('member_message')->data($message_data)->add();

        addlog('添加追问，ID：' . $remark_id);
        return $remark_id;
    }
}