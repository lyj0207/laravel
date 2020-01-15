<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <center>
        <p>
            <a href="/test/login">返回登录页面</a>
        </p>
        <p>
            <img src="{{$img_url}}" alt="">
        </p>
    </center>
</body>
</html>
<script src="/js/jquery.js"></script>
<script type="text/javascript">
    //每隔几秒
    var t = setInterval("check();",2000);
    //二维码唯一标识
    var status = "{{$status}}";
    // console.log(status);
    function check(){
        //js轮询
        $.ajax({
            url:"/test/wechatlogin",
            dataType:"json",
            data:{status:status},//二维码唯一标识
            success:function(res){
                //返回提示
                if(res.ret == 1){
                    //关闭定时器
                    clearInterval(t);
                    //提示扫码登录成功
                    alert(res.msg);
                    location.href = "{{url('/test/logo')}}";
                }
            }
        });
    }
</script>