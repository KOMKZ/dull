<?php
use Yii;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\formatter\Formatter;
use branchonline\lightbox\Lightbox;
use common\assets\LityAsset;
LityAsset::register($this);

?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h4><?= Yii::t('app', '文件预览')?></h4>
            </div>
            <div class="box-body">
                <?php
                // todo 业务膨胀的时候在考虑封装
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
                }elseif($fileMeta->isVideo()){
                    echo \kato\VideojsWidget::widget([
                        'options' => [
                            'class' => 'video-js vjs-default-skin vjs-big-play-centered',
                            'poster' => 'http://video-js.zencoder.com/oceans-clip.png',
                            'controls' => true,
                            'preload' => 'auto',
                            'width' => '600',
                            'height' => '350',
                            'data-setup' => '{ "plugins" : { "resolutionSelector" : { "default_res" : "720" } } }',
                        ],
                        'tags' => [
                            'source' => [
                                ['src' => $fileUrl, 'type' => $model['f_mime_type']]
                            ],
                        ],
                        'multipleResolutions' => true,
                    ]);
                }else{
                    echo Html::a('查看', $fileUrl, ['target' => '_blank']);
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
                            'format' => ['map', $map['f_category']]
                        ],
                        'f_prefix',
                        'f_host',
                        [
                            'attribute' => 'f_valid_type',
                            'format' => ['map', $map['f_valid_type']]
                        ],
                        'f_hash',
                        [
                            'attribute' => 'ext_img_url',
                            'format' => ['image']
                        ],
                        'f_mime_type',
                        [
                            'attribute' => 'f_storage_type',
                            'format' => ['map', $map['f_storage_type']],
                        ],
                        [
                            'attribute' => 'f_acl_type',
                            'format' => ['map', $map['f_acl_type']],
                        ],
                        [
                            'attribute' => 'f_size',
                            'format' => ['shortSize']
                        ],
                        [
                            'attribute' => 'f_status',
                            'format' => ['map', $map['f_status']]
                        ],
                        [
                            'attribute' => 'f_created_at',
                            'format' => ['date', 'Y-MM-dd HH:mm:ss']
                        ],
                        [
                            'attribute' => 'f_updated_at',
                            'format' => ['date', 'Y-MM-dd HH:mm:ss']
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
