<?php
namespace common\widgets;

use yii\base\Object;
use Yii;
use yii\helpers\Html;
use common\assets\AdSelectAsset;

/**
 *
 */
class AdSelectWidget extends Object
{
    public $selections = [];
    public $selected = [];
    public $selectTitle = 'Items can be selected';
    public $selectedTitle = 'Items has be selected';
    public $renderItem = null;
    public $renderSelectedItem = null;
    public $uniqueKey = 'id';
    public $action = null;
    public function render(){
        $toolBar = Html::tag('div', $this->renderToolBar(), ['class' => 'col-md-12']);
        $selectedBox = Html::tag('div', $this->renderSelectedBox(), ['class' => 'col-md-6']);
        $selectionsBox = Html::tag('div', $this->renderSelectionsBox(), ['class' => 'col-md-6']);
        $form = Html::tag('form', $toolBar . $selectionsBox . $selectedBox, [
            'action' => $this->action,
            'method' => 'post',
            'id' => '#ad-select-form'
        ]);
        return $form;
    }
    public function registerScript(){
        AdSelectAsset::register(Yii::$app->view);
    }
    public function renderSelectionsBox(){
        $items = $this->getSelectionItems();
        return $this->getPanel($this->selectTitle, '', $items);
    }
    public function renderSelectedBox(){
        $items = $this->getSelectedItems();
        return $this->getPanel($this->selectedTitle, '', $items);
    }
    protected function getSelectionItems(){
        $trs = '<tr class="ad-item"><td><input id="ad-select-check-all" type="checkbox"></td><td></td><td></td></tr>';
        foreach($this->selections as $index => $item){
            $tds = Html::tag('td', Html::input('checkbox'));
            $tds .= call_user_func_array($this->renderItem, [$index, $item]);
            $pkAttr = $this->getPkAttr();
            $trs .= Html::tag('tr', $tds, [
                'class' => 'ad-select-item ad-item',
                $pkAttr => $item[$this->uniqueKey]
            ]);
        }
        $itemsTable = Html::tag('table', $trs, ['class' => 'table table-strip', 'id' => 'ad-select-item-container']);
        return $itemsTable;
    }
    protected function getPkAttr(){
        return implode('-', ['data', $this->uniqueKey]);
    }
    protected function getSelectedItems(){
        $trs = '<tr class="ad-item"><td><input id="ad-selected-check-all" type="checkbox"></td><td></td><td></td></tr>';
        foreach($this->selected as $index => $item){
            $tds = Html::tag('td', Html::input('checkbox'));
            $tds .= call_user_func_array($this->renderSelectedItem, [$index, $item]);
            $pkAttr = $this->getPkAttr();
            $trs .= Html::tag('tr', $tds, [
                'class' => 'ad-selected-item ad-item',
                $pkAttr => $item[$this->uniqueKey]
            ]);
        }
        $itemsTable = Html::tag('table', $trs, ['class' => 'table table-strip', 'id' => 'ad-selected-item-container']);
        return $itemsTable;
    }
    public function renderToolBar(){
        $removeBtn = Html::button('Remove', ['class' => 'btn btn-default btn-sm', 'id' => 'ad-select-rm-btn']);
        $addBtn = Html::button('Add', ['class' => 'btn btn-default btn-sm', 'id' => 'ad-select-add-btn']);
        $saveBtn = Html::button('Save', ['class' => 'btn btn-default btn-sm', 'id' => 'ad-select-save-btn']);
        $wrapper = Html::tag('div', $removeBtn . $addBtn . $saveBtn);
        return $this->getPanel('ToolBar', $wrapper, '');
    }
    public function getPanel($title = '', $body = '', $others = ''){
        $title = Html::tag('div', $title, ['class' => 'panel-heading']);
        $body = $body ? Html::tag('div', $body, ['class' => 'panel-body']) : '';
        $panel = Html::tag('div', $title . $body . $others, ['class' => 'panel panel-default']);
        return $panel;
    }
}
