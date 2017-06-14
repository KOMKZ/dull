<?php

use yii\db\Migration;

class m170614_090957_action_log extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%action_log}}', [
            'al_id' => $this->primaryKey(), //主键
            'al_module' => $this->smallinteger(3)->notNull(), //模块id
            'al_action' => $this->char(20)->notNull(), //动作名称
            'al_uid' => $this->integer()->notNull(), //用户id
            'al_object_id' => $this->integer()->notNull(), //对象id
            'al_app_id' => $this->smallinteger(3)->notNull(), //APPid
            'al_ip' => $this->string(100)->null(), //IP
            'al_agent_info' => $this->text()->null(), //代理信息
            'al_data' => $this->text()->null(), //相关数据
            'al_created_time' => $this->integer()->notNull() //创建时间
        ], $tableOptions);
        // todo 建立索引
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%action_log}}');
        return true;
    }
}
