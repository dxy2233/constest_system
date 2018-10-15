<?php
/**
 * Created by PhpStorm.
 * User: havoe
 * Date: 2017/11/13
 * Time: 下午4:14
 */

namespace common\dzbase;

use yii\behaviors\TimestampBehavior;

class DzModel extends \yii\db\ActiveRecord
{

    // 设置基础状态 可全局使用
    // 不启用/停用/关闭
    const STATUS_INACTIVE = 0;
    // 启用/启用/开启
    const STATUS_ACTIVE = 1;
    
    public function behaviors()
    {
        $attributes = [];
        if ($this->hasAttribute('update_time')) {
            # 创建之前
            $attributes[self::EVENT_BEFORE_INSERT] = ['update_time'];
            # 修改之前
            $attributes[self::EVENT_BEFORE_UPDATE] = ['update_time'];
        }

        if ($this->hasAttribute('create_time')) {
            # 创建之前
            if (array_key_exists(self::EVENT_BEFORE_INSERT, $attributes)) {
                array_push($attributes[self::EVENT_BEFORE_INSERT], 'create_time');
            } else {
                $attributes[self::EVENT_BEFORE_INSERT] = ['create_time'];
            }
        }
        return [
            [
                // 自动添加时间
                'class' => TimestampBehavior::className(),
                'attributes' => $attributes,
                #设置默认值
                'value' => NOW_TIME
            ]
        ];
    }
    /**
     * 返回model 验证错误信息
     * @return array
     */
    public function getUniqueErrors()
    {
        $errors = $this->getErrors();
        $newErrors = [];
        foreach ($errors as $propName => $propErrors) {
            $newErrors[$propName] = $propErrors[0];
        }

        return $newErrors;
    }

    /**
     * 获取第一个错误信息
     * @return string
     */
    public function getFirstErrorText()
    {
        $errors = $this->getErrors();
        if (!$errors) {
            return '';
        }

        return array_values($errors)[0][0];
    }

    
    /**
     * 自定义查询规则
     * 公用定义查询类
     * @return void
     */
    public static function find()
    {
        return new DzQuery(get_called_class());
    }
    
    /**
     * 输出时间时自动格式化
     *
     * @return void
     */
    public function getCreateTimeText()
    {
        // return \Yii::$app->formatter->asDatetime($this->create_time);
        return date('Y-m-d H:i:s', $this->create_time);
    }
    /**
     * 输出时间时自动格式化
     *
     * @return void
     */
    public function getUpdateTimeText()
    {
        // return \Yii::$app->formatter->asDatetime($this->create_time);
        return date('Y-m-d H:i:s', $this->update_time);
    }
}
