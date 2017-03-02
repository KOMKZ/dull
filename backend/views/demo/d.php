<?php
use lo\widgets\loading\JqueryLoadingAsset;

JqueryLoadingAsset::register($this);
$js = <<<JS
    $('#a').loading('toggle');
JS;
$this->registerJs($js);
?>
<div id='a' style="width:300px;height:300px;background:#ccc;">
    a
</div>
