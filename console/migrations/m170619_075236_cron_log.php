<?php

use yii\db\Migration;

class m170619_075236_cron_log extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%cron_log}}', [
            'cl_id' => $this->primaryKey(), //主键
            'cl_module' => $this->smallinteger(3)->notNull(), //模块id
            'cl_action' => $this->char(20)->notNull(), //动作名称
            'cl_data' => $this->text()->null(), //相关数据
            'cl_created_time' => $this->integer()->notNull() //创建时间
        ], $tableOptions);
        // todo 建立索引
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%cron_log}}');
        return true;
    }
}
