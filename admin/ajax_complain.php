<?php
include("../includes/common.php");
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$act=isset($_GET['act'])?daddslashes($_GET['act']):null;

if(!checkRefererHost())exit('{"code":403}');

@header('Content-Type: application/json; charset=UTF-8');

switch($act){
case 'list':
	$paytype = [];
	$paytypes = [];
	$rs = $DB->getAll("SELECT * FROM pre_type");
	foreach($rs as $row){
		$paytype[$row['id']] = $row['showname'];
		$paytypes[$row['id']] = $row['name'];
	}
	unset($rs);

	$sql=" 1=1";
	if(isset($_POST['uid']) && !empty($_POST['uid'])) {
		$uid = intval($_POST['uid']);
		$sql.=" AND A.`uid`='$uid'";
	}
	if(isset($_POST['paytype']) && !empty($_POST['paytype'])) {
		$paytypen = intval($_POST['paytype']);
		$sql.=" AND A.`paytype`='$paytypen'";
	}elseif(isset($_POST['channel']) && !empty($_POST['channel'])) {
		$channel = intval($_POST['channel']);
		$sql.=" AND A.`channel`='$channel'";
	}
	if(isset($_POST['dstatus']) && $_POST['dstatus']>-1) {
		$dstatus = intval($_POST['dstatus']);
		$sql.=" AND A.`status`={$dstatus}";
	}
	if(!empty($_POST['starttime']) || !empty($_POST['endtime'])){
		if(!empty($_POST['starttime'])){
			$starttime = daddslashes($_POST['starttime']);
			$sql.=" AND A.addtime>='{$starttime} 00:00:00'";
		}
		if(!empty($_POST['endtime'])){
			$endtime = daddslashes($_POST['endtime']);
			$sql.=" AND A.addtime<='{$endtime} 23:59:59'";
		}
	}
	if(isset($_POST['value']) && !empty($_POST['value'])) {
		if($_POST['column']=='title' || $_POST['column']=='content'){
			$sql.=" AND A.`{$_POST['column']}` like '%{$_POST['value']}%'";
		}else{
			$sql.=" AND A.`{$_POST['column']}`='{$_POST['value']}'";
		}
	}
	$offset = intval($_POST['offset']);
	$limit = intval($_POST['limit']);
	$total = $DB->getColumn("SELECT count(*) from pre_complain A WHERE{$sql}");
	$list = $DB->getAll("SELECT A.*,B.money,B.name ordername FROM pre_complain A left join pre_order B on A.trade_no=B.trade_no WHERE{$sql} order by A.addtime desc limit $offset,$limit");
	$list2 = [];
	$channelids = [];
	foreach($list as $row){
		$row['typename'] = $paytypes[$row['paytype']];
		$row['typeshowname'] = $paytype[$row['paytype']];
		if(!in_array($row['channel'], $channelids)) $channelids[] = $row['channel'];
		$list2[] = $row;
	}
	$_SESSION['complain_channels'] = $channelids;

	exit(json_encode(['total'=>$total, 'rows'=>$list2]));
break;

case 'getChannels':
	$plugins = ['alipay', 'alipaysl', 'alipayd', 'wxpayn', 'wxpaynp', 'huifu'];
	$orderby = 'id ASC';
	if(isset($_SESSION['complain_channels']) && count($_SESSION['complain_channels'])>0){
		$orderby = 'FIELD(id,'.implode(',',$_SESSION['complain_channels']).') desc,id ASC';
	}
	$list=$DB->getAll("SELECT id,name,plugin FROM pre_channel WHERE plugin IN ('".implode("','", $plugins)."') ORDER BY {$orderby}");
	$result = ['code'=>0,'msg'=>'succ','plugins'=>$plugins,'data'=>$list];
	exit(json_encode($result));
break;

case 'setNotifyUrl':
	$action = intval($_POST['action']);
	$channelid = intval($_POST['channel']);
	$channel=\lib\Channel::get($channelid);
	if(!$channel)exit('{"code":-1,"msg":"当前支付通道不存在！"}');
	$model = new \lib\Complain\Wxpay($channel);
	if($action == 1){
		$result = $model->setNotifyUrl();
	}else{
		$result = $model->delNotifyUrl();
	}
	exit(json_encode($result));
break;

case 'refreshNewList':
	$channelid = intval($_POST['channel']);
	$num = intval($_POST['num']);
	$source = isset($_POST['source'])?intval($_POST['source']):0;
	if($num < 10) $num = 10;
	$channel=\lib\Channel::get($channelid);
	if(!$channel)exit('{"code":-1,"msg":"当前支付通道不存在！"}');
	$channel['source'] = $source;
	$model = \lib\Complain\CommUtil::getModel($channel);
	if(!$model)exit('{"code":-1,"msg":"不支持该支付插件"}');
	$result = $model->refreshNewList($num);
	exit(json_encode($result));
break;

case 'uploadImage':
	if(!isset($_FILES['file']))exit('{"code":-1,"msg":"请选择图片"}');
	$channelid = intval($_POST['channel']);
	$source = isset($_POST['source'])?intval($_POST['source']):0;
	$channel=\lib\Channel::get($channelid);
	$channel['source'] = $source;
	$model = \lib\Complain\CommUtil::getModel($channel);
	$result = $model->uploadImage($_FILES['file']['tmp_name'], $_FILES['file']['name']);
	exit(json_encode($result));
break;

case 'feedbackSubmit':
	$id = intval($_POST['id']);
	$row = $DB->find('complain', 'thirdid,channel,source', ['id'=>$id]);
	if(!$row)exit('{"code":-1,"msg":"投诉记录不存在"}');
	$thirdid = $row['thirdid'];
	$code = $_POST['code'];
	$content = trim($_POST['content']);
	$images = $_POST['images'];
	if(empty($code) || empty($content) && $code !== '1')exit('{"code":-1,"msg":"必填项不能为空"}');
	$channelid = intval($row['channel']);
	$channel=\lib\Channel::get($channelid);
	$channel['source'] = $row['source'];
	$model = \lib\Complain\CommUtil::getModel($channel);
	$result = $model->feedbackSubmit($thirdid, $code, $content, $images);
	exit(json_encode($result));
break;

case 'replySubmit':
	$id = intval($_POST['id']);
	$row = $DB->find('complain', 'thirdid,channel,source,thirdmchid', ['id'=>$id]);
	if(!$row)exit('{"code":-1,"msg":"投诉记录不存在"}');
	$thirdid = $row['thirdid'];
	$content = trim($_POST['content']);
	$images = $_POST['images'];
	if(empty($content))exit('{"code":-1,"msg":"必填项不能为空"}');
	$channelid = intval($row['channel']);
	$channel=\lib\Channel::get($channelid);
	$channel['source'] = $row['source'];
	$channel['thirdmchid'] = $row['thirdmchid'];
	$model = \lib\Complain\CommUtil::getModel($channel);
	$result = $model->replySubmit($thirdid, $content, $images);
	exit(json_encode($result));
break;

case 'supplementSubmit':
	$id = intval($_POST['id']);
	$row = $DB->find('complain', 'thirdid,channel,source', ['id'=>$id]);
	if(!$row)exit('{"code":-1,"msg":"投诉记录不存在"}');
	$thirdid = $row['thirdid'];
	$content = trim($_POST['content']);
	$images = $_POST['images'];
	if(empty($content))exit('{"code":-1,"msg":"必填项不能为空"}');
	$channelid = intval($row['channel']);
	$channel=\lib\Channel::get($channelid);
	$channel['source'] = $row['source'];
	$model = \lib\Complain\CommUtil::getModel($channel);
	$result = $model->supplementSubmit($thirdid, $content, $images);
	exit(json_encode($result));
break;

case 'refundProgressSubmit':
	$id = intval($_POST['id']);
	$row = $DB->find('complain', 'thirdid,channel,source', ['id'=>$id]);
	if(!$row)exit('{"code":-1,"msg":"投诉记录不存在"}');
	$thirdid = $row['thirdid'];
	$code = $_POST['code'];
	$content = trim($_POST['content']);
	$remark = trim($_POST['remark']);
	$images = $_POST['images'];
	if(empty($thirdid) || empty($content) && $code === '0')exit('{"code":-1,"msg":"必填项不能为空"}');
	$channelid = intval($row['channel']);
	$channel=\lib\Channel::get($channelid);
	$channel['source'] = $row['source'];
	$model = \lib\Complain\CommUtil::getModel($channel);
	$result = $model->refundProgressSubmit($thirdid, $code, $content, $remark, $images);
	exit(json_encode($result));
break;

case 'complete':
	$id = intval($_POST['id']);
	$row = $DB->find('complain', 'thirdid,channel,source,thirdmchid', ['id'=>$id]);
	if(!$row)exit('{"code":-1,"msg":"投诉记录不存在"}');
	$thirdid = $row['thirdid'];
	$mchext = $_POST['mchext'];
	if(empty($thirdid))exit('{"code":-1,"msg":"必填项不能为空"}');
	$channelid = intval($row['channel']);
	$channel=\lib\Channel::get($channelid);
	$channel['source'] = $row['source'];
	$channel['thirdmchid'] = $row['thirdmchid'];
	$model = \lib\Complain\CommUtil::getModel($channel);
	$result = $model->complete($thirdid);
	exit(json_encode($result));
break;

case 'delComplain':
	$id=$_GET['id'];
	if($DB->exec("DELETE FROM pre_complain WHERE id='$id'")!==false)exit('{"code":0,"msg":"succ"}');
	else exit('{"code":-1,"msg":"删除失败['.$DB->error().']"}');
break;

default:
	exit('{"code":-4,"msg":"No Act"}');
break;
}