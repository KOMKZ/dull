<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

use yii\base\InvalidConfigException;
use yii\db\Migration;
use yii\log\DbTarget;

/**
 * Initializes log table.
 *
 * The indexes declared are not required. They are mainly used to improve the performance
 * of some queries about message levels and categories. Depending on your actual needs, you may
 * want to create additional indexes (e.g. index on `log_time`).
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
 * @since 2.0.1
 */
class m141106_185632_log_init extends Migration
{



    public function up()
    {
        $tables = [
            "{{%log_backend}}",
            "{{%log_frontend}}",
            "{{%log_api}}",
            "{{%log_console}}"
        ];
        foreach ($tables as $table) {

            $tableOptions = null;
            if ($this->db->driverName === 'mysql') {
                // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
                $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            }

            $this->createTable($table, [
                'id' => $this->bigPrimaryKey(),
                'level' => $this->integer(),
                'category' => $this->string(),
                'log_time' => $this->double(),
                'prefix' => $this->text(),
                'message' => $this->text(),
            ], $tableOptions);

            $this->createIndex('idx_log_level', $table, 'level');
            $this->createIndex('idx_log_category', $table, 'category');
        }
        return true;
    }

    public function down()
    {
        $tables = [
            "{{%log_backend}}",
            "{{%log_frontend}}",
            "{{%log_api}}",
            "{{%log_console}}"
        ];
        foreach ($tables as $table) {

            $this->dropTable($table);
        }
        return true;
    }
}
