<?php
$is_defend=true;
include("../includes/common.php");
if(isset($_GET['regok'])){
    exit("<script language='javascript'>alert('恭喜你，商户注册成功！');window.location.href='./login.php';</script>");
}
if($islogin2==1){
    exit("<script language='javascript'>alert('您已登录！');window.location.href='./';</script>");
}

if($conf['reg_open']==0)sysmsg('未开放商户申请');

$csrf_token = md5(mt_rand(0,999).time());
$_SESSION['csrf_token'] = $csrf_token;

$cdnpublic = "https://static.tennsey.cn/pay/";
?>
<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <title>申请商户 | <?php echo $conf['sitename']?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?php echo $cdnpublic?>bs5/libs/alertifyjs/build/css/alertify.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $cdnpublic?>bs5/libs/alertifyjs/build/css/themes/default.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $cdnpublic?>bs5/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $cdnpublic?>bs5/css/icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $cdnpublic?>bs5/css/app.min.css" id="app-style" rel="stylesheet" type="text/css"/>
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
                            <p class="text-muted font-size-15 w-75 mx-auto mt-3 mb-0">Register</p>
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
                                    <h5 class="mb-0">注册账号</h5>
                                    <p class="text-muted mt-2">Register in to continue to user.</p>
                                </div>
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token?>"><input type="hidden" name="verifytype" value="<?php echo $conf['verifytype']?>">

                                <div class="text-center">
                                    <p class="mb-0">
                                    <?php if($conf['reg_pay']){?><div class="wrapper">商户申请价格为：<b><?php echo $conf['reg_pay_price']?></b>元</div><?php }?>
                                    </p>
                                </div>


                                <?php if($conf['verifytype']==1){?>
                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="text" class="form-control" name="phone" id="phoneInput" placeholder="登录手机" required>
                                    <label for="emailInput">登录手机</label>
                                    <div class="form-floating-icon">
                                        <i class="uil uil-envelope-alt"></i>
                                    </div>
                                </div>
                                <div class="form-floating form-floating-custom mb-3 d-flex align-items-center">
                                    <input type="text" name="code" class="form-control" placeholder="短信验证码" required>
                                    <label for="usernameInput">短信验证码</label>
                                    <div class="form-floating-icon">
                                        <i class="uil uil-shield"></i>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-primary" id="sendcode">获取验证码</button>
                                    </div>
                                </div>
                                <?php }else{?>
                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="email" class="form-control" name="email" id="emailInput" placeholder="登录邮箱" required>
                                    <label for="emailInput">登录邮箱</label>
                                    <div class="form-floating-icon">
                                        <i class="uil uil-envelope-alt"></i>
                                    </div>
                                </div>
                                <div class="form-floating form-floating-custom mb-3 d-flex align-items-center">
                                    <input type="text" name="code" class="form-control" placeholder="邮箱验证码" required>
                                    <label for="usernameInput">邮箱验证码</label>
                                    <div class="form-floating-icon">
                                        <i class="uil uil-shield"></i>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-primary" id="sendcode">获取验证码</button>
                                    </div>
                                </div>
                                <?php }?>

                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="password" class="form-control" name="pwd" id="passwordInput" placeholder="登录密码" required>
                                    <label for="passwordInput">登录密码</label>
                                    <div class="form-floating-icon">
                                        <i class="uil uil-padlock"></i>
                                    </div>
                                </div>
                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="password" class="form-control" name="pwd2" id="passwordRepeat" placeholder="再次输入密码" required>
                                    <label for="passwordRepeat">再次输入密码</label>
                                    <div class="form-floating-icon">
                                        <i class="uil uil-padlock"></i>
                                    </div>
                                </div>
                                <?php if($conf['reg_open']==2){?>
                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="text" class="form-control" name="invitecode" placeholder="邀请码" required>
                                    <label for="inviteRepeat">邀请码</label>
                                    <div class="form-floating-icon">
                                        <i class="uil uil-user-plus"></i>
                                    </div>
                                </div>
                                <?php }?>

                                <div class="py-1">
                                    <p class="mb-0">注册即表示您同意<?php echo $conf['sitename']?> <a href="../agreement.html" class="text-primary">使用条款</a></p>
                                </div>
                                <div class="mt-3">
                                    <button id="submit" class="btn btn-primary w-100" type="submit">注册</button>
                                </div>
                                <div class="mt-4 pt-3 text-center">
                                    <p class="text-muted mb-0">已经有账号了 ? <a href="login.php" class="fw-semibold text-decoration-underline"> 登录 </a></p>
                                </div>

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

<div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle">注册须知</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php echo $conf['zhuce']?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">知道了</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $cdnpublic?>bs5/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo $cdnpublic?>bs5/libs/metismenujs/metismenujs.min.js"></script>
<script src="<?php echo $cdnpublic?>bs5/libs/simplebar/simplebar.min.js"></script>
<script src="<?php echo $cdnpublic?>bs5/libs/feather-icons/feather.min.js"></script>
<script src="<?php echo $cdnpublic?>bs5/libs/alertifyjs/build/alertify.min.js"></script>
<script src="<?php echo $cdnpublic?>bs5/js/jquery-3.6.0.min.js"></script>
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.min.js"></script>
<script src="//static.geetest.com/static/tools/gt.js"></script>
<script>
    function invokeSettime(obj){
        var countdown=60;
        settime(obj);
        function settime(obj) {
            if (countdown === 0) {
                $(obj).attr("data-lock", "false");
                $(obj).attr("disabled",false);
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
        var sendto;
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
                url : "ajax.php?act=sendcode",
                data : {sendto:sendto,geetest_challenge:result.geetest_challenge,geetest_validate:result.geetest_validate,geetest_seccode:result.geetest_seccode},
                dataType : 'json',
                success : function(data) {
                    layer.close(ii);
                    if(data.code === 0){
                        new invokeSettime("#sendsms");
                        alertify.success('发送成功，请注意查收！');
                    }else{
                        alertify.error(data.msg);
                        captchaObj.reset();
                    }
                }
            });
        });
        $('#sendcode').click(function () {
            if ($(this).attr("data-lock") === "true") return;
            if($("input[name='verifytype']").val()==='1'){
                sendto=$("input[name='phone']").val();
                if(sendto===''){alertify.error('手机号码不能为空！');return false;}
                if(sendto.length!==11){alertify.error('手机号码不正确！');return false;}
            }else{
                sendto=$("input[name='email']").val();
                if(sendto===''){alertify.error('邮箱不能为空！');return false;}
                var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
                if(!reg.test(sendto)){alertify.error('邮箱格式不正确！');return false;}
            }
            captchaObj.verify();
        });
    };
    $(document).ready(function(){
        $("#submit").click(function(){
            if ($(this).attr("data-lock") === "true") return;
            var email=$("input[name='email']").val();
            var phone=$("input[name='phone']").val();
            var code=$("input[name='code']").val();
            var pwd=$("input[name='pwd']").val();
            var pwd2=$("input[name='pwd2']").val();
            var invitecode=$("input[name='invitecode']").val();
            if(email==='' || phone==='' || code==='' || pwd==='' || pwd2===''){alertify.error('请确保各项不能为空！');return false;}
            if($("input[name='invitecode']").length>0 && invitecode===''){alertify.error('邀请码不能为空！');return false;}
            if(pwd!=pwd2){alertify.error('两次输入密码不一致！');return false;}
            if($("input[name='verifytype']").val()==='1'){
                if(phone.length!=11){alertify.error('手机号码不正确！');return false;}
            }else{
                var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
                if(!reg.test(email)){alertify.error('邮箱格式不正确！');return false;}
            }
            var ii = layer.load();
            $(this).attr("data-lock", "true");
            var csrf_token=$("input[name='csrf_token']").val();
            $.ajax({
                type : "POST",
                url : "ajax.php?act=reg",
                data : {email:email,phone:phone,code:code,pwd:pwd,invitecode:invitecode,csrf_token:csrf_token},
                dataType : 'json',
                success : function(data) {
                    $("#submit").attr("data-lock", "false");
                    layer.close(ii);
                    if(data.code === 1){
                        layer.alert('恭喜你，商户申请成功！', {icon: 1}, function(){
                            window.location.href="./login.php";
                        });
                    }else if(data.code === 2){
                        var paymsg = '';
                        $.each(data.paytype, function(key, value) {
                            paymsg+='<button class="btn btn-default btn-block" onclick="window.location.href=\'../submit2.php?typeid='+key+'&trade_no='+data.trade_no+'\'" style="margin-top:10px;"><img width="20" src="../assets/icon/'+value.name+'.ico" class="logo">'+value.showname+'</button>';
                        });
                        layer.alert('<center><h2>￥ '+data.need+'</h2><hr>'+paymsg+'<hr>提示：支付完成后即可直接登录</center>',{
                            btn:[],
                            title:'支付确认页面',
                            closeBtn: false
                        });
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
        <?php if(!empty($conf['zhuce'])){?>
        $('#exampleModalScrollable').modal('show');
        <?php }?>
    });
</script>
</body>
</html>
