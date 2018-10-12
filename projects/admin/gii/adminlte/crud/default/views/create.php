
<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model <?php echo ltrim($generator->modelClass, '\\') ?> */

$this->title = <?php echo $generator->generateString('Create {modelClass}', ['modelClass' => Inflector::camel2words(StringHelper::basename($generator->modelClass))]) ?>;
$this->params['breadcrumbs'][] = ['label' => <?php echo $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-info">
    <div class="box-body">
        <div class="<?php echo Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-create">
            <div class="box-header">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="page-header">
                            <i class="fa fa-info text-danger"></i>


                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <div class="pull-right box-tools" >
                    <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="刷新" onclick="javascript:location.reload() ;" >
                        <i class="fa fa-repeat"></i></button>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="返回" onclick="javascript :history.back(-1);">
                        <i class="fa fa-backward"></i></button>
                </div>
            </div>
            <?php echo "<?php echo " ?>$this->render('_form', [
                'model' => $model,
            ]) ?>

        </div>
    </div>
</div>