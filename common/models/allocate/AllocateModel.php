<?php
namespace common\models\allocate;

use common\base\Model;

/**
 *
 */
class AllocateModel extends Model
{
    /**
     * 申请交易单号字符串
     * @return [type] [description]
     */
    public static function applyTransNumber(){
        list($s, $m) = explode('.', microtime(true));
        return sprintf("T%s%'.04d%'.02d", $s, $m, mt_rand(11, 99));
    }
}
