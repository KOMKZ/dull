<?php

use yii\db\Migration;

class m170707_072806_lic_image extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%lic_image}}', [
            'lici_id' => $this->primaryKey(), //主键
            'lici_data' => $this->text()->notNull(), // 轮播内容到数据
            'lici_mime_type' => $this->smallinteger()->notNull(), // 轮播内容的类型，图片，视频
            'lici_data_type' => $this->smallinteger()->notNull(), // 轮播内容的数据类型，url，queryid，二进制数据
            'lici_sort_value' => $this->smallinteger()->notNull()->defaultValue(0), // 排序值
            'lici_visable' => $this->smallinteger()->notNull()->defaultValue(1), // 是否可以显示
            'lici_created_time' => $this->integer()->notNull() //创建时间
        ], $tableOptions);
        // todo 建立索引
        return true;
    }

    public function down()
    {
        $this->dropTable('{{%lic_image}}');
        return true;
    }
}
