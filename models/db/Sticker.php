<?php

namespace app\models\db;

use app\components\validators\FilterClearTextValidator;
use app\components\validators\IntegerValidator;
use app\components\validators\PositiveIntegerValidator;
use yii\db\ActiveRecord;
use yii\validators\BooleanValidator;
use yii\validators\RequiredValidator;

/**
 * Стикер.
 *
 * @property int    $id     Идентификатор
 * @property int    $index  Индекс сортировки
 * @property string $text   Текст
 * @property int    $is_new Новый стикер
 *
 * @author Казанцев Александр <kazancev.al@dns-shop.ru>
 */
class Sticker extends ActiveRecord {

	const ATTR_ID     = 'id';
	const ATTR_INDEX  = 'index';
	const ATTR_TEXT   = 'text';
	const ATTR_IS_NEW = 'is_new';

	/**
	 * @inheritdoc
	 *
	 * @author Казанцев Александр <kazancev.al@dns-shop.ru>
	 */
	public function rules(): array {
		return [
			[static::ATTR_ID,     PositiveIntegerValidator::class],
			[static::ATTR_INDEX,  RequiredValidator::class],
			[static::ATTR_INDEX,  IntegerValidator::class],
			[static::ATTR_TEXT,   RequiredValidator::class],
			[static::ATTR_TEXT,   FilterClearTextValidator::class],
			[static::ATTR_IS_NEW, RequiredValidator::class],
			[static::ATTR_IS_NEW, BooleanValidator::class],
		];
	}
}