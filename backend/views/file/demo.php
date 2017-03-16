<?php
use common\assets\WUAsset;
WUAsset::register($this);
$fileUploadUrl = $url['file/wufileupload'];
$js = <<<JS

var g_file_hash = {};

WebUploader.Uploader.register({
    "before-send-file": "beforeSendFile",
    "before-send" : "beforeSend"
}, {
    beforeSendFile: function(file){
        g_file_hash = md5(file);
        console.log(g_file_hash);
    },
    beforeSend: function(block){
        //分片验证是否已传过，用于断点续传
        var task = new $.Deferred();
        $.ajax({
            type: "POST"
            , url: "{$fileUploadUrl}"
            , data: {
                type: "chunkCheck"
                , file: g_file_hash
                , chunkIndex: block.chunk
                , size: block.end - block.start
            }
            , cache: false
            , timeout: 1000 //todo 超时的话，只能认为该分片未上传过
            , dataType: "json"
        }).then(function(data, textStatus, jqXHR){
            task.reject();
        }, function(jqXHR, textStatus, errorThrown){    //任何形式的验证失败，都触发重新上传
            task.resolve();
        });

        return $.when(task);
    }
});

var uploader = WebUploader.create({
    // swf文件路径
    swf: '/static/webuploader/Uploader.swf',
    // 文件接收服务端。
    server: '{$fileUploadUrl}',
    chunked : true,
    chunkSize: 5000 * 1024,
    pick: '#picker',
});




uploader.on( 'fileQueued', function( file ) {
    $('#file-list').append( '<div id="' + file.id + '" class="item">' +
        '<h4 class="info">' + file.name + '</h4>' +
        '<p class="state">等待上传...</p>' +
    '</div>' );
});

uploader.on( 'uploadSuccess', function( file, response) {
    console.log(response);
});

// 文件上传失败，显示上传出错。
uploader.on( 'uploadError', function( file ) {
    console.log('u_error', file);
});

// 完成上传完了，成功或者失败，先删除进度条。
uploader.on( 'uploadComplete', function( file ) {

});

$('#send').click(function(){
    uploader.upload();
});

JS;

$this->registerJs($js);
?>
<div id="file-list">

</div>
<div id="picker">选取视频</div>
<div class="btn btn-default" id="send">上传</div>
