<?php
/**
 * 登录
 **/
$is_defend=true;
include("../includes/common.php");

if (isset($_GET['telegram'])){
    $tg_login = rc4($_GET['telegram'], $conf['telegram_key']);
    $array = explode("_", $tg_login);
    if (count($array) == 3) {
        $minutesDifference = (time() - $array[2]) / 60;
        if ($minutesDifference > 1) {
            exit("<script language='javascript'>alert('链接失效，请重新登陆。');window.location.href='./login.php';</script>");
        }
        $userrow = $DB->getRow("SELECT * FROM pre_user WHERE uid=:uid limit 1", [':uid' => $array[0]]);
        if ($userrow && ($array[1] == $userrow['key']) && $userrow['telegram']) {
            $city=get_ip_city($clientip);
            $DB->insert('log', ['uid' => $array[0], 'type' => 'Telegram授权登录', 'date' => 'NOW()', 'ip' => $clientip, 'city' => $city]);
            $session = md5($userrow['uid'] . $userrow['key'] . $password_hash);
            $expiretime = time() + 604800;
            $token = authcode("{$array[0]}\t{$session}\t{$expiretime}", 'ENCODE', SYS_KEY);
            setcookie("user_token", $token, time() + 604800);
            $DB->exec("update `pre_user` set `lasttime`=NOW() where `uid`='$uid'");
            exit("<script language='javascript'>window.location.href='./';</script>");
        }
    }
}

if (isset($_GET['id']) && isset($_GET['key'])){
    $uid = $_GET['id'];
    $userrow = $DB->getRow("SELECT * FROM pre_user WHERE uid=:uid limit 1", [':uid' => $uid]);
    if ($userrow && ($_GET['key'] == $userrow['key'])) {
        $city=get_ip_city($clientip);
        $DB->insert('log', ['uid' => $uid, 'type' => 'API授权登录', 'date' => 'NOW()', 'ip' => $clientip, 'city' => $city]);
        $session = md5($uid . $userrow['key'] . $password_hash);
        $expiretime = time() + 604800;
        $token = authcode("{$uid}\t{$session}\t{$expiretime}", 'ENCODE', SYS_KEY);
        setcookie("user_token", $token, time() + 604800);
        $DB->exec("update `pre_user` set `lasttime`=NOW() where `uid`='$uid'");
        exit("<script language='javascript'>window.location.href='./';</script>");
    }else{
        exit("<script language='javascript'>window.location.href='./login.php';</script>");
    }
}

if(isset($_GET['logout'])){
    if(!checkRefererHost())exit();
    setcookie("user_token", "", time() - 604800);
    @header('Content-Type: text/html; charset=UTF-8');
    exit("<script language='javascript'>alert('您已成功注销本次登录！');window.location.href='./login.php';</script>");
}elseif($islogin2==1){
    exit("<script language='javascript'>alert('您已登录！');window.location.href='./';</script>");
}
$csrf_token = md5(mt_rand(0,999).time());
$_SESSION['csrf_token'] = $csrf_token;

$cdnpublic = "https://static.tennsey.cn/pay/";
?>
<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <title>登录 | <?php echo $conf['sitename']?></title>
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
                            <p class="text-muted font-size-15 w-75 mx-auto mt-3 mb-0">Login</p>
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
                                    <h5 class="mb-0">欢迎回来 !</h5>
                                    <p class="text-muted mt-2">Sign in to continue to user.</p>
                                </div>
                                <?php if($_GET['m']=='key'){?><input type="hidden" name="type" value="0"/><?php } else {?><input type="hidden" name="type" value="1"/><?php }?>
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token?>">
                                <div class="card-body">
                                    <ul class="nav nav-tabs nav-justified" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo ($_GET['m'] == "key") ? '' : 'active'; ?>" href="login.php" role="tab">
                                                <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                <span class="d-none d-sm-block">账号登录</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo ($_GET['m'] == "key") ? 'active' : ''; ?>" href="?m=key" role="tab">
                                                <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                <span class="d-none d-sm-block">秘钥登录</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="form-floating form-floating-custom mb-3">
                                    <input type="text" class="form-control" id="usernameInput"
                                           placeholder="Enter User Name">
                                    <label for="usernameInput"><?php echo ($_GET['m'] == "key") ? '商户ID' : '登录账号'; ?></label>
                                    <div class="form-floating-icon">
                                        <i class="uil uil-users-alt"></i>
                                    </div>
                                </div>
                                <div class="form-floating form-floating-custom mb-3 auth-pass-inputgroup">
                                    <input type="password" class="form-control" id="passwordInput"
                                           placeholder="Enter Password">
                                    <button type="button" class="btn btn-link position-absolute h-100 end-0 top-0"
                                            id="password-addon" hidden>
                                        <i class="mdi mdi-eye-outline font-size-18 text-muted"></i>
                                    </button>
                                    <label for="passwordInput"><?php echo ($_GET['m'] == "key") ? '商户秘钥' : '登录密码'; ?></label>
                                    <div class="form-floating-icon">
                                        <i class="uil uil-padlock"></i>
                                    </div>
                                </div>

                                <?php if($conf['captcha_open_login']==1){?>
                                <div class="list-group-item" id="captcha" style="margin: auto;"><div id="captcha_text">
                                        正在加载验证码
                                    </div>
                                    <div id="captcha_wait">
                                        <div class="loading">
                                            <div class="loading-dot"></div>
                                            <div class="loading-dot"></div>
                                            <div class="loading-dot"></div>
                                            <div class="loading-dot"></div>
                                        </div>
                                    </div></div>
                                <div id="captchaform"></div>
                                <?php }?>

                                <div class="form-check form-check-primary font-size-16 py-1">
                                    <input class="form-check-input" type="checkbox" id="remember-check">
                                    <div class="float-end">
                                        <a href="findpwd.php"
                                           class="text-muted text-decoration-underline font-size-14">忘记密码 ?</a>
                                    </div>
                                    <label class="form-check-label font-size-14" for="remember-check">
                                        记住我
                                    </label>
                                </div>
                                <div class="mt-3">
                                    <button id="submit" class="btn btn-primary w-100" type="submit">登录</button>
                                </div>
                                <div class="mt-4 text-center">
                                    <?php if(!isset($_GET['connect'])){?>
                                    <?php if($conf['login_alipay']!=0 || $conf['login_qq']!=0 || $conf['login_wx']!=0){?>
                                    <div class="signin-other-title">
                                        <h5 class="font-size-15 mb-4 text-muted fw-medium">- 或者可以使用 -</h5>
                                    </div>
                                        <?php }?>
                                    <div class="d-flex gap-2">
                                        <div class="wrapper text-center">
                                        <?php if($conf['login_alipay']>0 || $conf['login_alipay']==-1){?>
                                            <button type="button" class="btn btn-rounded btn-lg btn-icon btn-default" title="支付宝快捷登录" onclick="connect('alipay')"><img src="../assets/icon/alipay.ico" style="border-radius:50px;"></button>
                                        <?php }?>
                                        <?php if($conf['login_qq']>0){?>
                                            <button type="button" class="btn btn-rounded btn-lg btn-icon btn-default" title="QQ快捷登录" onclick="connect('qq')"><img src="../assets/icon/qqpay.ico" style="border-radius:50px;"></button>
                                        <?php }?>
                                        <?php if($conf['login_wx']>0 || $conf['login_wx']==-1){?>
                                            <button type="button" class="btn btn-rounded btn-lg btn-icon btn-default" title="微信快捷登录" onclick="connect('wx')"><img src="../assets/icon/wxpay.ico" style="border-radius:50px;"></button>
                                            </div>
                                        <?php }?>
                                    </div>
                                    <?php }?>
                                </div>
                                <div class="mt-4 pt-3 text-center">
                                    <p class="text-muted mb-0">没有账户吗 ? <a href="reg.php" class="fw-semibold text-decoration-underline"> 立即注册 </a></p>
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
                            <i class="mdi mdi-heart text-danger"></i> <a
                                href="/"><?php echo $conf['sitename']?></a></p>
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
<script src="<?php echo $cdnpublic?>bs5/js/pass-addon.init.js"></script>
<script src="<?php echo $cdnpublic?>bs5/libs/alertifyjs/build/alertify.min.js"></script>
<script src="<?php echo $cdnpublic?>bs5/js/jquery-3.6.0.min.js"></script>
<script src="//static.geetest.com/static/tools/gt.js"></script>
<script>
    var captcha_open = 0;
    var handlerEmbed = function (captchaObj) {
        captchaObj.appendTo('#captcha');
        captchaObj.onReady(function () {
            $("#captcha_wait").hide();
        }).onSuccess(function () {
            var result = captchaObj.getValidate();
            if (!result) {
                return alertify.error('请完成验证');
            }
            $("#captchaform").html('<input type="hidden" name="geetest_challenge" value="'+result.geetest_challenge+'" /><input type="hidden" name="geetest_validate" value="'+result.geetest_validate+'" /><input type="hidden" name="geetest_seccode" value="'+result.geetest_seccode+'" />');
            $.captchaObj = captchaObj;
        });
    };
    $(document).ready(function(){
        if($("#captcha").length>0) captcha_open=1;
        $("#submit").click(function(){
            var type=$("input[name='type']").val();
            var user=$("input[name='usernameInput']").val();
            var pass=$("input[name='passwordInput']").val();
            if(user==='' || pass===''){alertify.error(type===1?'账号和密码不能为空！':'ID和密钥不能为空！');return false;}
            submitLogin(type,user,pass);
        });
        if(captcha_open===1){
            $.ajax({
                url: "./ajax.php?act=captcha&t=" + (new Date()).getTime(),
                type: "get",
                dataType: "json",
                success: function (data) {
                    $('#captcha_text').hide();
                    $('#captcha_wait').show();
                    initGeetest({
                        gt: data.gt,
                        challenge: data.challenge,
                        new_captcha: data.new_captcha,
                        product: "popup",
                        width: "100%",
                        offline: !data.success
                    }, handlerEmbed);
                }
            });
        }
    });

    function submitLogin(type,user,pass){
        var type=$("input[name='type']").val();
        var username = document.getElementById('usernameInput').value;
        var password = document.getElementById('passwordInput').value;

        var csrf_token=$("input[name='csrf_token']").val();
        var data = {type:type, user:username, pass:password, csrf_token:csrf_token};
        if(captcha_open === 1){
            var geetest_challenge = $("input[name='geetest_challenge']").val();
            var geetest_validate = $("input[name='geetest_validate']").val();
            var geetest_seccode = $("input[name='geetest_seccode']").val();
            if(geetest_challenge === ""){
                alertify.error("请先完成滑动验证！"); return false;
            }
            var adddata = {geetest_challenge:geetest_challenge, geetest_validate:geetest_validate, geetest_seccode:geetest_seccode};
        }
        $.ajax({
            type: 'POST',
            url: 'ajax.php?act=login',
            data: Object.assign(data, adddata),
            success: function(response) {
                if (response.code === 0){
                    alertify.success(response.msg)
                    setTimeout(function(){ window.location.href=response.url }, 500);
                } else {
                    alertify.warning(response.msg)
                    // $.captchaObj.reset();
                }
            },
            error: function(xhr, status, error) {
                alertify.error("服务器错误，请稍后再试~")
                return false;
            }
        });
    }

    function connect(type){
        $.ajax({
            type : "POST",
            url : "ajax.php?act=connect",
            data : {type:type},
            dataType : 'json',
            success : function(data) {
                if(data.code === 0){
                    window.location.href = data.url;
                }else{
                    alertify.error(data.msg, {icon: 7});
                }
            }
        });
    }
</script>
</body>
</html>

