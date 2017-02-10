<?php
use yii\grid\GridView;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <?php
            if($provider){
                echo GridView::widget([
                    'dataProvider' => $provider,
                    'columns' => [
                        [ 'attribute' => 'emf_id', 'label' => '编号'],
                    ]
                ]);
            }else{
                echo "
                <div class='box-body'>没有数据</div>
                ";
            }
            ?>
        </div>
    </div>
</div>
