<?php
/**
 *
 *  本功能在于实现复杂筛选器
 *  如需要添加新的select 模块添加对应的现实fun
 *  name id class 等数据的配置 添加对应config 或widget 覆盖
 */
namespace admin\widgets;

use admin\assets\DepDropAsset;
use yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use yii\widgets\Pjax;

class UnionSelectWidget extends InputWidget {

        //模块化Select数据
        public $blockData = ['--选择统计地域--','按照区域统计','按照省份统计','按照城市统计'];

        public $unionData;

        public $selected = 0;

        public $model='advanced';

        //是否支持多选
        public $multiple = true;

        //是否使用无刷新
        public $pjax = true;

        public $pjaxUrl = '';

        public $pjaxButtonId = 'pjaxButton';

        public $dateSelect = true;
        //省会数据
        public $provinceData;

        //主模块数据
        public $areaData;

        public $parameter = 'union_select';

        //配置型
        public $config = [
            'block'     =>  ['name'=>'union_block','id'=>'union_block','class'=>'col-xs-2','slected'=>1] , //模块ID
            'area'      =>  ['name'=>'union_area','id'=>'union_area','class'=>'col-xs-2','selected'=>1],           //区域ID
            'province'  =>  ['name'=>'union_province','id'=>'union_province','class'=>'col-xs-2','selected'=>1],    //省会ID
            'city'  =>      ['name'=>'union_city','id'=>'union_city','class'=>'col-xs-2','selected'=>1],   //城市ID
            'date'  =>      ['name'=>'union_dateSelect','id'=>'union_dateSelect','class'=>'col-xs-3','selected'=>1]   //日期选择器
        ];



        public function init()
        {


            if(is_array($this->unionData)){
                $this->blockData = ArrayHelper::getColumn($this->unionData,'label');
            }

            $this->selected = \Yii::$app->request->get($this->parameter) ?? $this->selected;

        }


        public function run()
        {
            if($this->model == 'advanced'){


            $block = SelectWidget::widget([
            'name' => 'country',
            'data'=>$this->blockData,
            'options'=>['id'=>$this->config['block']['id']],]);

            //主模块select
            echo $blockHtml = Html::tag('div',$block,['class'=>'col-xs-2']);

            $view = $this->getView();
            $view->registerJs($this->otherString()['script']);
            //因为是无刷所以这里的js一定是要在加载页面的时候就要注入
            DepDropAsset::register($view);

            //无刷新替换的内容块
            Pjax::begin();
            echo $pjaxButtonGroup = $this->otherString()['pjaxButton'];
            if(is_array($this->unionData)){
                foreach($this->unionData as $key=>$block){
                    if($this->selected == $key){
                        $directory=new UnionSelectWidget();
                        if(isset($block['fun'])){
                            if(method_exists($directory,$block['fun'])){
                                $fun = $block['fun'];
                               echo $this->$fun();
                            }
                        }
                    }
                }
            }
            Pjax::end();
            //日期格式

            }
            echo $blockHtml = Html::tag('div',$this->getDateType(),['class'=>'col-xs-2']);
            echo $this->getDateSelect();

     }


        //省市联动
        public function getCity(){
            //省会
            $str = Html::tag('div',\admin\widgets\SelectWidget::widget([
                'name'    => $this->config['province']['name'],
                'data'    => $this->provinceData,
                'options' => ['id' => $this->config['province']['id']],
            ]),['class'=>$this->config['city']['class']]);
            //城市
           $str .= Html::tag('div',\admin\widgets\DepDropWidget::widget([
                'name'          => $this->config['city']['name'],
                'type'          => \admin\widgets\DepDropWidget::TYPE_SELECT2,
                'options'       => ['id' => $this->config['city']['id'],'multiple'=>true],
                'pluginOptions' => array_merge([
                    'depends'                 => [$this->config['province']['id']],
                    'placeholder'             => 'select city',
                    'url'                     => \yii\helpers\Url::to(['/widget/get-cities']),
                ],
                [
                    'initDepends' => [$this->config['province']['id']],
                    'initialize' => true
                ]
                )
            ]),['class'=>$this->config['city']['class']]);
            return $str;
        }

        //区域
        public function getArea(){
            return Html::tag('div',\admin\widgets\SelectWidget::widget([
                'value' => $this->config['area']['selected'],
                'name'    => $this->config['area']['name'],
                'data'    => $this->areaData,
                'value' =>Yii::$app->request->get($this->config['area']['name']),
                'options' => ['id' => $this->config['area']['id'],'multiple' => false],
                'pluginOptions' => [
                    'tags' => true,
                    'multiple'=>false,

                ],
            ]),['class'=>$this->config['area']['class']]);
        }

        //省会
        public function getProvince(){
            return Html::tag('div',\admin\widgets\SelectWidget::widget([
                'name'    => $this->config['province']['name'],
                'value' => $this->config['province']['selected'],
                'data'    => $this->provinceData,
                'maintainOrder' => true,
                'options' => ['id' => $this->config['province']['id']],
                'pluginOptions' => [
                    'tags' => true,

                ],
            ]),['class'=>$this->config['province']['class']]);
        }

        public function getDateType(){
            return Html::dropDownList('dateType',\Yii::$app->request->get('dateType')??'month',[
                'day'=>'按天查询',
                'month'=>'按月查询',
                'year'=>'按年查询',
            ],['class'=>'form-control',]);
        }





        //日期筛选器
        public function getDateSelect(){
            $addon = <<< HTML
                    <span class="input-group-addon">
                        <i class="glyphicon glyphicon-calendar"></i>
                    </span>
HTML;
            return Html::tag('div',Html::tag('div',\admin\widgets\DateRangePicker::widget([
                    'name'=>'kvdate3',
                    'id'=>$this->config['date']['id'],
                    'presetDropdown'=>true,
                    'value' => \Yii::$app->formatter->asDate(strtotime("-1 month")). ' - '. \Yii::$app->formatter->asDate(time()),
                    'useWithAddon'=>true,
                    'convertFormat'=>true,
                    'startAttribute' => 'from_date',
                    'endAttribute' => 'to_date',
                    'pluginOptions'=>[
                        'locale'=>['format' => 'Y-m-d'],
                    ]
                ]) . $addon,['class'=>'input-group drp-containergit']),['class'=>$this->config['date']['class']]);

        }


        //pjax 触发URL
        public function otherString(){
            //触发事件按钮
            $pjaxButton = '';
            $script = " $('#{$this->config['block']['id']}').change(function() {";

            for ($i=0;$i<count($this->blockData);$i++ ){
                $id = $this->pjaxButtonId.$i;
                $pjaxButton .=  Html::a('',[$this->pjaxUrl,$this->parameter=>$i],['class'=>'','id'=>$id]);
                $script .= "if($(this).val() == {$i} ){ $('#{$id}').click();}";
            }

            $script .= "});";

            return ['pjaxButton'=>$pjaxButton,'script'=>$script];

        }




}