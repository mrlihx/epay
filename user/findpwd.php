<?php
include("../includes/common.php");

$csrf_token = md5(mt_rand(0,999).time());
$_SESSION['csrf_token'] = $csrf_token;
$cdnpublic = "https://static.tennsey.cn/pay/";
?>
<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8" />
    <title>找回密码 | <?php echo $conf['sitename']?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?php echo $cdnpublic?>bs5/libs/alertifyjs/build/css/alertify.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $cdnpublic?>bs5/libs/alertifyjs/build/css/themes/default.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $cdnpublic?>bs5/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="<?php echo $cdnpublic?>bs5/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $cdnpublic?>bs5/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="auth-bg-basic d-flex align-items-center min-vh-100">
    <div class="bg-overlay bg-light"></div>
    <div class="container">
        <div class="d-flex flex-column min-vh-100 py-5 px-3">
            <div class="row justify-content-center">
                <div class="col-xl-5">
                    <div class="text-center text-muted mb-2">
                        <div class="pb-3">
                            <a href="/"><span class="logo-lg"><img src="<?php echo $cdnpublic?>bs5/images/logo-sm.svg" alt="" height="24"> <span class="logo-txt"><?php echo $conf['sitename']?></span></span></a>
                            <p class="text-muted font-size-15 w-75 mx-auto mt-3 mb-0">Retrieve the password.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center my-auto">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card bg-transparent shadow-none border-0">
                        <div class="card-body">
                            <div class="py-3">
                                <div class="text-center">
                                    <h5 class="mb-0">找回密码</h5>
                                    <p class="text-muted mt-2">Change the password to continue accessing the user.</p>
                                </div>

                                <form class="mt-3">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token?>">
                                    <div class="form-floating form-floating-custom mb-3">
                                        <select class="form-select pt-3" name="type" id="type">
                                            <option value="email">使用邮箱找回</option>
                                            <option value="phone">使用手机找回</option>
                                        </select>
                                        <label for="type">选择找回方式</label>
                                        <div class="form-floating-icon">
                                            <i class="uil uil-envelope-edit"></i>
                                        </div>
                                    </div>

                                    <div class="form-floating form-floating-custom mb-3">
                                        <input type="text" class="form-control" id="accountInput" name="accountInput" placeholder="登录邮箱" required>
                                        <label for="accountInput" id="accountInput">登录邮箱</label>
                                        <div class="form-floating-icon">
                                            <i class="uil uil-envelope-alt" id="icon"></i>
                                        </div>
                                    </div>
                                    <div class="form-floating form-floating-custom mb-3 d-flex align-items-center">
                                        <input type="text" id="vcodeInput" name="vcodeInput" class="form-control" placeholder="邮箱验证码">
                                        <label for="vcodeInput" id="vcodeInput">邮箱验证码</label>
                                        <div class="form-floating-icon">
                                            <i class="uil uil-shield"></i>
                                        </div>
                                        <div class="col-auto">
                                            <button class="btn btn-primary" id="sendcode">获取验证码</button>
                                        </div>
                                    </div>

                                    <div class="form-floating form-floating-custom mb-3">
                                        <input type="password" class="form-control" name="pwdInput" id="pwdInput" placeholder="输入新的密码" required>
                                        <label for="pwdInput">输入新的密码</label>
                                        <div class="form-floating-icon">
                                            <i class="uil uil-padlock"></i>
                                        </div>
                                    </div>
                                    <div class="form-floating form-floating-custom mb-3">
                                        <input type="password" class="form-control" name="pwd2Input" id="pwd2Input" placeholder="再次输入密码" required>
                                        <label for="pwd2Input">再次输入密码</label>
                                        <div class="form-floating-icon">
                                            <i class="uil uil-padlock"></i>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button type="button" id="submit" class="btn btn-primary w-100 btn-block" ng-click="login()" ng-disabled='form.$invalid'>确认修改</button>
                                    </div>
                                </form>
                            </div>

                            <div class="mt-4 pt-3 text-center">
                                <p class="text-muted mb-0">想起密码了 ? <a href="login.php" class="fw-semibold text-decoration-underline"> 返回登录 </a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="mt-4 mt-md-5 text-center">
                        <p class="mb-0">©
                            <script>document.write(new Date().getFullYear())</script>
                            <i class="mdi mdi-heart text-danger"></i> <a href="/"><?php echo $conf['sitename']?></a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo $cdnpublic?>bs5/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo $cdnpublic?>bs5/libs/metismenujs/metismenujs.min.js"></script>
<script src="<?php echo $cdnpublic?>bs5/libs/simplebar/simplebar.min.js"></script>
<script src="<?php echo $cdnpublic?>bs5/libs/feather-icons/feather.min.js"></script>
<script src="<?php echo $cdnpublic?>bs5/libs/alertifyjs/build/alertify.min.js"></script>
<script src="<?php echo $cdnpublic?>bs5/js/jquery-3.6.0.min.js"></script>
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.min.js"></script>
<script src="//static.geetest.com/static/tools/gt.js"></script>
<script>
    function InvokeSettime(obj){
        var countdown=60;
        settime(obj);
        function settime(obj) {
            if (countdown === 0) {
                $(obj).attr("data-lock", "false");
                $(obj).text("获取验证码");
                countdown = 60;
                return;
            } else {
                $(obj).attr("data-lock", "true");
                $(obj).attr("disabled",true);
                $(obj).text("(" + countdown + ") s 重新发送");
                countdown--;
            }
            setTimeout(function() {
                    settime(obj) }
                ,1000)
        }
    }
    var handlerEmbed = function (captchaObj) {
        var sendto,type;
        captchaObj.onReady(function () {
            $("#wait").hide();
        }).onSuccess(function () {
            var result = captchaObj.getValidate();
            if (!result) {
                return alert('请完成验证');
            }
            var ii = layer.load(2, {shade:[0.1,'#fff']});
            $.ajax({
                type : "POST",
                url : "ajax.php?act=sendcode2",
                data : {type:type,sendto:sendto,geetest_challenge:result.geetest_challenge,geetest_validate:result.geetest_validate,geetest_seccode:result.geetest_seccode},
                dataType : 'json',
                success : function(data) {
                    layer.close(ii);
                    if(data.code === 0){
                        new InvokeSettime("#sendsms");
                        layer.msg('发送成功，请注意查收！');
                    }else{
                        alertify.error(data.msg);
                        captchaObj.reset();
                    }
                }
            });
        });
        $('#sendcode').click(function () {
            if ($(this).attr("data-lock") === "true") return;
            type = $("select[name='type']").val();
            sendto=$("input[name='accountInput']").val();
            if(type==='phone'){
                if(sendto===''){alertify.error('手机号码不能为空！');return false;}
                if(sendto.length!==11){alertify.error('手机号码不正确！');return false;}
            }else{
                if(sendto===''){alertify.error('邮箱不能为空！');return false;}
                var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
                if(!reg.test(sendto)){alertify.error('邮箱格式不正确！');return false;}
            }
            captchaObj.verify();
        });
    };
    $(document).ready(function(){
        $("select[name='type']").change(function(){
            if($(this).val() === 'email'){
                $("label[for='accountInput']").text('邮箱');
                $("label[for='vcodeInput']").text('邮箱验证码');
            }else{
                $("label[for='accountInput']").text('手机号');
                $("label[for='vcodeInput']").text('短信验证码');
            }
        });
        $("select[name='type']").change();
        $("#submit").click(function(){
            if ($(this).attr("data-lock") === "true") return;
            var type=$("select[name='type']").val();
            var account=$("input[name='accountInput']").val();
            var code=$("input[name='vcodeInput']").val();
            var pwd=$("input[name='pwdInput']").val();
            var pwd2=$("input[name='pwd2Input']").val();
            if(account==='' || code==='' || pwd==='' || pwd2===''){alertify.error('请确保各项不能为空！');return false;}
            if(pwd!==pwd2){alertify.error('两次输入密码不一致！');return false;}
            if(type==='phone'){
                if(account.length!==11){alertify.error('手机号码不正确！');return false;}
            }else{
                var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
                if(!reg.test(account)){alertify.error('邮箱格式不正确！');return false;}
            }
            var csrf_token=$("input[name='csrf_token']").val();
            var ii = layer.load(2, {shade:[0.1,'#fff']});
            $(this).attr("data-lock", "true");
            $.ajax({
                type : "POST",
                url : "ajax.php?act=findpwd",
                data : {type:type,account:account,code:code,pwd:pwd,csrf_token:csrf_token},
                dataType : 'json',
                success : function(data) {
                    $("#submit").attr("data-lock", "false");
                    layer.close(ii);
                    if(data.code === 1){
                        layer.alert(data.msg, {icon: 1}, function(){window.location.href="login.php"});
                    }else{
                        alertify.error(data.msg);
                    }
                }
            });
        });
        $.ajax({
            // 获取id，challenge，success（是否启用failback）
            url: "ajax.php?act=captcha&t=" + (new Date()).getTime(), // 加随机数防止缓存
            type: "get",
            dataType: "json",
            success: function (data) {
                console.log(data);
                // 使用initGeetest接口
                // 参数1：配置参数
                // 参数2：回调，回调的第一个参数验证码对象，之后可以使用它做appendTo之类的事件
                initGeetest({
                    width: '100%',
                    gt: data.gt,
                    challenge: data.challenge,
                    new_captcha: data.new_captcha,
                    product: "bind", // 产品形式，包括：float，embed，popup。注意只对PC版验证码有效
                    offline: !data.success // 表示用户后台检测极验服务器是否宕机，一般不需要关注
                    // 更多配置参数请参见：http://www.geetest.com/install/sections/idx-client-sdk.html#config
                }, handlerEmbed);
            }
        });
    });
</script>
</body>
</html>
