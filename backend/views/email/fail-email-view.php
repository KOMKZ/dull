<?php
use yii\widgets\DetailView;
use common\formatter\Formatter;

echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'emf_id',               // title attribute (in plain text)
        'emf_message',
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
