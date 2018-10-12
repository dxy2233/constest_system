<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model <?php echo ltrim($generator->searchModelClass, '\\') ?> */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="<?php echo Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-search row">

    <?php echo "<?php " ?>$form = ActiveForm::begin([
            'options'=>[
                'class '=>'form-horizontal'
            ],
            'fieldConfig' => [
                'template' => "<div class=\"col-sm-12\">{input}</div>\n",
                'labelOptions' => ['class' => 'col-sm-3 control-label'],  //修改label的样式
            ],
            'action' => ['index'],
            'method' => 'get',
        ]); ?>

<?php
$count = 0;
foreach ($generator->getColumnNames() as $attribute) {
    if (++$count < 6) {
        echo "    <div class=\"col-sm-2\">\n";
        echo "      <?php echo " . $generator->generateActiveSearchField($attribute) . " ?>\n";
        echo "    </div>\n\n";
    } else {
        echo "    <div class=\"col-sm-2\">\n";
        echo "      <?php // echo " . $generator->generateActiveSearchField($attribute) . " ?>\n";
        echo "    </div>\n\n";
    }
}
?>
    <div class="col-sm-3">
    <div class="form-group">
        <?php echo "<?php echo " ?>Html::submitButton(<?php echo $generator->generateString('搜索') ?>, ['class' => 'btn btn-primary']) ?>
        <?php echo "<?php echo " ?>Html::a('重置',Url::canonical(), ['class' => 'btn btn-danger']) ?>
    </div>
    </div>
    <div class="col-xs-12">
        <h4 class="page-header"></h4>
    </div>
    <?php echo "<?php " ?>ActiveForm::end(); ?>

</div>
