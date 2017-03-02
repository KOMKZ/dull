<?php

use yii\db\Migration;

class m170302_060448_setting extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $table = '{{%setting}}';
        $this->createTable($table, [
            'set_id' => $this->primaryKey(),
            'set_name' => $this->string(50)->notNull(),
            'set_value' => $this->text()->null(),
            'set_value_type' => $this->smallInteger()->notNull(),
            'set_des' => $this->string(255)->notNull(),
            'set_module' => $this->smallInteger()->notNull()->defaultValue(0),
            'set_parent_id' => $this->integer()->notNull()->defaultValue(0),
            'set_validators' => $this->text()->null(),
            'set_validators_params' => $this->text()->null(),
            'set_widget' => $this->smallInteger()->notNull(),
            'set_widget_params' => $this->text()->null(),
            'set_active' => $this->smallInteger()->notNull()->defaultValue(1),
            'set_created_at' => $this->integer()->notNull()
        ], $tableOptions);
        $this->createIndex('idx_set_name', $table, 'set_name');
        $comments = [
            'set_id' => '主键id',
            'set_name' => '设置项的名称',
            'set_value' => '设置项值',
            'set_value_type' => '设置项的类型',
            'set_des' => '设置项的描述',
            'set_module' => '设置项所属模块',
            'set_parent_id' => '父类设置项的id',
            'set_validators' => '设置项验证器',
            'set_validators_params' => '设置项验证器参数',
            'set_widget' => '设置项组件',
            'set_widget_params' => '设置项组件参数',
            'set_active' => '设置项是否可用',
            'set_created_at' => '创建时间',
        ];
        foreach($comments as $column => $comment){
            $this->addCommentOnColumn($table, $column, $comment);
        }
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%setting}}');
        return true;
    }
}
