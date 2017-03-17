<?php
use common\assets\WUAsset;
WUAsset::register($this);
$fileUploadUrl = $url['file/save-chunked-file'];
$fileAskUrl = $url['file/ask-chunked-file'];
$js = <<<JS

var g_file_hash = '';



WebUploader.Uploader.register({
    "before-send-file": "beforeSendFile",
    "before-send" : "beforeSend"
}, {
    beforeSendFile: function(file){
        g_file_hash = md5(file);
        var task = new $.Deferred();
        $.ajax({
            type: "POST"
            , url: "{$fileAskUrl}"
            , data: {
                ask_type: "checkFileTotal"
                , md5: g_file_hash
                , size: file.size
            }
            , cache: false
            , timeout: 1000 //todo
            , dataType: "json"
        }).then(function(data, textStatus, jqXHR){
            // console.log(1);
            if(0 == data.code){
                // file had existed, skip upload 注意这里会触发error
                uploader.skipFile(file);
                task.reject();
            }else{
                task.resolve();
            }
        }, function(jqXHR, textStatus, errorThrown){    //任何形式的验证失败，都触发重新上传
            task.resolve();
        });
        return $.when(task);
    },
    beforeSend: function(block){
        //分片验证是否已传过，用于断点续传
        var task = new $.Deferred();
        $.ajax({
            type: "POST"
            , url: "{$fileAskUrl}"
            , data: {
                ask_type: "checkFileChunked"
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
    // swf: '/static/webuploader/Uploader.swf',
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
    console.log('u_success', response);
});

// 文件上传失败，显示上传出错。
uploader.on( 'uploadError', function( file ) {
    console.log('u_error', file);
});

// 完成上传完了，成功或者失败，先删除进度条。
uploader.on( 'uploadComplete', function( file ) {
    console.log('u_complete', file);
});

uploader.on( 'uploadProgress', function( file, percentage ) {
    var $li = $( '#'+file.id ),
        $percent = $li.find('.progress .progress-bar');

    // 避免重复创建
    if ( !$percent.length ) {
        $percent = $('<div class="progress progress-striped active">' +
          '<div class="progress-bar" role="progressbar" style="width: 0%">' +
          '</div>' +
        '</div>').appendTo( $li ).find('.progress-bar');
    }

    $li.find('p.state').text('上传中');

    $percent.css( 'width', percentage * 100 + '%' );
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
