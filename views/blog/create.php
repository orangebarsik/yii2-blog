<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model orangebarsik\blog\models\Blog */

$this->title = 'Create Blog';
$this->params['breadcrumbs'][] = ['label' => 'Blogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blog-create">

    <?php /* <h1><?= Html::encode($this->title) ?></h1> */ ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
