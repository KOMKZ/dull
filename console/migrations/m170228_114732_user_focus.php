<?php

use yii\db\Migration;

class m170228_114732_user_focus extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $table = '{{%user_focus}}';
        $this->createTable($table, [
            'uf_uid' => $this->integer()->notNull(),
            'uf_f_uid' => $this->integer()->notNull(),
            'uf_created_at' => $this->integer()->notNull()
        ], $tableOptions);
        $this->addPrimaryKey('pk_user_focus', $table, ['uf_uid', 'uf_f_uid']);
        $this->createIndex('idx_uf_uid', $table, 'uf_uid');
        $this->createIndex('idx_uf_f_uid', $table, 'uf_f_uid');

        return true;
    }

    public function down()
    {
        $this->dropTable('{{%user_focus}}');
        return true;
    }
}
