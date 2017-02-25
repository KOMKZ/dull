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
        $table = '{{%user_msg}}';
        $this->createTable($table, [
            'um_id' => $this->primaryKey(),
            'um_uid' => $this->integer()->notNull(),
            'um_mid' => $this->integer()->notNull()->defaultValue(0),
            'um_read_status' => $this->smallinteger()->notNull()->defaultValue(0),
            'um_title' => $this->string(255)->notNull(),
            'um_content' => $this->text()->notNull(),
            'um_created_at' => $this->integer()->notNull()
        ], $tableOptions);
        $this->createIndex('idx_um_uid', $table, 'um_uid');
        $this->createIndex('idx_um_mid', $table, 'um_mid');
        $this->createIndex('idx_um_read_status', $table, 'um_read_status');


        return true;
    }

    public function down()
    {
        $this->dropTable('{{%user_msg}}');
        return true;
    }
}
