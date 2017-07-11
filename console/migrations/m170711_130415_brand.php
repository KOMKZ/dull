<?php

use yii\db\Migration;

class m170711_130415_brand extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%brand}}', [
            'brand_id' => $this->primaryKey(), //主键
            'brand_name' => $this->string()->notNull(), // 品牌名称
            'brand_web_home' => $this->string(255)->null(), // 品牌网站首页
            'brand_intro' => $this->string(255)->null(), // 品牌简介
            'brand_long_intro' => $this->text()->null(), // 品牌详细介绍
            'brand_comment' => $this->string(255)->null(), // 品牌备注
            'brand_created_time' => $this->integer()->notNull() //创建时间
        ], $tableOptions);
        // todo 建立索引
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%brand}}');
        return true;
    }
}
