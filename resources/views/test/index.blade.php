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
            <table border="1">
                <tr>
                    <td>ID</td>
                    <td>NAME</td>
                </tr>
            @foreach($all as $k=>$v)
                <tr>
                    <td>{{$k+1}}</td>
                    <td>{{$v['name']}}</td>
                </tr>
            @endforeach
            </table> 
        </center>
</body>
</html>