<?php

use yii\db\Migration;

class m170712_092812_goods_attr extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%goods_attr}}', [
            'gatrr_id' => $this->primaryKey(), //主键
            'gattr_name' => $this->string(64)->notNull(),
            'gattr_gcls_id' => $this->integer()->notNull(),
            'gattr_created_time' => $this->integer()->notNull(),
        ], $tableOptions);
        // todo 建立索引
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%goods_attr}}');
        return true;
    }
}
