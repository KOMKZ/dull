<?php

use yii\db\Migration;

class m170314_075037_valid_file_id extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%valid_file_id}}', [
            'vfi_id' => $this->bigPrimaryKey(),
            'vfi_fid' => $this->integer()
        ], $tableOptions);
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%valid_file_id}}');
        return true;
    }

}
