<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m200422_000051_add_sticker_author
 */
class m200422_000051_add_sticker_author extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('sticker', 'author_id', Schema::TYPE_INTEGER . ' NOT NULL COMMENT "Автор"');
        $this->createIndex('ix-sticker[author_id]', 'sticker', ['author_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('sticker', 'author_id');
    }
}