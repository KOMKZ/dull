<?php

use yii\db\Migration;

class m170220_075948_sys_msg extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%sys_msg}}', [
            'smsg_id' => $this->primaryKey(),
            // 系统消息类型
            'smsg_type' => $this->smallinteger()->notNull(),
            'smgs_created_uid' => $this->integer()->notNull(),
            'smsg_releate_type' => $this->smallinteger()->notNull(),
            'smsg_object_id' => $this->integer()->notNull(),
            'smsg_expired_at' => $this->integer()->notNull(),
            'smsg_created_at' => $this->integer()->notNull(),
            'smsg_updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%sys_msg}}');
        return true;
    }
}
