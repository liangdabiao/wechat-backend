<?php

namespace iBrand\Wechat\Backend\Services;



/**
 * 二维码服务提供.
 */
class QRCodeService
{

    protected static $appUrl;

    protected static $code;

    public function __construct(

    )
    {
        self::$appUrl = settings('wechat_api_url');

        self::$code=config('wechat-error-code');
    }



    //创建永久二维码
    public function storeForever($data,$app_id=null){

        $return=[];

        $app_id=empty($app_id)?wechat_app_id():$app_id;

        $url = self::$appUrl . "api/qrcode/forever?appid=" . $app_id;

        $code=self::$code;

        $res=wechat_platform()->wxCurl($url,$data);


        if(isset($res->errcode)&&$res->errcode!==0){
            $return['errcode']=$res->errcode;
            $return['errmsg']=isset($code[$res->errcode])?$code[$res->errcode]:$res->errmsg;
        }

        if(isset($res->url) && !empty($res->url))
        {
            return $res;
        }

        return $return;

    }



    //创建临时二维码
    public function storeTemporary($data,$app_id=null){

        $return=[];

        $app_id=empty($app_id)?wechat_app_id():$app_id;

        $url = self::$appUrl . "api/qrcode/temporary?appid=" . $app_id;

        $code=self::$code;

        $res=wechat_platform()->wxCurl($url,$data);

        if(isset($res->errcode)&&$res->errcode!==0){
            $return['errcode']=$res->errcode;
            $return['errmsg']=isset($code[$res->errcode])?$code[$res->errcode]:$res->errmsg;
        }

        if(isset($res->url) && !empty($res->url))
        {
            return $res;
        }

        return $return;

    }






}
