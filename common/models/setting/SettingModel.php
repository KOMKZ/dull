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

    }

    public function set($name, $value, $createOnEmpty = false){

    }

    public function all(){
        $result = Setting::find()->all();
        $result = ArrayHelper::index(ArrayHelper::toArray($result), 'set_name');
        return $result;
    }

    public function update($condition, $data = []){

    }

    public function create($def){
        $setting = new Setting();
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
