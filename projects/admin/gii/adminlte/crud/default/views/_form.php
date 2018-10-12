<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?php echo ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="<?php echo Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form form-horizontal">

    <?php echo "<?php " ?>$form = ActiveForm::begin([

    'options'=>[
    'class '=>'form-horizontal'
    ],
    'fieldConfig' => [
    'template' => "{label}\n<div class=\"col-sm-7\">{input}</div>\n<div class=\"col-sm-3\" >{error}</div>",
    'labelOptions' => ['class' => 'col-sm-2 control-label'],  //修改label的样式
    ],
    ]); ?>

<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
        echo "    <?php echo " . $generator->generateActiveField($attribute) . " ?>\n\n";
    }
} ?>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">


        <?php echo "<?php echo " ?>Html::submitButton($model->isNewRecord ? <?php echo $generator->generateString('增加') ?> : <?php echo $generator->generateString('修改') ?>, ['class' => $model->isNewRecord ? 'btn btn-success btn-sm' : 'btn btn-primary btn-sm']) ?>
    </div>
    </div>
    <?php echo "<?php " ?>ActiveForm::end(); ?>

</div>
