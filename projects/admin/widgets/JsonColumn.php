<?php

namespace admin\widgets;
use yii\grid\Column;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class JsonColumn extends DataColumn
{


    public $showKey = true;

    public $findKey = 'name';

    public $findValue = 'value';

    /**
     * 解析json 数据
     * @param mixed $model
     * @param mixed $key
     * @param int $index
     * @return string
     */

    public function renderDataCellContent($model, $key, $index){

        $content = $this->getDataCellValue($model, $key, $index);
        $content = json_decode($content,true);


            $html = '<ul class="nav nav-pills nav-stacked">';
            foreach ($content as $key=>$value){
                if(!$this->showKey) $key = '';
                if(strpos($value,'http://')){
                    $html .= Html::tag('li',$key.'<img src="'.$value.'" >');
                }else{
                    $html .= Html::tag('li','[ '. $key.']'.$value);
                }

            }
            $html .= '</ul>';

        return $html;


    }


//    /**
//     * 判断数组层级
//     * @param $arr
//     * @return int
//     */
//    function arrayDepth($arr)
//    {
//        if (!is_array($arr))
//            return 0;
//        #递归将所有值置NULL，消除影响如array("array(\n  ()")
//        array_walk_recursive($arr, function (&$val) {
//            $val = NULL;
//        });
//        $ma = array();
//        #从行首匹配[空白]至第一个左括号，多行m
//        if (!preg_match_all("'^\\(|^\\s+\\('m", print_r($arr, true), $ma))
//            return 0;
//        return count(array_unique($ma[0]));
//    }
}
