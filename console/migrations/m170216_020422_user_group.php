<?php

use yii\db\Migration;

class m170216_020422_user_group extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_group}}', [
            'ug_id' => $this->primaryKey(),
            'ug_name' => $this->string()->notNull()->unique(),
            'ug_description' => $this->string(100)->notNull(),
            'ug_created_at' => $this->integer()->notNull(),
            'ug_updated_at' => $this->integer()->notNull()
        ], $tableOptions);
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%user_group}}');
        return true;
    }
}
