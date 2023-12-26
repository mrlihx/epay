<?php
// 微信手机扫码支付页面

if (!defined('IN_PLUGIN')) exit();
$durl = dwz($code_url);
?>
<!DOCTYPE html>
<html lang="ch-cn">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>微信收银台-<?php echo $sitename ?></title>
    <link rel="stylesheet" href="/assets/paypage/css/bootstrap-reboot.min.css">
    <link rel="stylesheet" href="/assets/paypage/css/bootstrap-grid.css">
    <link rel="stylesheet" href="/assets/paypage/css/pc_qrcode.css">
<!--    <link rel="stylesheet" href="/assets/paypage/css/mobile_qrcode.css">-->
    <link rel="stylesheet" href="/assets/paypage/css/layer.css" id="layuicss-layer">


<!--    <style>-->
<!--        .order .buttom_info{-->
<!--            padding-top: .56rem;-->
<!--            padding-bottom: .6rem;-->
<!--            border-bottom: .013333rem solid #ededed;-->
<!--            text-align: center;-->
<!--        }-->
<!---->
<!--        .order .buttom_info .dakai {-->
<!--            line-height: .6rem;-->
<!--            padding: .15rem .373333rem;-->
<!--            background: linear-gradient(-45deg,#3369ff,#5cabf8);-->
<!--            border-radius: .306667rem;-->
<!--            color: #fff;-->
<!--            font-size: .293333rem;-->
<!--            top: 0;-->
<!--        }-->
<!--        .order .buttom_info p {-->
<!--            display: block;-->
<!--            color: #999;-->
<!--            font-size: .37333rem;-->
<!--            margin-top: .233333rem;-->
<!--            margin-bottom: .233333rem;-->
<!--        }-->
<!--    </style>-->
</head>
<body>

<header class="header">
    <div class="header__wrap">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="header__content d-flex justify-content-between">
                        <div class="header__logo d-flex align-items-center">
                            <svg t="1610806307396" class="icon" viewBox="0 0 1024 1024" version="1.1"
                                 xmlns="http://www.w3.org/2000/svg" p-id="6171" width="26" height="26">
                                <path d="M1024 199.18v410.38c0 110-89.54 199.18-200 199.18H200c-110.46 0-200-89.18-200-199.18V199.18C0 89.17 89.54 0 200 0h624c110.46 0 200 89.17 200 199.18z m-553.95 317v46.72q0.9 19.32 12 28.75t30.9 9.43q40.14 0 41.95-38.18v-47.58l86.6 0.45q11.73-0.9 18.49-8.76t7.67-19.54a33.48 33.48 0 0 0-7.67-19.32q-6.77-8.09-18.49-9h-86.6v-27.4l86.15-0.45q11.73-0.9 18.72-9a33.26 33.26 0 0 0 7.89-19.76q-0.9-11.23-7.67-18.42t-18.49-8.09h-66.3l69.91-113.2q9-11.68 9-24.71a50.37 50.37 0 0 0-4.28-15.27 24.48 24.48 0 0 0-7.22-9 27.29 27.29 0 0 0-9.92-4.49 74.75 74.75 0 0 0-12.4-1.8 43.43 43.43 0 0 0-19.4 7.19 54.51 54.51 0 0 0-14 13.48l-75.34 125.83L443 229.18A65.48 65.48 0 0 0 429 215a36.39 36.39 0 0 0-19.4-7.41q-18.49 2.25-25.26 10.11t-9 20.44a36.94 36.94 0 0 0 3.61 18.19 67.53 67.53 0 0 0 8.57 13.7l60.44 106H383q-12.18 0.9-18.72 8.09t-7.89 18.42q1.35 11.68 7.89 19.32t18.72 8.56l87.05 0.45v28.3H383q-12.18 0.9-18.72 8.09t-7.89 18.42a43.81 43.81 0 0 0 7.89 20.44q6.54 9.21 18.72 10.11h87.05z"
                                      fill="#4375ff" p-id="6172"></path>
                                <path d="M264.96 903.6m60.2 0l373.67 0q60.2 0 60.2 60.2l0 0q0 60.2-60.2 60.2l-373.67 0q-60.2 0-60.2-60.2l0 0q0-60.2 60.2-60.2Z"
                                      fill="#4375ff" p-id="6173"></path>
                            </svg>
                            <span class="ml-2">收银台</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>


<section class="section details__section section--first  section--last">
    <div class="container">

        <div class="row">
            <div class="col-12">
                <div class="time">
                    <span>请使用微信扫码完成支付 </span>
                </div>
                <div class="order">
                    <span>订单号：<?php echo $order['trade_no'] ?></span>
                    <span data-clipboard-text="<?php echo $order['trade_no'] ?>" class="copy"
                          id="clipboard_order">复制</span>
                </div>
                <div class="goods_name">
                    <span><?php echo $order['name'] ?></span>
                </div>
                <div class="pay_type mt-4">
                    <img src="/assets/paypage/img/wx.png">
                    <span>微信扫码支付</span>
                </div>
                <div class="mod-ct">
                    <div class="order"></div>
                    <div class="qr-image" id="qrcode"></div>
                </div>
                <div class="price mt-4">
                    <span><?php echo $order['realmoney'] ?></span>
                    <span>元</span>
                </div>
                <p class="mt-2" style="color:#3259ff;font-size: 13px">防止支付失败 会随机增减几分</p>

                <div class="goods_name">
                    请截屏扫码付款或复制下方链接在微信内打开
                    <span data-clipboard-text="<?php echo $durl ?>" class="copy" style="color:#3259ff;font-size: 13px" id="clipboard_payurl"><?php echo $durl ?></span>
                </div>

                <div class="shanxinzha">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAACiElEQVR4nO2a0XHDIAyGGcEjZISMkBE6QkfICNmgI3QEj+ARMoIegJ9Hj9A+WNy1vcbItrCcHN8drwYJ6UcWONdoNBqNRqNRESLqvPeXGOMNwAeAAcAdwMjjDqBPKX3GGG/e+4v1mjeTjU4pfbKRXytGH0J4t7ZlEUTUAbgCoJVGPxpXa9uKeO8vHM6ahv8cdMiI4F3vKxr+d9yJqLO22znnXAjhXCHcJWMMIZxNjeeQXytwWuPNxPgQwvsBjLdxAof9UuNHAEM+50MIZyLqiKjj771hOj2GNU7YLR1Y8JYo/QjgulS02BmLHLyLMEKu9mOM8bZ1UVw9Sp0waNn5Lxym0l1Xy8slKVetTlgQ+veU0kl7/pTSSTj/qD23c06ck1XPZt6EYiTEGG8WE6uG/SO49ihuhGoUCnP/Q23CAhJhVNUCgfLvcwQxHJFUWFO/22TqOSeAK9H6YigJ/xqqX4I3pn4aCNS/bvExv7bZ1FSJTG5pHSr8M4I02K4DAgG0+R11oiNxe3SiUH1ZNiUEOrBdCEsngIUAZvZywGwFaN2bK2yQigMOmwK8vjmNUtGAYc4B1rc4/Jf47+6rrA3TVdZcnplfWHC/IEfCCGBQi0yBA3Rq7qMCQSlsLYRVkfQCDnldpUmpHFY5bo6MoOY2/SeojrQXZ30kVgXCpqhlaVydQtmZB72sEyRaoFqFHZHSv8GfsVuneDeEHdlf0fBydQLX3lIH/CqbAVz59djJ2o5NCG9oJIMwvRE8Wdu0GEUn5JLa9s3PGpQfST3nn+WKVyMPBdPalk1g+wvR53ZABsvf9+RhdstUBXaE+E3Ry1aPzk2dJb7X7zE1WnOq6PbwGo1Go9FoNIR8A0fjmlx859ePAAAAAElFTkSuQmCC">
                    <span>正在检测订单状态 <p>0</p> 次</span>
                </div>

            </div>
        </div>
    </div>
</section>

<script src="<?php echo $cdnpublic ?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?php echo $cdnpublic ?>layer/3.1.1/layer.min.js"></script>
<script src="<?php echo $cdnpublic ?>jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
<script src="<?php echo $cdnpublic ?>clipboard.js/1.7.1/clipboard.min.js"></script>

<script>
    var clipboard = new Clipboard('#clipboard_order');
    clipboard.on('success', function (e) {
        layer.msg('复制成功');
    });
    clipboard.on('error', function (e) {
        layer.msg('复制失败');
    });

    var payurl = new Clipboard('#clipboard_payurl');
    payurl.on('success', function (e) {
        layer.msg('复制成功，请到微信里面粘贴。');
    });
    clipboard.on('error', function (e) {
        layer.msg('复制失败，请长按链接后手动复制');
    });

    var code_url = '<?php echo $code_url?>';
    var code_type = code_url.indexOf('data:image/') > -1 ? 1 : 0;
    if (code_type == 0) {
        $('#qrcode').qrcode({
            text: code_url,
            width: 230,
            height: 230,
            foreground: "#000000",
            background: "#ffffff",
            typeNumber: -1
        });
    } else {
        $('#qrcode').html('<img src="' + code_url + '"/>');
    }

    var i = 0;

    function loadmsg() {
        i++;
        $('.shanxinzha p').text(i);
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "/getshop.php",
            data: {type: "wxpay", trade_no: "<?php echo $order['trade_no']?>"},
            success: function (data) {
                if (data.code === 1) {
                    clearInterval(check);
                    layer.msg('支付成功，正在跳转中...', {icon: 16, shade: 0.1, time: 15000});
                    window.location.href = data.backurl;
                } else {
                    setTimeout("loadmsg()", 2000);
                }
            },
            error: function () {
                setTimeout("loadmsg()", 2000);
            }
        });
    }

    window.onload = function () {
        setTimeout("loadmsg()", 2000);
    }
</script>
</body>
</html>