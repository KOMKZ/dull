<?php
use yii\widgets\DetailView;
use common\formatter\Formatter;

echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'emf_id',
        [
            'attribute' => 'emf_message',
            'format' => 'json'
        ],
        [
            'attribute' => 'emf_created_at',
            'format' => ['date', 'Y-MM-dd HH-mm-ss']
        ],
        [
            'attribute' => 'emf_data',
            'format' => 'json'
        ]
    ],
    'formatter' => [
        'class' => 'common\formatter\Formatter',
    ]
]);
?>
