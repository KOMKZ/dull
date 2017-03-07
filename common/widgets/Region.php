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



    public function run(){
        $this->province['value'] = !empty($this->province['value']) ? $this->province['value'] : -1;
        $this->city['value'] = !empty($this->city['value']) ? $this->city['value'] : -1;
        $this->district['value'] = !empty($this->district['value']) ? $this->district['value'] : -1;
        $this->registerJs();
        $html[] = Html::DropdownList($this->province['name'], null, [], ArrayHelper::merge($this->province['options'], [
            'id' => $this->getProvinceId(),
        ]));
        $html[] = Html::DropdownList($this->city['name'], null, [],  ArrayHelper::merge($this->city['options'], [
            'id' => $this->getCityId(),
        ]));
        $html[] = Html::DropdownList($this->district['name'], null, [],  ArrayHelper::merge($this->district['options'], [
            'id' => $this->getDistrictId(),
        ]));
        return implode("\n", $html);
    }

    public function getProvinceId(){
        return trim($this->province['name'], '[]') . '_province';
    }
    public function getCityId(){
        return trim($this->city['name'], '[]') . '_city';
    }
    public function getDistrictId(){
        return trim($this->district['name'], '[]') . '_district';
    }

    public function registerJs(){
        $joinChar = strripos($this->url, '?') ? '&' : '?';
        $url = $this->url . $joinChar;
        $provinceId = $this->getProvinceId();
        $cityId = $this->getCityId();
        $districtId = $this->getDistrictId();

        $provinceDefault = Html::renderSelectOptions('', ['-1' => $this->province['options']['prompt']]);
        $cityDefault = Html::renderSelectOptions('', ['-1' => $this->city['options']['prompt']]);
        $districtDefault = Html::renderSelectOptions('', ['-1' => $this->district['options']['prompt']]);

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
        function value_init(){
            if(-1 != {$provinceValue}){
                $('#{$provinceId}').val('{$provinceValue}');
                get_data('{$provinceValue}', function(res){
                    fill_city(res.data);
                    if(-1 != {$cityValue}){
                        $('#{$cityId}').val('{$cityValue}');
                    }
                });
                get_data('{$cityValue}', function(res){
                    fill_district(res.data);
                    if(-1 != {$districtValue}){
                        $('#{$districtId}').val('{$districtValue}');
                    }
                });
            }


        }
        function get_data(pid, callback){
            $.get('{$url}parent_id=' + pid, function(res){
                if(res.code == 0){
                    callback(res);
                }
            })
        }
        get_data(0, function(res){
            fill_province(res.data);
            value_init();
        });
        $('#{$provinceId}').change(function(){
            get_data($(this).val(), function(res){
                fill_city(res.data);
                $('#{$cityId}').val('-1');
                $('#{$cityId}').change();
            });
        });
        $('#{$cityId}').change(function(){
            get_data($(this).val(), function(res){
                fill_district(res.data);
                $('#{$districtId}').val('-1');
            })
        });
JS;
        Yii::$app->view->registerJs($js);
    }
}
