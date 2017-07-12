<?php

use yii\db\Migration;

class m170712_091536_goods_classification extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%goods_classification}}', [
            'gcls_id' => $this->primaryKey(), //主键
            'gcls_name' => $this->string(64)->notNull(),
            'gcls_pid' => $this->integer()->defaultValue(0)->notNull(),
            'gcls_page' => $this->string(255)->null(),
            'gcls_sort_value' => $this->integer()->defaultValue(0)->notNull(),
            'gcls_created_time' => $this->integer()->notNull(),
        ], $tableOptions);
        // todo 建立索引
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%goods_classification}}');
        return true;
    }
}
