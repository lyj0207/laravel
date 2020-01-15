<?php
namespace App\Http\Middleware;
use App\Http\Controllers\test\LoginController;
use Closure;
use Session;
class Login
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    //中间介
    public function handle($request, Closure $next)
    {
        //防非法登录
        if(empty(session('all'))){
            echo '<script>alert("请先登录");location.href = "http://www.laravel.com/test/login"</script>';
        }

        //20分钟未操作,则提示重新登录
        // $time = date('Y-m-d H:i:s',time());
        // dd($time);
        // dd(LoginController::login_time());
        //当前时间大于登陆时间+20分钟 让它重新登录
        if(time()> LoginController::login_time() + 20){
            session()->forget('all');
            // echo '<script>alert("20分钟未操作,请重新登录");location.href = "http://www.laravel.com/test/login"</script>'; 
            return redirect("/test/login")->withErrors("20分钟未操作,请重新登录");
        }
        
        //一直操作则更新过期时间
        LoginController::update_time();
        
        return $next($request);
    }
}
