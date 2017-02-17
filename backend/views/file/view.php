<?php
use Yii;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\formatter\Formatter;
use branchonline\lightbox\Lightbox;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h4><?= Yii::t('app', '文件预览')?></h4>
            </div>
            <div class="box-body">
                <?php
                if($fileMeta->isImage()){
                    echo Lightbox::widget([
                        'files' => [
                            [
                                'thumb' => $fileUrl,
                                'thumbOptions' => [
                                    'style' => "width:100px;",
                                    'class' => 'img-response'
                                ],
                                'original' => $fileUrl,
                                'title' => $model['f_name'],
                            ],
                        ]
                    ]);
                }else{
                    echo Html::tag('p', '该文件暂时不支持预览');
                }
                ?>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h4><?= Yii::t('app', '文件信息')?></h4>
            </div>
            <div class="box-body">
                <?php
                echo DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'f_id',
                        'f_name',
                        'f_ext',
                        [
                            'attribute' => 'f_category',
                            'format' => ['map', $fileCategoryMap]
                        ],
                        'f_prefix',
                        'f_host',
                        'f_hash',
                        [
                            'attribute' => 'ext_img_url',
                            'format' => ['image']
                        ],
                        'f_mime_type',
                        [
                            'attribute' => 'f_storage_type',
                            'format' => ['map', $fileStorageTypeMap],
                        ],
                        [
                            'attribute' => 'f_acl_type',
                            'format' => ['map', $fileAclTypeMap],
                        ],
                        [
                            'attribute' => 'f_size',
                            'format' => ['shortSize']
                        ],
                        [
                            'attribute' => 'f_status',
                            'format' => ['map', $fileStatusMap]
                        ],
                        [
                            'attribute' => 'f_created_at',
                            'format' => ['date', 'Y-MM-dd HH-mm-ss']
                        ],
                        [
                            'attribute' => 'f_updated_at',
                            'format' => ['date', 'Y-MM-dd HH-mm-ss']
                        ],
                        [
                            'attribute' => 'f_meta_data',
                            'format' => ['json']
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
</div>
