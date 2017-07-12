<?php

use yii\db\Migration;

class m170712_072345_goods_sku extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%goods_sku}}', [
            'gsku_sku_id' => $this->primaryKey(), //主键
            'gsku_gid' => $this->integer()->notNull(),
            'gsku_name' => $this->string(255)->null(),
            'gsku_sku_value' => $this->string(64)->notNull(),
            'gsku_weight' => $this->float()->null(),
            'gsku_weight_type' => $this->char(10)->null(),
            'gsku_height' => $this->integer()->defaultValue(0)->null(),
            'gsku_width' => $this->integer()->defaultValue(0)->null(),
            'gsku_length' => $this->integer()->defaultValue(0)->null(),
            'gsku_price' => $this->integer()->notNull(),
            'gsku_status' => $this->char(15)->notNull(),
            'gsku_created_time' => $this->integer()->notNull(),
            'gsku_updated_time' => $this->integer()->notNull()
        ], $tableOptions);
        // todo 建立索引
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%goods_sku}}');
        return true;
    }
}
