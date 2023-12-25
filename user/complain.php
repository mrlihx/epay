<?php
/**
 * 支付交易投诉记录
**/
include("../includes/common.php");
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$title='支付交易投诉记录';
include './head.php';

$type_select = '<option value="0">所有支付方式</option>';
$rs = $DB->getAll("SELECT * FROM pre_type WHERE status=1 ORDER BY id ASC");
foreach($rs as $row){
	$type_select .= '<option value="'.$row['id'].'">'.$row['showname'].'</option>';
}
unset($rs);
?>
<style>
.fixed-table-toolbar,.fixed-table-pagination{padding: 15px;}
td{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;max-width:330px;}
</style>
<div id="content" class="app-content" role="main">
    <div class="app-content-body ">

<div class="bg-light lter b-b wrapper-md hidden-print">
  <h1 class="m-n font-thin h3">支付交易投诉</h1>
</div>
<div class="wrapper-md control">
<?php if(isset($msg)){?>
<div class="alert alert-info">
	<?php echo $msg?>
</div>
<?php }?>
<?php if(!$conf['complain_open']) showmsg('未开启交易投诉处理功能');?>
	<div class="panel panel-default">
		<div class="panel-heading font-bold">
			支付交易投诉记录
		</div>
<form onsubmit="return searchSubmit()" method="GET" class="form-inline" id="searchToolbar">
  <div class="form-group">
    <label>搜索</label>
	<select name="type" class="form-control"><option value="1">关联订单号</option><option value="2">第三方投诉单号</option><option value="3">问题类型</option><option value="4">投诉原因</option><option value="5">投诉详情</option><option value="6">联系电话</option></select>
  </div>
  <div class="form-group">
    <input type="text" class="form-control" name="kw" placeholder="搜索内容">
  </div>
  <div class="form-group">
    <select name="paytype" class="form-control"><?php echo $type_select?></select>
  </div>
  <div class="form-group">
	<select name="dstatus" class="form-control"><option value="-1">全部状态</option><option value="0">待处理</option><option value="1">处理中</option><option value="2">处理完成</option></select>
  </div>
  <button type="submit" class="btn btn-primary">搜索</button>
  <a href="javascript:searchClear()" class="btn btn-default" title="刷新记录列表"><i class="fa fa-refresh"></i></a>
</form>

      <table id="listTable">
	  </table>
    </div>
  </div>
</div>
</div>

<?php include 'foot.php';?>
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
				title: '支付方式',
				formatter: function(value, row, index) {
					return row.typename ? '<img src="/assets/icon/'+row.typename+'.ico" width="16" onerror="this.style.display=\'none\'">'+row.typeshowname : '';
				}
			},
			{
				field: 'trade_no',
				title: '关联订单号<br/>商品名称',
				formatter: function(value, row, index) {
					return '<a href="./order.php?type=1&kw='+value+'">'+value+'</a><br/>'+row.ordername;
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
					return '<a href="complain_info.php?id='+row.id+'" class="btn btn-info btn-xs">查看详情</a>';
				}
			},
		],
	})
})
</script>