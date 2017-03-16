<?php
namespace common\models\setting;

use common\base\Model;
use common\models\setting\tables\Setting;
use yii\validators\Validator;
use yii\helpers\ArrayHelper;


/**
 *
 */
class SettingModel extends Model
{
    public $useCache = true;

    public function get($name, $default = null){
        // 1. 只取出名称还有数值
        // 2. 最好根据fields来取，默认是上面两个项目
        // 3. 假设是用cache， cache分为组件cache 还有 参数cache

    }

    public function set($name, $value){
        // 1. update one value 的快捷方法
    }

    public function all(){
        $result = Setting::find()->all();
        $result = ArrayHelper::index(ArrayHelper::toArray($result), 'set_name');
        return $result;
    }

    public function classifyByModule($items){
        $result = [];
        $map = Setting::getValidConsts('set_module');
        foreach($items as $key => $item){
            $result[$item['set_module']]['childrens'][] = $item;
        }
        foreach($result as $key => $items){
            $result[$key]['label'] = $map[$key];
        }
        return $result;
    }

    public function update($condition, $data = []){


    }

    public function updateOneValue($condition, $value){
        // 设定修改场景
        // 1. 增加一个返回loadvalue
        // 2. 工厂
        $one = $this->getOne($condition);
        console($one);

    }

    public function updateAllSettings($data){
        if(!empty($data)){
            $errors = [];
            foreach($data as $name => $def){
                $result = $this->updateOneValue(['set_name' => $def['set_name']], $def['set_value']);
                if(!$result){
                    $error[$name] = $this->getOneError();
                }
            }
        }
        return true;
    }

    public function create($def){
        $setting = new Setting();
        $setting->scenario = 'create';
        if(!$setting->load($def, '') || !$setting->validate()){
            $this->addError('', $this->getArErrMsg($setting));
            return false;
        }

        if(!empty($def['set_validators']) && is_array($def['set_validators'])){
            $validators = $this->createValueValidators($def['set_validators'], $setting);
            if(false === $validators){
                return false;
            }
            foreach($validators as $validator){
                $validator->validateAttribute($setting, 'set_value');
                if($setting->hasErrors()){
                    $this->addError('', $this->getArErrMsg($setting));
                    return false;
                }
            }
            $setting->set_validators = json_encode($def['set_validators']);
            $setting->set_validators_params = '';
        }else{
            $setting->set_validators = '';
            $setting->set_validators_params = '';
        }

        if(!empty($def['set_widget_params']) && is_array($def['set_widget_params'])){
            $setting->set_widget_params = json_encode($def['set_widget_params']);
        }else{
            $setting->set_widget_params = '';
        }
        $result = $setting->insert(false);
        if(!$result){
            $this->addError('', '插入失败');
            return false;
        }
        return $setting;
    }

    public function getOne($condition){
        if(is_object($condition)){
            return $condition;
        }
        if($condition){
            return Setting::find()->where($condition)->one();
        }else{
            return null;
        }
    }

    public function remove($name){

    }

    protected function createValueValidators($rules, $model){
        $validators = [];
        foreach($rules as $rule){
            $validator = $this->createValueValidator($rule, $model);
            if(!$validator){
                return false;
            }
            $validators[] = $validator;
        }
        return $validators;
    }

    protected function createValueValidator($rule, $model){
        if ($rule instanceof Validator) {
            return $rule;
        } elseif (is_array($rule) && isset($rule[0])) { // attributes, validator type
            $validator = Validator::createValidator($rule[0], $model, ['set_value'], array_slice($rule, 1));
            return $validator;
        } else {
            $this->addError('', 'Invalid validation rule: a rule must specify both attribute names and validator type.');
            return false;
        }
    }


}
