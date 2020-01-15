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
            <p style='color:red'>
                @if(!empty($errors->first()))
                    {{$errors->first()}}
                @endif
            </p>
            <a href="/test/register">返回注册</a>
            <a href="/test/logo">微信扫码登录</a>
            <form>
            @csrf
                <table>
                    姓名：  <input type="text" name="name"><br><br>
                    密码：  <input type="password" name="pwd" id=""><br><br>
                    <input type="button" class="btn" value="submit">
                </table>
            </form>
        </center>
</body>
</html>
<script src="/js/jquery.js"></script>
<script>
    $(document).on('click','.btn',function(){
        var data = {};
        var name = $("input[name='name']").val();
        var pwd = $("input[name='pwd']").val();
        data.name = name;
        data.pwd = pwd;

        if(name == '' || pwd == ''){
            alert('用户名或密码不能为空');return;
        }
        // console.log(data);return;
        $.ajax({
            url:"/test/do_login",
            data:data,
            dataType:"json",   
            success:function(res){
                if(res.msg == 1){
                    alert(res.find);
                    location.href="/test/index";
                }else if(res.msg == 2){
                    alert(res.find);
                    location.href="/test/register";
                }else if(res.msg == 3){
                    alert(res.find);
                    location.href="/test/login";
                }else if(res.msg == 4){
                    alert(res.find);
                    location.href="/test/login";
                }else if(res.msg == 5){
                    alert(res.find);
                    location.href="/test/login";
                };
            }
        })
    })
</script>