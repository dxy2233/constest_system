<?php

use yii\db\Migration;

/**
 * Class m180827_093042_create_users
 */
class m180827_093042_create_records extends Migration
{
    private $table = 'db_records';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->comment('用户名'),
            'name_pin' => $this->string(50)->notNull()->comment('姓名拼音'),
            'mobile' => $this->string(20)->notNull()->comment('手机号码'),
            'urgent_name' => $this->string(50)->notNull()->comment('紧急联系人'),
            'urgent_tel' => $this->string(20)->notNull()->comment('紧急联系人方式'),
            'sex' => $this->tinyInteger(1)->defaultValue(1)->notNull()->comment('性别'),
            'idcard' => $this->string(18)->notNull()->comment('身份证号'),
            'shop_name' => $this->string(255)->notNull()->comment('店铺名'),
            'my_identity' => $this->integer(11)->notNull()->comment('职位'),
            'is_passport' => $this->tinyInteger(1)->defaultValue(1)->comment('有无护照'),
            'photo' => $this->text()->comment('身份证正反面'),
            'created_at' => $this->integer(11)->comment('添加时间'),
            'updated_at' => $this->integer(11)->comment('更新时间'),
            'KEY `name` (`name`) USING BTREE',
            'KEY `mobile` (`mobile`) USING BTREE',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->table);
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180827_093042_create_users cannot be reverted.\n";

        return false;
    }
    */
}
