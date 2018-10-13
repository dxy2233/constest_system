<?php
/**
 * Created by PhpStorm.
 * User: havoe
 * Date: 2017/11/13
 * Time: 下午4:14
 */

namespace common\dzbase;

class DzModel extends \yii\db\ActiveRecord
{

    // 设置基础状态 可全局使用
    // 不启用/停用/关闭
    const STATUS_INACTIVE = 0;
    // 启用/启用/开启
    const STATUS_ACTIVE = 1;
    
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
}
