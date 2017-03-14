<?php

use yii\db\Migration;

class m170307_020331_region extends Migration
{
    public function up()
    {
        return true;
        $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . '/region.sql';
        $sql = file_get_contents($file);
        $cmd = Yii::$app->db->createCommand($sql);
        $cmd->execute();
        return true;
    }

    public function down()
    {
        return true;
        $this->dropTable('region');
        return true;
    }
}
