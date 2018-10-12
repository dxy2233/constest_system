
<div class="col-sm-2">
    <?php echo \admin\widgets\SelectWidget::widget([
        'name'    => 'provinceId',
        'data'    => \yii\helpers\ArrayHelper::map(
            \common\models\ChinaZone::find()->asArray()->where(['level' => 1])->all(),
            'id', 'name'),
        'options' => ['id' => 'provinceId'],
    ]);
    ?>
</div>

<?php if ($this->context->showOnlyProvince == false){ ?>

<?php if ($this->context->showDistricts) {
    $initConfig = [];
}  else {
    $initConfig = [
        'initDepends' => ['provinceId'],
        'initialize' => true
    ];
}?>


<div class="col-sm-2">
    <?php
    echo \admin\widgets\DepDropWidget::widget([
        'name'          => 'cityId',
        'type'          => \admin\widgets\DepDropWidget::TYPE_SELECT2,
        'options'       => ['id' => 'cityId'],
        'pluginOptions' => array_merge([
            'depends'                 => ['provinceId'],
            'placeholder'             => 'select city',
            'url'                     => \yii\helpers\Url::to(['/widget/get-cities']),
        ], $initConfig)
    ]);
    ?>
</div>
<?php if ($this->context->showDistricts){ ?>
<div class="col-sm-2 border-right ">
    <?php
    echo \admin\widgets\DepDropWidget::widget([
        'name'          => 'districtId',
        'type'          => \admin\widgets\DepDropWidget::TYPE_SELECT2,
        'options'       => ['id' => 'districtId', 'placeholder' => '选择区'],
        'pluginOptions' => [
            'depends'                 => ['cityId'],
            'placeholder'             => '选择区',
            'url'                     => \yii\helpers\Url::to(['/widget/get-districts']),
            'initDepends' => ['provinceId'],
            'initialize' => true
        ]
    ]);
    ?>
</div>
<?php } }?>

