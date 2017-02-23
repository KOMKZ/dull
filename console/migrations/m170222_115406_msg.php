<?php

use yii\db\Migration;

class m170222_115406_msg extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%msg}}', [
            'm_id' => $this->primaryKey(),
            'm_title' => $this->string()->notNull(),
            'm_content' => $this->string(255)->notNull(),
            'm_params_map' => $this->text()->null(),
            'm_created_at' => $this->integer()->notNull()
        ], $tableOptions);
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%msg}}');
        return true;
    }
}
