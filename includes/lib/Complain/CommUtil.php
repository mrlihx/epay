<?php
namespace lib\Complain;

class CommUtil
{
    public static function getModel($channel){
        if($channel['plugin'] == 'alipay' || $channel['plugin'] == 'alipaysl' || $channel['plugin'] == 'alipayd'){
            if($channel['source'] == 1){
                return new AlipayRisk($channel);
            }else{
                return new Alipay($channel);
            }
        }elseif($channel['plugin'] == 'wxpayn' || $channel['plugin'] == 'wxpaynp'){
            return new Wxpay($channel);
        }elseif($channel['plugin'] == 'huifu' && $channel['type']==2){
            return new HuifuWxpay($channel);
        }
        return false;
    }

    public static function autoHandle($trade_no, $status){
        global $DB, $conf;

        //自动冻结订单
        if($conf['complain_freeze_order']==1){
            if($status < 2){ //冻结订单
                \lib\Order::freeze($trade_no);
            }elseif($status == 2){ //解冻订单
                \lib\Order::unfreeze($trade_no);
            }
        }

        //自动拉黑支付账号
        $order = $DB->find('order', 'buyer,realmoney,status', ['trade_no'=>$trade_no]);
        if($status < 2 && $conf['complain_auto_black'] == 1 && !empty($order['buyer'])){
            if(!$DB->getRow("select * from pre_blacklist where type=:type and content=:content limit 1", [':type'=>0, ':content'=>$order['buyer']])){
                $DB->insert('blacklist', ['type'=>0, 'content'=>$order['buyer'], 'addtime'=>'NOW()', 'remark'=>'投诉自动拉黑']);
            }
        }

        //自动退款
        if($status == 0 && $conf['complain_auto_refund'] == 1 && (empty($conf['complain_auto_refund_money']) || $conf['complain_auto_refund_money']>=$order['realmoney']) && ($order['status'] == 1 || $order['status'] == 3)){
            $params = ['trade_no'=>$trade_no, 'money'=>$order['realmoney'], 'key'=>md5($trade_no.SYS_KEY.$trade_no)];
            get_curl($conf['localurl'].'api.php?act=refundapi', http_build_query($params));
            //\lib\Order::refund($trade_no, $order['realmoney'], 1);
        }
    }
}