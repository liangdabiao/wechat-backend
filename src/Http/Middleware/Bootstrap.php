<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/9
 * Time: 12:41
 */

namespace iBrand\Wechat\Backend\Http\Middleware;

use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;

class Bootstrap
{
    public function handle(Request $request, \Closure $next)
    {

        Admin::css('/assets/wechat-backend/css/menu.css');
        Admin::css('/assets/wechat-backend/css/fans.css');
        Admin::css('/assets/wechat-backend/libs/element/index.css');
        Admin::css('/assets/wechat-backend/libs/ladda/ladda-themeless.min.css');
        Admin::css('/assets/wechat-backend/css/upload.css');
        Admin::css('/assets/wechat-backend/css/reply.css');


        Admin::js('/assets/wechat-backend/js/loading.js');
        Admin::js('/assets/wechat-backend/libs/Chart.min.js');
        Admin::js('/assets/wechat-backend/libs/element/vue.js');
        Admin::js('/assets/wechat-backend/libs/element/index.js');
        Admin::js('/assets/wechat-backend/libs/ladda/spin.min.js');
        Admin::js('/assets/wechat-backend/libs/ladda/ladda.min.js');
        Admin::js('/assets/wechat-backend/libs/ladda/ladda.jquery.min.js');
        Admin::js('/assets/wechat-backend/js/common.js');

        return $next($request);
    }

}