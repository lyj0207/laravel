<?php

namespace App\Http\Controllers\test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;

class TestController extends Controller
{
    //
    public function register(Request $requset)
    {
        return view('test/register');
    }

    public function do_register(Request $requset)
    {
        $info = $requset->all();
        $info = UserModel::create($info);
        // dd($info);
        if($info)
        {
            echo '<script>alert("注册成功"); location.href="/test/login"</script>';
        }else{
            echo '<script>alert("网络超时"); location.href="/test/register"</script>';
        }
    }
}
