<?php
namespace alipay;

use yii\base\Object;

/**
 *
 */
class RefundNotifyResponse extends Object{
    public $notify_time;
    public $notify_type;
    public $notify_id;
    public $sign_type;
    public $sign;
    public $batch_no;
    public $success_num;
    public $result_details;
}
