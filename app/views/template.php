<!DOCTYPE html>
<html>
<head>
    <title><?=$title?></title>
    <meta http-equiv="Content-Type; charset=UTF-8"/>
    <link href="<?=basepath?>assets/css/bootstrap.css" rel="stylesheet" />
    <link href="<?=basepath?>assets/css/font-awesome.min.css" rel="stylesheet" />
    <link href="<?=basepath?>assets/css/layout.css" rel="stylesheet" />
</head>

<body>
    <div class="mycontainer">
        <div class="header">
            <h1><?=$title?></h1>
        </div>
        <div class="body" id="body">
            <div class="row"><?=$body?></div>
            <div class="row">
                <div class="message alert alert-success"></div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="<?=basepath?>assets/js/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="<?=basepath?>assets/js/jquery-ui-1.10.4.custom.min.js"></script>
    <script type="text/javascript" src="<?=basepath?>assets/js/action.js"></script>
</body>
</html>
