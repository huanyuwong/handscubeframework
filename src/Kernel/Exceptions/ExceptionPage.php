<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Whoops!</title>
    <style>
        body{
            margin: 0;
            font-family: "Helvetica Neue", helvetica, arial, sans-serif;
        }
        .error-header{
            background-color: #2a2a2a;
            padding: 35px 40px;
            font-size: 18px;
        }
        p{
            margin: 0;
        }
        .error-header>p{
            line-height: 18px;
            color: #fff;
        }
        .error-header>.whoops{
            color: #fff;
            font-size: 34px;
            font-family: Courier,Verdana,"微软雅黑"
        }
        .error-header>p>.err-type{
            /* font-size: 24px; */
            color: #e95353;
            font-family: Courier,Verdana,"微软雅黑";
        }
        .err-message{
            color: #fff;
            font-size: 23px;
            /* font-weight: bold; */
            font-family: Courier,Verdana;
        }
        .err-file{
            color: #fff;
            cursor: pointer;
            display: inline-block;
            margin-top: 10px;
        }
        .err-line{
            color: #e95353;
        }
        .margin-top-10{
            margin-top: 10px;
        }
        .margin-top-20{
            margin-top: 20px;
        }
        .err-container{
            padding: 0;
            margin: 0;
            font-family: "Helvetica Neue", helvetica, arial, sans-serif;
        }
        .display-flex{
            display: flex;
        }
        .err-item{
            font-size: 14px;
            list-style: none;
            cursor: pointer;
            transition: all 0.1s ease;
            background: #f3f1f1;
            padding: 14px;
            color: #a29d9d;
            border-right: 4px solid #eeeeee;
            word-wrap:break-word;
        }
        .err-item:not(:last-child){
            border-bottom: 1px solid rgba(0, 0, 0, .05);
        }
        .err-item:hover{
            border-right-color: #4288CE;
            background: #d3f4f9;
        }
        .err-index{
            display: block;
            font-size: 12px;
            color: #bebebe;
            background-color: #2a2a2a;
            height: 18px;
            width: 18px;
            line-height: 18px;
            border-radius: 5px;
            padding: 0 1px 0 1px;
            text-align: center;
        }
        .err-title{
            flex: 1;
            margin-left: 5px;
            margin-bottom: 10px;
            color: #131313;
            word-wrap:break-word;
        }
        .err-detail{
            display: flex;
        }
        .err-detail>.err-container{
            width: 200px;
        }
        .err-code{
            display: none;
            flex: 1;
            padding: 5px;
            background: #303030;
        }
        .err-detail>.err-code{
            display: block;
        }
        .code-file{
            color: #a29d9d;
            font-size: 12px;
            padding: 12px 6px;
            word-wrap:break-word;
        }
    </style>
</head>
<body>
    <div class="error-header">
        <p class="whoops">Whoops!</h3>
        <p class="margin-top-20"><span class="err-type">ErrorException</span><?php echo '[' . $exception['errCode'] . ']' ?></p>
        <p class="margin-top-10"><span class="err-message"> <?php echo $exception['errType'] . ' : ' ?>
 <?php echo $exception['errMsg'] . ' in ' ?></span>
        <span class="err-file"><?php echo $exception['errFile'] ?></span><span class="err-line"><?php echo ' line ' . $exception['errLine'] ?></span></p>
    </div>
    <div>
        <ul class="err-container">
        <?php foreach ($exception['methods'] as $index => $item) {?>
            <li class="err-item">
                <div class="display-flex">
                    <span class="err-index"><?php echo $index ?></span>
                    <div class="err-title">
                        <?php echo $item ?>
                    </div>
                </div>
                <div>
                      <?php echo $exception['files'][$index] ?>
                </div>
            </li>
        <?php }?>
        </ul>
    </div>
</body>
<script>

</script>
</html>
