<?php

use yii\db\Migration;

class m170216_074108_user_identity extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_identity}}', [
            'ui_id' => $this->primaryKey(),
            // 用户id
            'ui_uid' => $this->integer()->notNull(),
            // 用户组
            'ui_g_name' => $this->string()->notNull()
        ], $tableOptions);
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%user_identity}}');
        return true;
    }
}
