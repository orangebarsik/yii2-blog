<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use vova07\imperavi\Widget;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\ImageManager;

/* @var $this yii\web\View */
/* @var $model orangebarsik\blog\models\Blog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="blog-form">

    <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>


    <div class="row">

    <?= $form->field($model, 'file', ['options' => ['class' => 'col-xs-6']])->widget(\kartik\file\FileInput::classname(), [
        'language' => 'ru',
        'options' => ['accept' => 'image/*'],
        'pluginOptions' => [
            'allowedFileExtensions' => ['jpg','jpeg', 'gif', 'png'],
            'showCaption' => false,
            'showRemove' => false,
            'showUpload' => false,
            'browseClass' => 'btn btn-primary btn-block',
            'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
            
            'browseLabel' =>  'Выбрать фото ...'
        ]

    ]); ?>

    <?= $form->field($model, 'title', ['options' => ['class' => 'col-xs-6']])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'url', ['options' => ['class' => 'col-xs-6']])->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'status_id', ['options' => ['class' => 'col-xs-6']])->dropDownList(\orangebarsik\blog\models\Blog::STATUS_LIST) ?>

    <?= $form->field($model, 'sort', ['options' => ['class' => 'col-xs-6']])->textInput() ?>

    <?= $form->field($model, 'new_tags', ['options' => ['class' => 'col-xs-6']])->widget(\kartik\select2\Select2::classname(), [
        'data' => ArrayHelper::Map(\orangebarsik\blog\models\Tag::find()->all(), 'name', 'name'),
        'language' => 'ru',
        'options' => ['placeholder' => 'Выберите теги ...', 'multiple' => true],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => true,
            'maximumInputLength' => 10
        ],
    ]); ?>

    </div>

    <?= $form->field($model, 'text')->widget(Widget::className(), [
        'settings' => [
            'lang' => 'ru',
            'minHeight' => 200,
            'formatting' => ['p', 'blockquite', 'h1', 'h2'],
            'imageUpload' => Url::to(['site/save-redactor-img', 'sub' => 'blog']),
            'plugins' => [
                'clips',
                'fullscreen',
            ],
            'clips' => [
                ['Lorem ipsum...', 'Lorem...'],
                ['red', '<span class="label-red">red</span>'],
                ['green', '<span class="label-green">green</span>'],
                ['blue', '<span class="label-blue">blue</span>'],
            ],
        ],
    ]);?>


    <?php /* $form->field($model, 'tags_array')->widget(\kartik\select2\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::Map(\common\models\Tag::find()->all(),'id', 'name'),
        'language' => 'ru',
        'options' => ['placeholder' => 'Выберите теги ...', 'multiple' => true],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => true,
            'maximumInputLength' => 10
        ],
    ]); */ ?>

    <?php /* <pre>print_r($model->imagesLinksData); </pre>*/ ?>

    <?= \kartik\file\FileInput::widget([
        'name' => 'ImageManager[attachment]',
        'language' => 'ru',
        'options'=>[
            'multiple'=>true,
            'accept' => 'image/*'
        ],
        'pluginOptions' => [
            'previewFileType' => 'any',
            'allowedFileExtensions' => ['jpg','jpeg', 'gif', 'png'],
            'deleteUrl' => Url::toRoute(['/blog/delete-image']),
            'initialPreview' => $model->imagesLinks,
            'initialPreviewAsData' => true,
            'overwriteInitial' => false,
            'initialPreviewConfig' => $model->imagesLinksData,
            'uploadUrl' => Url::to(['/site/save-img']),
            'uploadExtraData' => [
                'ImageManager[class]' => $model->formName(),
                'ImageManager[item_id]' => $model->id
            ],
            'browseLabel' =>  'Выбрать фото ...',
            'maxFileCount' => 12
        ],
        'pluginEvents' => [
            'filesorted' => new \yii\web\JsExpression('function(event, params){
                $.post("' . Url::toRoute(["/blog/sort-image", "id" => $model->id]) . '",{sort: params});
           }')
        ],
    ]);  ?>

    <br/> <br/>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>