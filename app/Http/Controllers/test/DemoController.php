<?php

namespace App\Http\Controllers\test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use \App\Tools\Tools;

class DemoController extends Controller
{
    public function index(Request $request)
    {
        $echostr = $request->input('echostr');
        // echo $echostr;
        $info = file_get_contents("php://input");
        // dd($info);
        //处理xml格式数据，将xml格式数据转换成对象
        $xmlObj = simplexml_load_string($info,'SimpleXMLElement',LIBXML_NOCDATA);

        //判断用户是否关注
        if($xmlObj->MsgType == 'event' && $xmlObj->Event == 'subscribe')
        {
            //存储用户 二维码关系 用户openid  二维码唯一标识
            $openid = (string)$xmlObj->FromUserName;//用户openid
            // var_dump($openid);die;
            $EventKey = (string)$xmlObj->EventKey;//qrscene_123123
            // var_dump($EventKey);die;

            //得到二维码标识
            $status = ltrim($EventKey,'qrscene_');
            if($status){
                //用户扫码登录的程序流程
                Cache::put($status,$openid,20);
                //回复文本消息
                Tools::responseText("爸爸马上来,请稍等",$xmlObj);
            }
        }

        //用户关注过 触发SCAN事件
        if($xmlObj->MsgType == 'event' && $xmlObj->Event == 'SCAN')
        {
            //存储用户 二维码关系 用户openid  二维码唯一标识
            $openid = (string)$xmlObj->FromUserName;//用户openid
            // var_dump($openid);die;
            $status = (string)$xmlObj->EventKey;//qrscene_123123
            // var_dump($EventKey);die;
            if($status){
                //用户扫码登录的程序流程
                Cache::put($status,$openid,20);
                //回复文本消息
                Tools::responseText("爸爸马上来,请稍等",$xmlObj);
            }
        }
    }

    //扫码登录页面
    public function login(Request $request)
    {
        $echostr = $request->input('echostr');
        echo $echostr;
        //生成一个二维码
        //生成唯一标识
        $status = md5(uniqid());
        //生成二维码图片
        $img_url = Tools::createTmpQrcode($status);

        return view('test/logo',[
            'status'=>$status,
            'img_url'=>$img_url
        ]);
    }

    //检测手机端是否已经扫码
    public function wechatlogin(Request $request)
    {
        //二维码唯一标识
        $status = request('status');
        //缓存里有 登陆成功 
        $openid = Cache::get($status);
        if(!$openid){
            //抛错
            return json_encode(['msg'=>'爸爸没有来','ret'=>0]);
        }

        //判断用户是否是新用户 如果是新用户就创建账号 (做绑定手机号等业务处理)
        //登陆成功  存储session
        return json_encode(['msg'=>'爸爸已到达','ret'=>1]);
    }
}
