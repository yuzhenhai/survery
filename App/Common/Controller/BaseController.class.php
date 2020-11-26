<?php
/**
 *
 * Copyright: Bodasoft
 * Author   ：dowei<douwei@bodait.com>
 * Date     ：2018-1-23
 * Version  ：1.0.0
 * Desc     ：管理后台模块公共控制器，用于储存公共数据。
 *
 **/

namespace Common\Controller;

use Think\Controller;

class BaseController extends Controller
{
    public function _initialize()
    {
        C(setting());
    }
}