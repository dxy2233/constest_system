<?php
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model <?php echo ltrim($generator->modelClass, '\\') ?> */

$this->title = $model-><?php echo $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' => <?php echo $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-info">
    <div class="box-body">
        <div class="<?php echo Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">
    <div class="box-header">
    <p>
        <?php \yii\widgets\ActiveForm::begin() ?>
            <?php echo "<?php echo " ?>Html::a(<?php echo $generator->generateString('更新') ?>, ['update', <?php echo $urlParams ?>], ['class' => 'btn btn-primary btn-sm']) ?>
            <?php echo "<?php echo " ?>Html::a(<?php echo $generator->generateString('删除') ?>, ['delete', <?php echo $urlParams ?>], [
                'class' => 'btn btn-danger btn-sm',
                'data' => [
                    'confirm' => <?php echo $generator->generateString('确定删除当前数据?') ?>,
                    'method' => 'post',
                ],
            ]) ?>
        <?php \yii\widgets\ActiveForm::end(); ?>
    </p>


    <div class="pull-right box-tools" >
        <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="刷新" onclick="javascript:location.reload() ;" >
            <i class="fa fa-repeat"></i></button>
        <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="返回" onclick="javascript :history.back(-1);">
            <i class="fa fa-backward"></i></button>
    </div>
    </div>
    <div class="box-body">
    <?php echo "<?php echo " ?>DetailView::widget([
        'model' => $model,
        'attributes' => [
<?php
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        echo "            '" . $name . "',\n";
    }
} else {
    foreach ($generator->getTableSchema()->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
    }
}
?>
        ],
    ]) ?>
</div>
</div>
    </div>
</div>