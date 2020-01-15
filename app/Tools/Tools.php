<?php
namespace App\Tools;
use Illuminate\Support\Facades\Cache;
class Tools {
    /**回复文本消息
     * [responseText description]
     * @return [type] [description]
     */
    public static function responseText($msg,$xmlObj){
        echo "<xml>
              <ToUserName><![CDATA[".$xmlObj->FromUserName."]]></ToUserName>
              <FromUserName><![CDATA[".$xmlObj->ToUserName."]]></FromUserName>
              <CreateTime>".time()."</CreateTime>
              <MsgType><![CDATA[text]]></MsgType>
              <Content><![CDATA[".$msg."]]></Content>
            </xml>";die;
    }
    public static function get_access_token()
    {
        //缓存里有数据 直接读数据
        // $access_token=Cache::get("access_token");
        if(empty($access_token)){
            //缓存里没有数据  调用接口读取  存入缓存
            $appid = "wx33757a7eb228d0c1";
            $appSecrer = "5bbd39dc9b88ced124e25a1a02175189";
            $url=file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appSecrer}");
            //发送请求
            $data=json_decode($url,1);
            $access_token=$data['access_token'];
            //存储2小时
            Cache::put("access_token",$access_token,7200);
        }  
        return $access_token;  
    }
    /**
     * curl Get方法请求 
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public static function curlGet($url){    
        //初始化： curl_init
        $ch = curl_init();
        //设置    curl_setopt
        curl_setopt($ch, CURLOPT_URL, $url);  //请求地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); //返回数据格式
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
        //执行  curl_exec
        $result = curl_exec($ch);
        //关闭（释放）  curl_close
        curl_close($ch);
        return $result;
}
    /**
     * [curl Post方法请求 ]
     * @param  [type] $url      [description]
     * @param  [type] $postData [description]
     * @return [type]           [description]
     */
    public static function curlPost($url,$postData){
        //初始化： curl_init
        $ch = curl_init();
        //设置    curl_setopt
        curl_setopt($ch, CURLOPT_URL, $url);  //请求地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); //返回数据格式
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        //访问https网站 关闭ssl验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
        //执行  curl_exec
        $result = curl_exec($ch);
        //关闭（释放）  curl_close
        curl_close($ch);
        return $result;
}
/**
     * 网页授权获取用户openid
     * @return [type] [description]
     */
    public static function getOpenid()
    {
        //先去session里取openid 
        $openid = session('openid');
        //var_dump($openid);die;
        if(!empty($openid)){
            return $openid;
        }
        //微信授权成功后 跳转咱们配置的地址 （回调地址）带一个code参数
        $code = request()->input('code');
        if(empty($code)){
            //没有授权 跳转到微信服务器进行授权
            $host = $_SERVER['HTTP_HOST'];  //域名
            $uri = $_SERVER['REQUEST_URI']; //路由参数
            $redirect_uri = urlencode("http://".$host.$uri);  // ?code=xx
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxacef5370fe99e7d5&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
            header("location:".$url);die;
        }else{
            //通过code换取网页授权access_token
            $url =  "https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxacef5370fe99e7d5&secret=c4182544d5889f29704ee2b0ebf5407e&code={$code}&grant_type=authorization_code";
            $data = file_get_contents($url);
            $data = json_decode($data,true);
            $openid = $data['openid'];
            //获取到openid之后  存储到session当中
            session(['openid'=>$openid]);
            return $openid;
            //如果是非静默授权 再通过openid  access_token获取用户信息
        }   
    }
    //公共函数
    //处理分类数据
    public static function getCateInfo($cateInfo,$parent_id=0,$level=0){
        static $info=[];//定义静态变量  只占一个空间
        foreach ($cateInfo as $k => $v){
            if($v['parent_id']==$parent_id){
                $v['level']=$level;
                $info[]=$v;
                self::getCateInfo($cateInfo,$v['cate_id'],$level+1);
                //自己调用自己
            }
        }
        return $info;
    }

    //微信二维码
    public static function createTmpQrcode($status)
    {
        $token = Self::get_access_token();
        //创建参数二维码接口
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$token}";
        //请求数据
        $postData = ["expire_seconds"=>60,//二维码有效期
             "action_name"=>"QR_SCENE",
              "action_info"=>[
                  "scene"=>[
                      "scene_str"=>$status
                  ]
               ]
            ];
        $postData = json_encode($postData);
        //发请求
        //拿数据 拿到票据ticket
        $data = Self::curlPost($url,$postData);
        $data = json_decode($data,true);
        if(isset($data['ticket'])){//获取成功
            //通过ticket换取二维码
            $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$data['ticket'];
            return $url;
        }
        return false;
    }
}
           
          