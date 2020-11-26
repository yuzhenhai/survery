<?php
/**
 *
 * Copyright: Bodasoft
 * Author   ：dowei<douwei@bodait.com>
 * Date     ：2018-1-23
 * Version  ：1.0.0
 * Desc     ：公用配置文件。
 *
 **/
return array(
    //网站配置信息
    'URL' => 'http://127.0.0.1/survey',
    'COOKIE_SALT' => 'su',         //设置cookie加密密钥
    //备份配置
    'DB_PATH_NAME' => 'db',        //备份目录名称,主要是为了创建备份目录
    'DB_PATH' => './db/',          //数据库备份路径必须以 / 结尾；
    'DB_PART' => '20971520',       //该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M
    'DB_LEVEL' => '9',             //压缩级别   1:普通   4:一般   9:最高
    //扩展配置文件
    'LOAD_EXT_CONFIG' => 'db',
    'UPLOAD_IMAGES_PATH' =>'/Public/upload/images/',
    'UPLOADFILE_PATH' =>'/Public/upload/uploadfile/',
    'UPLOAD_BULLETIN_PATH' =>'/Public/upload/bulletin/',
    'UPLOAD_ATTACHMENT_PATH' =>'/Public/upload/subject_attachment/',

    'AVATAR_DEFAULT_PATH' =>'/Public/themes/avatars/avatar2.png',
    'AVATAR_PATH' =>'/Public/upload/avatars/'
);
