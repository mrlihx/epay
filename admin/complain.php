<?php
/**
 * 支付交易投诉记录
**/
include("../includes/common.php");
$title='支付交易投诉记录';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$type_select = '<option value="0">所有支付方式</option>';
$rs = $DB->getAll("SELECT * FROM pre_type ORDER BY id ASC");
foreach($rs as $row){
	$type_select .= '<option value="'.$row['id'].'">'.$row['showname'].'</option>';
}
unset($rs);
?>
<style>td{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;max-width:330px;}.list-group-item{word-break: break-all;}</style>
<div class="modal" id="modal-store" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content animated flipInX">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span
							aria-hidden="true">&times;</span><span
							class="sr-only">Close</span></button>
				<h4 class="modal-title" id="modal-title">获取最新投诉记录</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" id="form-store">
					<div class="form-group">
						<label class="col-sm-2 control-label">支付通道</label>
						<div class="col-sm-10">
							<select name="channel" id="channel" class="form-control" onchange="changeChannel()">
							</select>
							<font id="channel_tips" color="green"></font>
						</div>
					</div>
					<div class="form-group" id="form_source_alipay" style="display:none">
						<label class="col-sm-2 control-label">获取方式</label>
						<div class="col-sm-10">
							<select name="source" id="source" class="form-control" onchange="changeChannel()">
							<option value="0">旧版支付交易投诉处理</option><option value="1">新版RiskGO消费者投诉</option>
							</select>
						</div>
					</div>
					<hr/>
					<div class="form-group" id="form_getnewlist" style="display:none">
						<label class="col-sm-2 control-label">手动获取</label>
						<div class="col-sm-10">
						<div class="input-group"><div class="input-group-addon">获取最新</div>
				<input type="number" name="num" id="get_num" value="20" class="form-control" required placeholder="条数"/>
				<div class="input-group-addon">条投诉记录</div><div class="input-group-btn"><a href="javascript:refreshNewList()" class="btn btn-info">立即获取</a></div></div>
						</div>
					</div>
					<hr/>
					<div class="form-group" id="form_notify_alipay" style="display:none">
						<label class="col-sm-2 control-label">自动获取</label>
						<div class="col-sm-10">
							在支付宝开放平台应用订阅“交易投诉通知回调”，<br/>应用网关地址：<?php echo $siteurl?>pay/appgw/<span class="channelid">0</span>/<hr/><font color="green">注：只能获取到用户在【账单详情->对此订单有疑问->交易投诉】入口提交的投诉（大多数商户无此入口），无法获取支付宝举报中心提交的投诉，对应支付宝后台【小程序与代扣等投诉】列表。</font>
						</div>
					</div>
					<div class="form-group" id="form_notify_alipayrisk" style="display:none">
						<label class="col-sm-2 control-label">自动获取</label>
						<div class="col-sm-10">
							定时访问以下网址：<li class="list-group-item"><?php echo $siteurl?>cron.php?do=complain&channel=<span class="channelid">0</span>&key=<?php echo $conf['cronkey']; ?></li><br/><font color="green">注：需签约RiskGO，可获取支付宝举报中心提交的投诉，对应支付宝后台【支付交易投诉】列表。</font>
						</div>
					</div>
					<div class="form-group" id="form_notify_wxpay" style="display:none">
						<label class="col-sm-2 control-label">自动获取</label>
						<div class="col-sm-10">
						<a href="javascript:setnotifyurl(1)" class="btn btn-success btn-block">设置投诉通知回调地址</a><br/>
						<a href="javascript:setnotifyurl(0)" class="btn btn-warning btn-block">删除投诉通知回调地址</a><br/><font color="green">注：开启后微信商户的投诉将自动回调到本站，如本站更换域名，需要重新点击设置按钮。</font>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
			</div>
		</div>
	</div>
</div>
  <div class="container" style="padding-top:70px;">
  <div class="row">
    <div class="col-md-12 center-block" style="float: none;">
<form onsubmit="return searchSubmit()" method="GET" class="form-inline" id="searchToolbar">
  <div class="form-group">
    <label>搜索</label>
	<select name="column" class="form-control"><option value="trade_no">关联订单号</option><option value="thirdid">第三方投诉单号</option><option value="type">问题类型</option><option value="title">投诉原因</option><option value="content">投诉详情</option><option value="phone">联系电话</option></select>
  </div>
  <div class="form-group">
    <input type="text" class="form-control" name="value" placeholder="搜索内容">
  </div>
  <div class="form-group">
    <input type="text" class="form-control" name="uid" style="width: 100px;" placeholder="商户号" value="">
  </div>
  <div class="form-group">
    <select name="paytype" class="form-control"><?php echo $type_select?></select>
  </div>
  <div class="form-group">
    <input type="text" class="form-control" name="channel" style="width: 80px;" placeholder="通道ID" value="">
  </div>
  <div class="form-group">
	<select name="dstatus" class="form-control"><option value="-1">全部状态</option><option value="0">待处理</option><option value="1">处理中</option><option value="2">处理完成</option></select>
  </div>
  <button type="submit" class="btn btn-primary">搜索</button>
  <a href="javascript:searchClear()" class="btn btn-default" title="刷新记录列表"><i class="fa fa-refresh"></i></a>
  <a href="javascript:getdialog()" class="btn btn-success">获取最新数据</a>
</form>

      <table id="listTable">
	  </table>
    </div>
  </div>
</div>
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.min.js"></script>
<script src="<?php echo $cdnpublic?>bootstrap-table/1.20.2/bootstrap-table.min.js"></script>
<script src="<?php echo $cdnpublic?>bootstrap-table/1.20.2/extensions/page-jump-to/bootstrap-table-page-jump-to.min.js"></script>
<script src="../assets/js/custom.js"></script>
<script>
$(document).ready(function(){
	updateToolbar();
	const defaultPageSize = 10;
	const pageNumber = typeof window.$_GET['pageNumber'] != 'undefined' ? parseInt(window.$_GET['pageNumber']) : 1;
	const pageSize = typeof window.$_GET['pageSize'] != 'undefined' ? parseInt(window.$_GET['pageSize']) : defaultPageSize;

	$("#listTable").bootstrapTable({
		url: 'ajax_complain.php?act=list',
		pageNumber: pageNumber,
		pageSize: pageSize,
		classes: 'table table-striped table-hover table-bordered',
		columns: [
			{
				field: 'id',
				title: 'ID',
				formatter: function(value, row, index) {
					return '<b>'+value+'</b>';
				}
			},
			{
				field: 'uid',
				title: '商户号',
				formatter: function(value, row, index) {
					return '<b><a href="./ulist.php?column=uid&value='+value+'" target="_blank">'+value+'</a></b>';
				}
			},
			{
				field: 'uid',
				title: '通道ID',
				formatter: function(value, row, index) {
					return row.typename ? '<img src="/assets/icon/'+row.typename+'.ico" width="16" onerror="this.style.display=\'none\'"><a href="./pay_channel.php?kw='+row.channel+'&type=0&dstatus=-1" target="_blank">'+row.channel+'</a>' : '';
				}
			},
			{
				field: 'trade_no',
				title: '关联订单号<br/>商品名称',
				formatter: function(value, row, index) {
					return '<a href="./order.php?column=trade_no&value='+value+'" target="_blank">'+value+'</a><br/>'+row.ordername;
				}
			},
			{
				field: 'type',
				title: '问题类型<br/>订单金额',
				formatter: function(value, row, index) {
					return value+'<br/>￥'+row.money;
				}
			},
			{
				field: 'title',
				title: '投诉原因<br/>投诉详情',
				formatter: function(value, row, index) {
					return value+'<br/>'+row.content;
				}
			},
			{
				field: 'addtime',
				title: '创建时间<br/>最后更新时间',
				formatter: function(value, row, index) {
					return value+'<br/>'+row.edittime;
				}
			},
			{
				field: 'status',
				title: '状态',
				formatter: function(value, row, index) {
					if(value == '1'){
						return '<font color=orange>处理中</font>';
					}else if(value == '2'){
						return '<font color=green>处理完成</font>';
					}else{
						return '<font color=red>待处理</font>';
					}
				}
			},
			{
				field: 'status',
				title: '操作',
				formatter: function(value, row, index) {
					return '<a href="complain_info.php?id='+row.id+'" class="btn btn-info btn-xs">详情</a>&nbsp;<a href="javascript:delItem('+row.id+')" class="btn btn-danger btn-xs">删除</a>';
				}
			},
		],
	})
})
function delItem(id) {
	var confirmobj = layer.confirm('你确实要删除此记录吗？', {
	  btn: ['确定','取消'], icon:0
	}, function(){
		$.ajax({
			type : 'GET',
			url : 'ajax_complain.php?act=delComplain&id='+id,
			dataType : 'json',
			success : function(data) {
				if(data.code == 0){
					layer.closeAll();
					searchSubmit();
				}else{
					layer.alert(data.msg, {icon: 2});
				}
			},
			error:function(data){
				layer.msg('服务器错误');
				return false;
			}
		});
	}, function(){
		layer.close(confirmobj);
	});
}
function getdialog(){
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'GET',
		url : 'ajax_complain.php?act=getChannels',
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				$("#modal-store").modal('show');
				$("#channel").empty();
				$("#channel").append('<option value="0">请选择支付通道</option>');
				$.each(data.data, function(index, item){
					$("#channel").append('<option value="'+item.id+'" plugin="'+item.plugin+'">'+item.id+'__'+item.name+'</option>');
				});
				$("#channel_tips").text('只支持'+data.plugins.join('、')+'支付插件');
				changeChannel();
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
function changeChannel(){
	var channel = parseInt($("#channel").val());
	var source = parseInt($("#source").val());
	if(channel>0){
		$("#form_getnewlist").show();
		var plugin = $("#channel option:selected").attr('plugin');
		if(plugin.indexOf('wxpay')>-1){
			$("#form_notify_alipay").hide();
			$("#form_notify_alipayrisk").hide();
			$("#form_notify_wxpay").show();
			$("#form_source_alipay").hide();
		}
		else if(plugin.indexOf('alipay')>-1){
			$(".channelid").text(channel)
			if(source == 1){
				$("#form_notify_alipay").hide();
				$("#form_notify_alipayrisk").show();
			}else{
				$("#form_notify_alipay").show();
				$("#form_notify_alipayrisk").hide();
			}
			$("#form_notify_wxpay").hide();
			$("#form_source_alipay").show();
		}
		else{
			$("#form_notify_alipay").hide();
			$("#form_notify_alipayrisk").hide();
			$("#form_notify_wxpay").hide();
			$("#form_source_alipay").hide();
		}
	}else{
		$("#form_getnewlist").hide();
		$("#form_notify_alipay").hide();
		$("#form_notify_alipayrisk").hide();
		$("#form_notify_wxpay").hide();
		$("#form_source_alipay").hide();
	}
}
function refreshNewList(){
	var channel = parseInt($("#channel").val());
	if(channel == 0){
		layer.alert('请选择支付通道！');return false;
	}
	var source = parseInt($("#source").val());
	var num = parseInt($("#get_num").val());
	if(num < 10) num = 10;
	if(num > 200) {
		layer.alert('最多获取200条投诉记录');return false;
	}
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'POST',
		url : 'ajax_complain.php?act=refreshNewList',
		data : {channel:channel, num:num, source:source},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				layer.alert(data.msg,{
					icon: 1,
					closeBtn: false
				}, function(){
					layer.closeAll();
					$("#modal-store").modal('hide');
					searchSubmit();
				});
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
function setnotifyurl(action){
	var channel = parseInt($("#channel").val());
	if(channel == 0){
		layer.alert('请选择支付通道！');return false;
	}
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'POST',
		url : 'ajax_complain.php?act=setNotifyUrl',
		data : {channel:channel, action:action},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				layer.alert(action==1?'设置投诉通知回调地址成功！':'删除投诉通知回调地址成功！', {icon: 1})
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