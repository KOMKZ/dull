<?php

use yii\db\Migration;

class m170222_120127_sys_msg extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%sys_msg}}', [
            'sm_id' => $this->primaryKey(),
            'sm_mid' => $this->integer()->notNull(),
            'sm_create_uid' => $this->integer()->notNull(),
            'sm_object_type' => $this->smallinteger()->notNull(),
            'sm_object_id' => $this->integer()->notNull(),
            'sm_expired_at' => $this->integer()->notNull(),
            'sm_created_at' => $this->integer()->notNull()
        ], $tableOptions);
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%sys_msg}}');
        return true;
    }
}
