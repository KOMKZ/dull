<?php

use yii\db\Migration;

class m170209_030141_file extends Migration
{
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%file}}', [
            // id
            'f_id' => $this->bigPrimaryKey(),
            // 文件存储名
            'f_name' => $this->string()->notNull(),
            // 文件下载名
            'f_depostion_name' => $this->string(),
            // 文件地址prefix
            'f_prefix' => $this->string(),
            // 文件host
            'f_host' => $this->string()->notNull(),
            // 文件分类
            'f_category' => $this->char(25)->notNull(),
            // file hash
            'f_hash' => $this->char(60),
            // 文件后缀
            'f_ext' => $this->char(10),
            // 文件mime_type
            'f_mime_type' => $this->string()->notNull(),
            // meta_data
            'f_meta_data' => $this->text(),
            // 文件大小
            'f_size' => $this->bigInteger()->notNull(),
            // 文件存储类型
            'f_storage_type' => $this->char(20)->notNull(),
            // 文件访问类型
            'f_acl_type' => $this->char(30)->notNull(),
            // 文件创建时间
            'f_created_at' => $this->integer()->notNull(),
            // 文件更新时间
            'f_updated_at' => $this->integer()->notNull()
        ], $tableOptions);
        // todo 索引的建立
    }

    public function down()
    {
        $this->dropTable("{{%file}}");
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
