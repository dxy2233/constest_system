<?php

namespace common\models\business;

use common\traits\SmsType;

class BSmsTemplate extends \common\models\SmsTemplate
{
    // 下面一个 trait 记录所有发送短信类型ID
    use SmsType;

    public static $STATUS_UNUSED = 0;

    public static $STATUS_USED = 1;

    /**
     * 根据参数和模板，组装成最后的文字
     *
     * @param $type
     * @param $params
     * @return string
     */
    public static function assembleContent($type, array $params)
    {
        $template = self::findOne(['type' => $type, 'status' => self::$STATUS_USED]);
        assert($template != null);

        $content = $template->content;
        foreach ($params as $key => $value) {
            $key = $key == "vcode" ? "code" : $key;
            $content = str_replace('${'.$key.'}', $value, $content);
        }

        return $content;
    }


    /**
    * 自定义 label
    * @return array
    */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [

        ]);
    }
}
