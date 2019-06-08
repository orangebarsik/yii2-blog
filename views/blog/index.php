<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel orangebarsik\blog\models\BlogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Blogs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blog-index">

    <?php /* <h1><?= Html::encode($this->title) ?></h1> */?>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Blog', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			[
				'class' => 'yii\grid\ActionColumn', 
				'template' => '{view} {update} {delete} {check}',
				'buttons' => [
					'check' => function($url, $model, $key){
						return Html::a('<i class="fa fa-check" aria-hidden="true"></i>',$url);
					}
				],
				'visibleButtons' => [
					'check' => function($model, $key, $index){
						return $model->status_id === 1 ;
					}
				]
			],

            'id',
            'title',
            //'text:ntext',
            //'url:url',
			
			['attribute' => 'url', 'format' => 'raw', 'headerOptions' => ['class' => '234']],
			
            //'status_id',
			//'status_id:boolean',
			//['attribute' => 'status_id', 'filter' => [0 => 'off', 1 => 'on'], 'value' => function($model){ return $model->status_id == 1 ? 'on' : 'off';}],
			//['attribute' => 'status_id', 'filter' => \common\models\Blog::getStatusList(), 'value' => function($model){ return $model->getStatusName;}],
            ['attribute' => 'status_id', 'filter' => \orangebarsik\blog\models\Blog::STATUS_LIST, 'value' => 'statusName'],
            'sort',
            'smallImage:image',
            'date_create:datetime',
            'date_update:datetime',
            ['attribute' => 'tags', 'value' => 'tagsAsString'],


        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
