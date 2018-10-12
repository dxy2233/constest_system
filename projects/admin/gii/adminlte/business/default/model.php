<?php

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;



class <?= $className ?> extends <?= '\\' . ltrim($generator->modelClass, '\\') . "\n" ?> {





    /**
    * 自定义 label
    * @return array
    */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[

        ]);
    }
}
