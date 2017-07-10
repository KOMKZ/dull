<?php

use yii\db\Migration;

class m170707_072136_loop_img_container extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%loop_img_container}}', [
            'lic_id' => $this->primaryKey(), //主键
            'lic_plugin' => $this->smallinteger(3)->notNull(), //轮波图的插件
            'lic_params' => $this->text()->Null(), //轮播图的插件的参数
            'lic_des' => $this->string()->notNull(), // 轮播图容器介绍
            'lic_index' => $this->char(20)->notNull(), // 轮播图索引代号
            'lic_created_time' => $this->integer()->notNull() //创建时间
        ], $tableOptions);
        // todo 建立索引
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%loop_img_container}}');
        return true;
    }
}
