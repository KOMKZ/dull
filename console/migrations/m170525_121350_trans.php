<?php

use yii\db\Migration;

class m170525_121350_trans extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%trans}}', [
            't_id' => $this->primaryKey(),
            't_title' => $this->string(255)->notNull(),
            't_number' => $this->char(17)->notNull(),
            't_type' => $this->smallInteger(3)->notNull(),
            't_fee_type' => $this->char(10)->notNull(),
            't_fee' => $this->integer()->notNull(),
            't_created_at' => $this->integer()->notNull(),
            't_updated_at' => $this->integer()->notNull(),
            't_invalid_after' => $this->integer()->notNull(),
            't_closed_time' => $this->integer()->null(),
            't_payed_time' => $this->integer()->null(),
            't_pa_uid' => $this->integer()->notNull(),
            't_pb_uid' => $this->integer()->notNull(),
            't_des' => $this->text()->null(),
            't_out_trade_no' => $this->char(20)->notNull(),
            't_out_trade_type' => $this->smallInteger(3)->null(),
            't_app_id' => $this->smallInteger(3)->null(),
            't_status' => $this->smallInteger(3)->notNull(),
            't_error_status' => $this->smallInteger(3)->notNull(),
            't_pay_status' => $this->smallInteger(3)->notNull(),
            't_succ_pay_type' => $this->char(8)->null()
        ], $tableOptions);
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%trans}}');
        return true;
    }
}
