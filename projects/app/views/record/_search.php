<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\Models\RecordSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="Record-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'name_pin') ?>

    <?= $form->field($model, 'mobile') ?>

    <?= $form->field($model, 'urgent_name') ?>

    <?php // echo $form->field($model, 'urgent_tel')?>

    <?php // echo $form->field($model, 'sex')?>

    <?php // echo $form->field($model, 'idcard')?>

    <?php // echo $form->field($model, 'created_at')?>

    <?php // echo $form->field($model, 'updated_at')?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>