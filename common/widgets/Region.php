<?php
namespace common\widgets;

use Yii;
use yii\base\InvalidParamException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class Region extends Widget
{
    public $url;
    public $province;
    public $city;
    public $district;



    public $name;
    public function run(){
        $this->province['value'] = !empty($this->province['value']) ? $this->province['value'] : -1;
        $this->city['value'] = !empty($this->city['value']) ? $this->city['value'] : -1;
        $this->district['value'] = !empty($this->district['value']) ? $this->district['value'] : -1;
        $this->registerJs();
        $html[] = Html::DropdownList($this->name, null, [], ArrayHelper::merge($this->province['options'], [
            'id' => $this->getProvinceId(),
        ]));
        $html[] = Html::DropdownList($this->name, null, [],  ArrayHelper::merge($this->city['options'], [
            'id' => $this->getCityId(),
        ]));
        $html[] = Html::DropdownList($this->name, null, [],  ArrayHelper::merge($this->district['options'], [
            'id' => $this->getDistrictId(),
        ]));
        return implode("\n", $html);
    }

    public function getProvinceId(){
        return trim($this->name, '[]') . '_province';
    }
    public function getCityId(){
        return trim($this->name, '[]') . '_city';
    }
    public function getDistrictId(){
        return trim($this->name, '[]') . '_district';
    }

    public function registerJs(){
        $joinChar = strripos($this->url, '?') ? '&' : '?';
        $url = $this->url . $joinChar;
        $provinceId = $this->getProvinceId();
        $cityId = $this->getCityId();
        $districtId = $this->getDistrictId();

        $provinceDefault = Html::renderSelectOptions('', ['' => $this->province['options']['prompt']]);
        $cityDefault = Html::renderSelectOptions('', ['' => $this->city['options']['prompt']]);
        $districtDefault = Html::renderSelectOptions('', ['' => $this->district['options']['prompt']]);

        $provinceValue = $this->province['value'];
        $cityValue = $this->city['value'];
        $districtValue = $this->district['value'];


        $js = <<<JS
        function fill_province(items){
            var html = '{$provinceDefault}';
            $.each(items, function(v, i){
                html += '<option value="'+v+'">'+i+'</option>';
            });
            return $('#{$provinceId}').html(html);
        }
        function fill_city(items){
            var html = '{$cityDefault}';
            $.each(items, function(v, i){
                html += '<option value="'+v+'">'+i+'</option>';
            });
            return $('#{$cityId}').html(html);
        }
        function fill_district(items){
            var html = '{$districtDefault}';
            $.each(items, function(v, i){
                html += '<option value="'+v+'">'+i+'</option>';
            });
            return $('#{$districtId}').html(html);
        }
        $.get('{$url}parent_id=0', function(res){
            if(res.code == 0){
                province = res.data;
                fill_province(province);
                if(-1 != {$provinceValue}){
                    $('#{$provinceId}').val('{$provinceValue}');
                    $('#{$provinceId}').change();
                }
            }
        })
        $('#{$provinceId}').change(function(){
            $.get('{$url}parent_id=' + $(this).val(), function(res){
                if(res.code == 0){
                    fill_city(res.data);
                    if(-1 != {$cityValue}){
                        $('#{$cityId}').val('{$cityValue}');
                        $('#{$cityId}').change();
                    }
                }
            })
        });
        $('#{$cityId}').change(function(){
            $.get('{$url}parent_id=' + $(this).val(), function(res){
                if(res.code == 0){
                    fill_district(res.data);
                    if(-1 != {$districtValue}){
                        $('#{$districtId}').val('{$districtValue}');
                        $('#{$districtId}').change();
                    }
                }
            })
        });
JS;
        Yii::$app->view->registerJs($js);
    }
}
