<?php

use yii\db\Migration;

/**
 * Class m180830_023626_create_admin
 */
class m180830_023626_create_admin_user extends Migration
{
    private $table = 'db_admin_user';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->comment('用户名'),
            'mobile' => $this->string(20)->notNull()->comment('手机号码'),
            'pwd_salt' => $this->string(50)->notNull()->comment('密码salt'),
            'password' => $this->string(20)->notNull()->comment('密码'),
            'status' => $this->tinyInteger(1)->defaultValue(1)->notNull()->comment('状态'),
            'allow_city' => $this->text()->notNull()->comment('店铺名'),
            'allow_ip' => $this->text()->notNull()->comment('店铺名'),
            'create_time' => $this->integer(11)->comment('添加时间'),
            'last_login_time' => $this->integer(11)->comment('上次登录时间'),
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
        echo "m180830_023626_create_admin cannot be reverted.\n";

        return false;
    }
    */
}
