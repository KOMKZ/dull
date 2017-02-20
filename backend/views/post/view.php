<?php
use yii\widgets\DetailView;
use common\formatter\Formatter;


?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h5>文章基本信息</h5>
            </div>
            <div class="box-body">
                <?php
                echo DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'p_id',
                        'p_title',
                        'p_thumb_img_id',
                        [
                            'attribute' => 'p_thumb_img',
                            'format' => ['imageThumb']
                        ],
                        [
                            'attribute' => 'p_content_type',
                            'format' => ['map', $postContentTypeMap]
                        ],
                        [
                            'attribute' => 'p_status',
                            'format' => ['map', $postStatusMap]
                        ],
                        'p_content',
                        [
                            'attribute' => 'p_created_at',
                            'format' => ['date', 'Y-MM-dd HH:mm:ss']
                        ],
                        [
                            'attribute' => 'p_updated_at',
                            'format' => ['date', 'Y-MM-dd HH:mm:ss']
                        ],
                    ],
                    'formatter' => [
                        'class' => 'common\formatter\Formatter',
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h5>文章内容</h5>
            </div>
            <div class="box-body">

            </div>
        </div>
    </div>
</div>
