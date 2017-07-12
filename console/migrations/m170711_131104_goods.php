<?php

use yii\db\Migration;

class m170711_131104_goods extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%goods}}', [
            'goods_id' => $this->primaryKey(), //主键
            'goods_primary_name' => $this->string(255)->notNull(), // 商品的主名称
            'goods_secondary_name' => $this->string(255)->null(),  // 商品的副名称
            'goods_logic_id' => $this->integer()->notNull(), // 业务主键
            'goods_des_id' => $this->integer()->notNull(), // 详细描述的id
            'goods_intro' => $this->string(255)->notNull(), // 简介
            'goods_status' => $this->char(15)->notNull(), // 状态
            'goods_type' => $this->char(15)->notNull(), // 产品形态
            'goods_sell_type' => $this->char(15)->notNull(), // 产品销售类型
            'goods_creator' => $this->string(255)->notNull(), // 创建者
            'goods_updator' => $this->string(255)->notNull(), // 更新者，详细更新历史由动作表来维护
            'goods_created_time' => $this->integer()->notNull(), //创建时间
            'goods_updated_time' => $this->integer()->notNull(), // 更新时间
            'goods_online_begin' => $this->integer()->null(), // 上架开始时间
            'goods_online_end' => $this->integer()->null() // 下架结束时间
        ], $tableOptions);
        // todo 建立索引
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%goods}}');
        return true;
    }
}
