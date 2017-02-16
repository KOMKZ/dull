<?php

use yii\db\Migration;

class m170211_072318_user extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'u_id' => $this->primaryKey(),
            'u_username' => $this->string()->notNull()->unique(),
            'u_auth_key' => $this->string(32)->notNull(),
            'u_password_hash' => $this->string()->notNull(),
            'u_password_reset_token' => $this->string()->unique(),
            'u_email' => $this->string()->notNull()->unique(),
            'u_status' => $this->char(12)->notNull(),
            'u_auth_status' => $this->smallinteger()->notNull(),
            'u_created_at' => $this->integer()->notNull(),
            'u_updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
        return true;
    }
}
