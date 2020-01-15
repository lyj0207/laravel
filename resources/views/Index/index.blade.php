<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
</head>
<body>
        <center>
            <form>
                Account： <input type="text" name="name" ><br><br>
                PassWord： <input type="password" name="pwd"><br><br>
                <button class="btn">Login</button>
            </form>
        </center>
</body>
</html>

<script src="/jquery.js"></script>

<script>
    $(document).on('click','.btn',function(){
        event.preventDefault();
        var name = $("[name=name]").val();     
        var pwd = $("[name=pwd]").val(); 

        $.ajax({
            url:"http://www.index.com/Index/do_login",
            dataType:"json",
            data:{name:name,pwd:pwd},
            type:"post",
            success:function(res){

            }
        })
    })
</script>