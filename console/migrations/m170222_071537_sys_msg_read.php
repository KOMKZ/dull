<?php

use yii\db\Migration;

class m170222_071537_sys_msg_read extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%sys_msg_read}}', [
            'smsr_id' => $this->primaryKey(),
            // 已经阅读的msgid
            'smsr_mid' => $this->integer()->notNull(),
            'smsr_uid' => $this->integer()->notNull(),
            'smsr_created_at' => $this->integer()->notNull(),
            'smsr_updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%sys_msg_read}}');
        return true;
    }
}
