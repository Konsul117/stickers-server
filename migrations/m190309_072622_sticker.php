<?php

use yii\db\Migration;

/**
 * @inheritdoc
 */
class m190309_072622_sticker extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('sticker', [
			'id'     => $this->primaryKey() . ' COMMENT "Идентификатор"',
			'index'  => 'INT NOT NULL COMMENT "Индекс сортировки"',
			'text'   => 'TEXT NOT NULL COMMENT "Текст"',
			'is_new' => 'TINYINT NOT NULL COMMENT "Новый стикер"',
		], 'COMMENT "Стикер"');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('sticker');
	}
}
