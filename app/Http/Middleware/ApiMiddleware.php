<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Redis;
class ApiMiddleware
{
    public $key='1904api';
    public $iv='1904190419041904';

    public $app_mac = [
       '1904a' => '1904apwd',
       '1904b' => '1904bpwd'
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $data = $request->post('data');
        //解密数据
        $decrypt_data = $this->AesDecrypt($data);
        $check = $this->checksign($decrypt_data); 
        //验证客户端的签名
        if($check['code'] != 200){
           return response($check);
        }else{
            return $next($request);
        }
        
    }

//验证签名
    private function checksign($decrypt_data)
    {
        $cliSign = request()->post('sign');
        ksort($decrypt_data);
        //判断appid是否存在
        // dd($this->app_mac[$decrypt_data['app_id']]);
        if(isset($this->app_mac[$decrypt_data['app_id']])){
            $json = json_encode($decrypt_data) .'app_key='. $this->app_mac[$decrypt_data['app_id']];
            if($cliSign == md5($json)){
                if(Redis::sAdd('code_set',$decrypt_data['time'].$decrypt_data['rand'])){
                    return [
                        'code'=>200,
                        'msg'=>"成功",
                        'data'=>md5($json)
                    ];
                }else{
                    return [
                        'code'=>9999999,
                        'msg'=>"失败",
                        'data'=>[]
                    ];
                }
            }
            if($cliSign==md5($json)){
                return [
                    'code'=>200,
                    'msg'=>"成功",
                    'data'=>md5($json)
                ];
            }else{
                return [
                    'code'=>99,
                    'msg'=>"zzzzzzzzzzzz",
                    'data'=>""
                ];
            }
            // dd($json);
            
        }else{
            return [
                'code'=>999,
                'msg'=>"zz",
                'data'=>""
            ];
        }
        
    }

    

    //加密
    protected function AesEncrypt($data)
    {
        if(is_array($data))
        {
            $data = json_encode($data);
        }

        $encrypt = openssl_encrypt(
            //第一个是要加密的传递数值
            $data,
            //第二个是加密的方式 这个是对称加密
            'AES-256-CBC',
            //调用key
            $this->key,
            1,
            //调用iv
            $this->iv
        );
        // dd($encrypt);

        return base64_encode($encrypt);
    }
    //解密
    protected function AesDecrypt($encrypt)
    {
        $decrypt = openssl_decrypt(
            base64_decode($encrypt),
    		'AES-256-CBC',
    		$this->key,
    		1,
    		$this->iv
        );
        return json_decode($decrypt,1);
    }
/**
 * array $data  参数约束
 */
    public function CurlPost($api_url,array $data,$is_post = 1)
    {
        $ch = curl_init();
        if($is_post)
        {
            curl_setopt($ch,CURLOPT_POST , 1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,['data'=>$this->AesEncrypt($data)]);
        }else{
            $api_url = $api_url.'?'.http_build_query($data);
        }

        curl_setopt($ch,CURLOPT_URL,$api_url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;

    }
}
