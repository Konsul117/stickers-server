<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m200426_003136_add_groups
 */
class m200426_003136_add_groups extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('board', [
            'id'        => $this->primaryKey(),
            'title'     => Schema::TYPE_STRING . '(50) NOT NULL COMMENT "Название"',
            'author_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Автор"',
        ], 'COMMENT "Доска"');

        $this->addColumn('sticker', 'board_id', \yii\db\Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Доска"');
        $this->createIndex('ix-sticker[board_id]', 'sticker', ['board_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('sticker', 'board_id');
        $this->dropTable('board');
    }
}
