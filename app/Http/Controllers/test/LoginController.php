<?php

namespace App\Http\Controllers\test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;
use Session;
class LoginController extends Controller
{
    public function login()
    {
        return view('test/login');
    }

    public function do_login(Request $request){
        //获取登录数据
        $data=$request->all();
        // dd($data);

        $where[]=[
            'name','=',$data['name'],
        ];

        //单条查询
        $info=UserModel::where($where)->first();
        // dd($info);

        //锁定账号和账号时间
        if (time()-$info['error_time']<60) {

            $time=60-(time()-$info['error_time']);
            return json_encode(['find'=>'账号已锁定到'.date("Y-m-d H:i:s",$info['error_time']),'msg'=>3]);
        }
        if ($info) {
            if ($info['pwd'] == $data['pwd']) {
                //取出sessionid
                $sessionid = session::getid();
                // dd($sessionid);

                //存入session
                $request->session()->put('sessionid', $sessionid);
                //添加登陆时间
                $login_time=time();


                //添加数据库
                UserModel::where(['u_id'=>$info['u_id']])->update(['error_num'=>0,'error_time'=>0,'session_id'=>$sessionid,'login_time'=>$login_time]);

                //将所有值存入session中
                $request->session()->put('all', $info);
                // dd(session('all'));
                //提示登录成功
                return json_encode(['find'=>'登陆成功','msg'=>1]);
            }else{

                $error_num=$info['error_num'];
                
                //判断密码是否超过3次
                if ($error_num>=2) {
                    UserModel::where(['u_id'=>$info['u_id']])->update(['error_num'=>0,'error_time'=>time()]);
                    //提示密码错误,将锁定账号
                    return json_encode(['find'=>'密码错误,3次将锁定账号','msg'=>4]);
                }else{
                    
                    $error=$error_num+1;
                    $num=3-$error;
                    UserModel::where(['u_id'=>$info['u_id']])->update(['error_num'=>$error]);
                    return json_encode(['find'=>'密码错误,还有'.$num.'次机会','msg'=>5]);
                }
            }
            }else{

                return json_encode(['find'=>'账号未注册','msg'=>2]);
            }
    }

    //获取登陆时间
    public static function login_time()
    {
        //取出session所有值
        $all = session('all');
        // dd($all);
        //获取id
        $u_id = $all->u_id;
        // dd($u_id);
        //获取id中的时间
        $login_time = UserModel::where(['u_id'=>$u_id])->value('login_time');
        // dd($login_time);

        return $login_time;
    }

    //过期时间
    public static function update_time()
    {
        $all = session('all');
        // dd($all);
        $u_id = $all->u_id;
        // dd($u_id);
        $login_time = UserModel::where(['u_id'=>$u_id])->update(['login_time'=>time() + 20 ]);
        // dd($login_time);

    }

    //展示
    public function index(Request $request)
    {
        //取出所有值
        $all = UserModel::get();
        // dd($all);

        //循环取session值
        foreach($all as $v){
            // dd($v);
        }
        
        //防止多终端登录
        if($v['session_id'] != session('sessionid'))
        {
            echo '<script>alert("你的账号已在其他地方登录,请求下线");location.href = "http://www.laravel.com/test/login"</script>';

        }
        //展示试图
        return view('test/index',['all'=>$all]);
    }
}
