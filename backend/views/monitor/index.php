<?php
$js = <<<JS
// document.domain = "caibaojian.com";
function setIframeHeight(iframe) {
if (iframe) {
        var iframeWin = iframe.contentWindow || iframe.contentDocument.parentWindow;
        if (iframeWin.document.body) {
            iframe.height = iframeWin.document.documentElement.scrollHeight || iframeWin.document.body.scrollHeight;
        }
    }
};

window.onload = function () {
setIframeHeight(document.getElementById('external-frame'));
};
JS;
?>

<iframe src="http://localhost:15672" scrolling="no" style="width:100%;height:800px;border:none;"/>
