<?php
namespace common\models\notify;

use Yii;
use common\base\Model;
use yii\helpers\ArrayHelper;
use common\models\notify\tables\SysMsg;

/**
 *
 */
class NotifyModel extends Model
{
    public static function getMTplTypeMap($noContent = false){
        $map = require(dirname(__FILE__) . '/' . 'm-tpl-type-map.php');
        if(!$noContent){
            return $map;
        }else{
            return ArrayHelper::map($map, 'value', 'label');
        }

    }
    public function createMsg($data){
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $sysMsg = new SysMsg();
            $sysMsg->scenario = 'create';
            if(!$sysMsg->load($data, '') || !$sysMsg->validate()){
                $this->addError('', $this->getArErrMsg($sysMsg));
                return false;
            }
            // build title and content
            if($sysMsg->use_tpl){
                $tplMap = self::getMTplTypeMap();
                $tplData = $tplMap[$sysMsg->tpl_type];
                console($tplData);
            }


        } catch (\Exception $e) {
            Yii::error($e);
            $transaction->rollback();
            $this->addError('', $e->getMessage());
            return false;
        }

    }
}
