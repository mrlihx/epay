<?php
include("../includes/common.php");
$title='USDT转账';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
  <div class="container" style="padding-top:70px;">
    <div class="col-xs-12 col-sm-10 col-lg-8 center-block" style="float: none;">
<?php

if(isset($_POST['submit'])){
	if(!checkRefererHost())exit();
	$out_biz_no = trim($_POST['out_biz_no']);
	if(!isset($_POST['paypwd']) || $_POST['paypwd']!==$conf['admin_paypwd'])showmsg('支付密码错误',3);
	$payee_account = htmlspecialchars(trim($_POST['payee_account']));
	$money = trim($_POST['money']);
	$desc = htmlspecialchars(trim($_POST['desc']));
	if(empty($out_biz_no) || empty($payee_account) || empty($money))showmsg('必填项不能为空',3);
	if(!is_numeric($money) || !preg_match('/^[0-9.]+$/', $money) || $money<=0)showmsg('转账金额输入不规范',3);

    $result = SendUSDT($payee_account, $money);
	if($result['orderid']!=""){
		$data = ['biz_no'=>$out_biz_no, 'uid'=>0, 'type'=>"usdt", 'channel'=>0, 'account'=>$payee_account, 'username'=>"USDT", 'money'=>$money, 'costmoney'=>$money, 'paytime'=>'NOW()', 'pay_order_no'=>$result['orderid'], 'status'=>1, 'desc'=>$desc];
		$DB->insert('transfer', $data);
        $result='转账成功！<br>交易Hash：'.$result['orderid'].' <br>支付时间:'.$result['paydate'];
		showmsg($result,1,'./transfer.php');
	}else{
        if ($result['msg']=="") $result['msg']="内部服务器错误。";
		$result='转账失败：'.$result['msg'];
		showmsg($result,4);
	}
}

$out_biz_no = date("YmdHis").rand(11111,99999);
?>

	  <div class="panel panel-primary">
        <div class="panel-heading"><h3 class="panel-title">USDT转账</h3></div>
        <div class="panel-body">
		<div class="tab-pane active" id="alipay">
          <form action="" method="POST" role="form">
			<div class="form-group">
				<div class="input-group"><div class="input-group-addon">交易号</div>
				<input type="text" name="out_biz_no" value="<?php echo $out_biz_no?>" class="form-control" required/>
			</div></div>
			<div class="form-group">
				<div class="input-group"><div class="input-group-addon">收款地址</div>
				<input type="text" name="payee_account" value="TENY7re3b4ELEbBYsrcnbuBtYQ6uysTJRN" class="form-control" required placeholder="TRC20地址"/>
			</div></div>
			<div class="form-group">
				<div class="input-group"><div class="input-group-addon">转账金额</div>
				<input type="text" name="money" value="" class="form-control" placeholder="USDT数量" required/>
			</div></div>
            <div class="form-group">
            <div class="input-group"><div class="input-group-addon">转账备注</div>
                <input type="text" name="desc" value="" class="form-control" placeholder="可留空"/>
            </div></div>
			<div class="form-group">
				<div class="input-group"><div class="input-group-addon">支付密码</div>
				<input type="text" name="paypwd" value="" class="form-control" required/>
			</div></div>
            <p><input type="submit" name="submit" value="立即转账" class="btn btn-primary form-control"/></p>
			<p><a href="javascript:balanceQuery()" class="btn btn-block btn-default">查询账户余额</a></p>
          </form>
        </div>
		</div>
		<div class="panel-footer">
          <span class="glyphicon glyphicon-info-sign"></span> 交易号可以防止重复转账，同一个交易号只能提交同一次转账。<br/>
		  <a href="./set.php?mod=account">修改支付密码</a>
        </div>
      </div>
    </div>
  </div>
<script src="<?php echo $cdnpublic?>jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.min.js"></script>
<script>
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
function balanceQuery(){
	var type = $("input[name=type]").val();
	var channel = $("select[name=channel]").val();
	if(channel == ''){
		layer.alert('请先选择通道');return;
	}
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'POST',
		url : 'ajax_transfer.php?act=usdt_balance_query',
		dataType : 'json',
		data : {},
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				layer.alert('Trx余额：'+data.trx+'<br>USDT余额：'+data.usdt);
			}else{
				layer.alert(data.msg, {icon: 2})
			}
		},
		error:function(data){
			layer.close(ii);
			layer.msg('服务器错误');
		}
	});
}
</script>