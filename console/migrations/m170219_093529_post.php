<?php

use yii\db\Migration;

class m170219_093529_post extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%post}}', [
            'p_id' => $this->primaryKey(),
            'p_title' => $this->string(50)->notNull(),
            'p_content' => $this->text()->notNull(),
            'p_thumb_img' => $this->text()->null(),
            'p_thumb_img_id' => $this->bigInteger()->null(),
            'p_created_uid' => $this->integer()->notNull(),
            // markdown or html
            'p_content_type' => $this->char(10)->notNull(),
            'p_status' => $this->char(10)->notNull(),
            'p_created_at' => $this->integer()->notNull(),
            'p_updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%post}}');
        return true;
    }
}
