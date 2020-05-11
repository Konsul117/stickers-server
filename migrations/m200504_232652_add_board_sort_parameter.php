<?php

use yii\db\Migration;

/**
 * Class m200504_232652_add_board_sort_parameter
 */
class m200504_232652_add_board_sort_parameter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('board', 'index', \yii\db\Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT \'Индекс сортировки\' AFTER `author_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('board', 'index');
    }
}
