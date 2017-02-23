<?php

use yii\db\Migration;

class m170222_121942_user_msg extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_msg}}', [
            'um_id' => $this->primaryKey(),
            'um_rid' => $this->integer()->notNull(),
            'um_uid' => $this->integer()->notNull(),
            'um_mid' => $this->integer()->notNull(),
            'um_title' => $this->string(255)->notNull(),
            'um_content' => $this->text()->notNull(),
            'um_created_at' => $this->integer()->notNull()
        ], $tableOptions);
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%user_msg}}');
        return true;
    }
}
