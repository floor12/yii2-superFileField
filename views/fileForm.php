<?php
/**
 * Created by PhpStorm.
 * User: floor12
 * Date: 23.06.2016
 * Time: 14:02
 */

use floor12\superfilefield\CropperAsset;

CropperAsset::register($this);

$rand = md5(rand(0, 1000) . time());
?>

<script>
    var className = '<?= $class ?>';
    var csrfToken = '<?= Yii::$app->request->csrfToken; ?>';
    var csrfParam = '<?= Yii::$app->request->csrfParam; ?>';
</script>

<?= $this->render('cropper'); ?>

<div class="form-group">
    <label class="control-label"><?= $attributeName ?></label>

    <div class="btn btn-default btn-xs" id="file-field-add_<?= $rand ?>"><span
            class="glyphicon glyphicon-upload"></span> Добавить вложение
    </div>

    <div class='superfield-list' id='superFileField_<?= $rand ?>'></div>

    <div id="process_<?= $rand ?>" class="superfield-process">
        <div class="progress uploaderProgress" id="uploaderProgress_<?= $rand ?>">
            <div id="progressBar_<?= $rand ?>" class="progress-bar progress-bar-striped active progress-bar-success"
                 role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
</div>

<?php

$this->registerJs("

    var className = '{$class}';
    var csrfToken = '" . Yii::$app->request->csrfToken . "';
    var csrfParam = '" . Yii::$app->request->csrfParam . "';
    
    superfileIndex(className, '{$field}', {$id}, '#superFileField_{$rand}')

    new ss.SimpleUpload({
        button: $('#file-field-add_{$rand}'), // HTML element used as upload button
        url: '/superfilefield/create/', // URL of server-side upload handler
        name: 'file[]',
        noParams: true,
        multiple: true,
        multipart: true,
        responseType: 'json',
        data: {'_csrf': csrfToken, class: className, field: '{$field}'},
        onSubmit: function (filename, extension) {
            $('#process_{$rand}').show();
            this.setProgressBar($('#progressBar_{$rand}')); // designate as progress bar
        },
        onComplete: function (filename, response) {
            $('#process_{$rand}').hide();
            if (!response) {
                alert(filename + ' upload failed');
                return false;
            }
            $.each(response,function(key,value){
                        superfileRander(value,'#superFileField_{$rand}')

            });
        }
    });


", yii\web\View::POS_END, 'uploader_' . $field);


?>
