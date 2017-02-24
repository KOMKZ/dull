<?php
echo \yii2mod\alert\Alert::widget([
    'useSessionFlash' => false,
    'options' => [
        'timer' => null,
        'type' => \yii2mod\alert\Alert::TYPE_INPUT,
        'title' => 'An input!',
        'text' => "Write something interesting",
        'confirmButtonText' => "Yes, delete it!",
        'closeOnConfirm' => false,
        'showCancelButton' => true,
        'animation' => "slide-from-top",
        'inputPlaceholder' => "Write something"
    ],
    'callback' => new \yii\web\JsExpression(' function(inputValue) {
                if (inputValue === false) return false;
                if (inputValue === "") {
                    swal.showInputError("You need to write something!");
                    return false;
                }
                swal("Nice!", "You wrote: " + inputValue, "success");
    }')
]);
?>
<button id="btn" type="button" class="btn btn-default" name="button">demo</button>
