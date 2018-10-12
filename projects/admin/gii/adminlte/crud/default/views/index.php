<?php
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use <?php echo $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;

/* @var $this yii\web\View */
<?php echo !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?php echo $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-info">
    <div class="box-body">
        <div class="<?php echo Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">




    <div class="box-header">
        <div class=" row" >
            <div class="col-sm-12 margin-bottom">
                <?php echo "<?php echo " ?>Html::a(<?php echo $generator->generateString('新增 {modelClass}', ['modelClass' => Inflector::camel2words(StringHelper::basename($generator->modelClass))]) ?>, ['create'], ['class' => 'btn btn-success btn-sm']) ?>
            </div>
        </div>

        <div class="pull-right box-tools" >
            <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="刷新" onclick="javascript:location.reload() ;" >
                <i class="fa fa-repeat"></i></button>
            <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="返回" onclick="javascript :history.back(-1);">
                <i class="fa fa-backward"></i></button>
        </div>

        <?php if(!empty($generator->searchModelClass)): ?>
            <?php echo "    <?php "  ?>echo $this->render('_search', ['model' => $searchModel]); ?>
        <?php endif; ?>

    </div>

    <div class="box-body">
<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?php echo "<?php echo " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'grid-view table-responsive'],
        <?php echo  "'columns' => [\n"; ?>
            ['class' => 'yii\grid\SerialColumn'],

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (++$count < 6) {
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

<?php else: ?>
    <?php echo "<?php echo " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?php echo $nameAttribute ?>), ['view', <?php echo $urlParams ?>]);
        },
    ]) ?>
<?php endif; ?>
    </div>
</div>
    </div>
</div>