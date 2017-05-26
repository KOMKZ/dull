<?php

use yii\db\Migration;

class m170526_063149_pay_order extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%pay_order}}', [
            'po_id' => $this->primaryKey(),
            'po_tid' => $this->integer()->notNull(),
            'po_type' => $this->char(10)->null(),
            'po_pay_status' => $this->smallinteger(3)->notNull(),
            'po_error_status' => $this->smallinteger(3)->notNull(),
            'po_third_data' => $this->text()->null(),
            'po_info_type' => $this->smallinteger()->notNull(),
            'po_invalid_after' => $this->integer()->notNull(),
            'po_created_at' => $this->integer()->notNull(),
            'po_updated_at' => $this->integer()->notNull()
        ], $tableOptions);
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%pay_order}}');
        return true;
    }
}
